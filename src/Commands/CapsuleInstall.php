<?php

namespace A17\Twill\Commands;

use A17\Twill\Exceptions\NoCapsuleFoundException;
use A17\Twill\Facades\TwillCapsules;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class CapsuleInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:capsule:install
                               {capsule : Capsule name (posts) in plural, Github repository (area17/capsule-posts) or full URL of the Capsule git repository}
                               {--require : Require as a Composer package. Can receive maintainer updates.}
                               {--copy : Copy Capsule code. Cannot receive updates.}
                               {--branch=stable : Repository branch}
                               {--prefix=twill-capsule : Capsule repository name prefix}
                               {--service=github.com : Service URL (defaults to Github)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a Twill Capsule';

    protected $repositoryUri;

    /**
     * @var \A17\Twill\Helpers\Capsule
     */
    protected $capsule;

    protected $capsuleName;

    protected $repositoryUrl;

    protected string $namespace;

    private function getUnzippedPath(): string
    {
        return TwillCapsules::getProjectCapsulesPath() .
            '/' .
            $this->capsuleName .
            '-' .
            $this->getBranch();
    }

    /**
     * Create super admin account.
     */
    public function handle(): int
    {
        if (! $this->checkParameters()) {
            return 255;
        }

        $this->configureInstaller();

        $this->displayConfigurationSummary();

        $this->installCapsule();

        return 0;
    }

    protected function checkParameters(): bool
    {
        if (! $this->option('require') && ! $this->option('copy')) {
            $this->error('Missing mandatory strategy: --require or --copy.');

            return false;
        }

        if ($this->option('require')) {
            $this->error('Require strategy not implemented yet.');

            return false;
        }

        return true;
    }

    protected function configureInstaller(): void
    {
        $capsule = $this->argument('capsule');

        if ($this->isFullUrl($capsule)) {
            $url = $capsule;

            $capsule = $this->extractRepositoryFromUrl($capsule);
        } else {
            $capsule = Str::snake(Str::kebab($capsule));

            if (! Str::contains($capsule, '/')) {
                $capsule = $this->getRepositoryPrefix() . sprintf('-%s', $capsule);
            }

            $url = $this->getRepositoryUrlPrefix() . sprintf('/%s', $capsule);
        }

        $this->repositoryUri = $capsule;

        $this->capsuleName = Str::afterLast($capsule, '/');

        $this->repositoryUrl = $url;

        $this->name = $this->makeCapsuleName($capsule);

        $this->namespace = Str::studly($this->name);

        $this->capsule = TwillCapsules::makeProjectCapsule($this->namespace);
    }

    protected function isFullUrl($capsule): bool
    {
        return false;
    }

    protected function getRepositoryUrlPrefix(): string
    {
        return 'https://' . $this->getService();
    }

    protected function getRepositoryPrefix()
    {
        $prefix = Config::get('twill.capsules.capsule_repository_prefix');

        if (filled($capsule = $this->getCapsulePrefix())) {
            $prefix .= '/' . $this->getCapsulePrefix();
        }

        return $prefix;
    }

    protected function getBranch()
    {
        return $this->option('branch');
    }

    protected function getZipAddress(): string
    {
        return sprintf(
            '%s/archive/refs/heads/%s.zip',
            $this->repositoryUrl,
            $this->getBranch()
        );
    }

    protected function makeCapsuleName($capsule): string
    {
        $capsule = Str::afterLast($capsule, '/');

        return Str::after($capsule, $this->getCapsulePrefix() . '-');
    }

    protected function getCapsulePrefix()
    {
        return $this->option('prefix');
    }

    public function getService()
    {
        return $this->option('service');
    }

    protected function displayConfigurationSummary(): void
    {
        $this->info('Configuration summary');

        $this->info('---------------------');

        $this->info('Name prefix: ' . $this->getCapsulePrefix());

        $this->info(sprintf('Capsule repository URI: %s', $this->repositoryUri));

        $this->info(sprintf('Capsule name: %s', $this->capsuleName));

        $this->info(sprintf('Name: %s', $this->name));

        $this->info('Module: ' . $this->getModule());

        $this->info(sprintf('Namespace: %s', $this->namespace));

        $this->info('Service: ' . $this->getService());

        $this->info('Branch: ' . $this->getBranch());

        $this->info(sprintf('Repository URL: %s', $this->repositoryUrl));

        $this->info('Zip URL: ' . $this->getZipAddress());

        $this->info('Temporary file: ' . $this->getTempFileName());
    }

    protected function getModule(): string
    {
        return Str::camel($this->name);
    }

    protected function canInstallCapsule(): bool
    {
        // We know that we throw an exception if it does not exist so we use that check here.
        try {
            TwillCapsules::getCapsuleForModule($this->getModule());
            $this->error('A capsule with this name already exists!');

            return false;
        } catch (NoCapsuleFoundException) {
        }

        if ($this->directoryExists()) {
            $this->error(
                'Capsule directory already exists: ' .
                    $this->getCapsuleDirectory()
            );

            return false;
        }

        return true;
    }

    protected function installCapsule(): int
    {
        $installed =
            $this->canInstallCapsule() &&
            $this->download() &&
            $this->uncompress(
                $this->getTempFileName(),
                TwillCapsules::getProjectCapsulesPath()
            ) &&
            $this->renameToCapsule();

        $this->comment('');

        if (! $installed) {
            $this->error('Your capsule was not installed.');
        } else {
            $this->comment('Your capsule was installed successfully!');
        }

        return $installed ? 0 : 255;
    }

    protected function getCapsuleDirectory(): string
    {
        return $this->capsule->getPsr4Path();
    }

    protected function directoryExists(): bool
    {
        return file_exists($this->getCapsuleDirectory());
    }

    protected function download(): bool
    {
        if (! $this->cleanTempFile() || ! $this->repositoryExists()) {
            return false;
        }

        $this->info('Downloading zip file...');

        file_put_contents(
            $this->getTempFileName(),
            fopen($this->getZipAddress(), 'r')
        );

        return true;
    }

    protected function cleanTempFile(): bool
    {
        if (file_exists($this->getTempFileName())) {
            unlink($this->getTempFileName());

            if (file_exists($this->getTempFileName())) {
                $this->error(
                    'Unable to remove temporary file: ' .
                        $this->getTempFileName()
                );

                return false;
            }
        }

        return true;
    }

    protected function getTempFileName(): string
    {
        $this->makeDir(TwillCapsules::getProjectCapsulesPath());

        return TwillCapsules::getProjectCapsulesPath() . '/install.zip';
    }

    protected function repositoryExists(): bool
    {
        $guzzle = new Client();

        try {
            $statusCode = $guzzle
                ->request('GET', $this->repositoryUrl)
                ->getStatusCode();
        } catch (Exception $exception) {
            $statusCode = $exception->getCode();
        }

        if ($statusCode !== 200) {
            $this->error('Repository not found: ' . $this->repositoryUrl);

            return false;
        }

        return true;
    }

    protected function uncompress($zip, $directory): bool
    {
        $this->info('Unzipping file...');

        if (extension_loaded('zip')) {
            return $this->unzipWithExtension($zip, $directory);
        }

        if ($this->unzipShellCommandExists()) {
            return $this->unzipWithShell($zip, $directory);
        }

        $this->error(
            'Zip extension not installed and unzip command not found.'
        );

        return false;
    }

    protected function unzipShellCommandExists(): bool
    {
        $return = shell_exec('which unzip');

        return ! empty($return);
    }

    protected function unzipWithExtension($zip, $directory): bool
    {
        $this->info('Unzipping with PHP zip extension...');

        $unzip = new \ZipArchive();

        $success = $unzip->open($zip) && $unzip->extractTo(sprintf('%s/', $directory));

        try {
            $unzip->close();
        } catch (Exception) {
            //
        }

        unlink($zip);

        if (! $success) {
            $this->error(sprintf('Cound not read zip file: %s', $zip));

            return false;
        }

        return true;
    }

    protected function unzipWithShell($zip, $directory): bool
    {
        $this->info('Unzipping with unzip shell command...');

        chdir($this->capsule['base_path']);

        shell_exec('unzip install.zip');

        return file_exists($this->getUnzippedPath());
    }

    public function renameToCapsule(): bool
    {
        $destination = $this->capsule->getPsr4Path();

        rename($this->getUnzippedPath(), $destination);

        return file_exists($destination);
    }

    public function makeDir($path): void
    {
        if (! file_exists($path)) {
            mkdir($path, 0775, true);
        }
    }
}
