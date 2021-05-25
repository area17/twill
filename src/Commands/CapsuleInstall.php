<?php

namespace A17\Twill\Commands;

use Exception;
use GuzzleHttp\Client;
use A17\Twill\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use A17\Twill\Services\Capsules\Manager;

class CapsuleInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:capsule:install 
                               {capsule : Capsule name (posts) in plural, Github repository (area17/capsule-posts) or full URL of the Capsule git repository}
                               {--require : Require as a Composer package. Can receive maitainer updates.} 
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

    protected $capsules = [];

    protected $resolved = [];

    /**
     * Create a new console command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->manager = new Manager();
    }

    /**
     * @return string
     */
    private function getUnzippedPath($capsule): string
    {
        return $capsule['capsule']['base_path'] .
            '/' .
            $capsule['capsule_name'] .
            '-' .
            $this->getBranch();
    }

    /**
     * Create super admin account.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->checkParameters()) {
            return 255;
        }

        $this->applicationBanner();

        $this->loadBaseCapsule();

        $this->resolveDependencies();

        if (!$this->canInstall()) {
            return 1;
        }

        $installed = $this->capsules->where('can_install', true)->reduce(function ($installed, $capsule) {
            $this->displayConfigurationSummary($capsule);

            return $installed && $this->installCapsule($capsule);
        }, true);

        if (!$installed) {
            $this->info('A fatal error occurred during installation. Aborted.');

            return 1;
        }

        return 0;
    }

    protected function checkParameters()
    {
        if (!$this->option('require') && !$this->option('copy')) {
            $this->error('Missing mandatory strategy: --require or --copy.');

            return false;
        }

        if ($this->option('require')) {
            $this->error('Require strategy not implemented yet.');

            return false;
        }

        return true;
    }

    protected function loadBaseCapsule()
    {
        $capsule = [
            'module' => $this->argument('capsule'),
            'branch' => $this->option('branch'),
            'service' => $this->option('service'),
            'prefix' => $this->option('prefix'),
        ];

        $this->loadCapsule($capsule);
    }

    public function generateCapsuleData($capsule)
    {
        $module = $this->extractModuleName($capsule['module']);

        if ($this->isFullUrl($module)) {
            $url = $capsule['module'];

            $repository = $this->extractRepositoryFromUrl($capsule['module']);
        } else {
            $repository = Str::snake(Str::kebab($capsule['module']));

            if (!Str::contains($repository, '/')) {
                $repository = $this->getAREA17RepositoryPrefix() . "-$repository";
            }

            $url = $this->getRepositoryUrlPrefix() . "/$repository";

            $rawUrl = $this->getServiceRawUrlPrefix($capsule) . "/$repository";
        }

        $capsule['module'] = $module;

        $capsule['repository_uri'] = $repository;

        $capsule['capsule_name'] = Str::afterLast($repository, '/');

        $capsule['repository_url'] = $url;

        $capsule['repository_raw_url'] = $rawUrl;

        $capsule['name'] = $this->makeCapsuleName($repository);

        $capsule['namespace'] = Str::studly($module);

        $capsule['capsule'] = $this->manager->makeCapsule([
            'name' => $module,
            'enabled' => true,
        ]);

        $capsule['config_url'] = $this->makeConfigFile($capsule);

        $capsule['config'] = $this->downloadConfig($capsule);

        $capsule['zip_address'] = $this->getZipAddress($capsule);

        $capsule['temp_file'] = $this->getTempFileName($capsule);

        return $capsule;
    }

    protected function isFullUrl($capsule)
    {
        return false;
    }

    protected function getRepositoryUrlPrefix()
    {
        return 'https://' . $this->getService();
    }

    protected function getAREA17RepositoryPrefix()
    {
        $prefix = 'area17';

        if (filled($capsule = $this->getCapsulePrefix())) {
            $prefix .= '/' . $this->getCapsulePrefix();
        }

        return $prefix;
    }

    protected function getBranch()
    {
        return $this->option('branch');
    }

    protected function getZipAddress($capsule)
    {
        return sprintf(
            '%s/archive/refs/heads/%s.zip',
            $capsule['repository_url'],
            $capsule['branch']
        );
    }

    protected function makeCapsuleName($capsule)
    {
        $capsule = Str::afterLast($capsule, '/');

        return Str::after($capsule, $this->getCapsulePrefix() . '-');
    }

    protected function getCapsulePrefix()
    {
        return $this->option('prefix');
    }

    protected function getServiceUrlPrefix()
    {
        return $this->option('prefix');
    }

    protected function getServiceRawUrlPrefix($capsule)
    {
        if ($capsule['service'] === 'github.com') {
            return 'https://raw.githubusercontent.com';
        }

        return null;
    }

    public function getService()
    {
        return $this->option('service');
    }

    protected function displayConfigurationSummary($capsule)
    {
        $this->info('');
        $this->info("Installing Capsule {$capsule['capsule']['name']}...");
        $this->info('');
        $this->info('Configuration summary');
        $this->info('----------------------------------------------------------------------------------');

        $this->info("Name prefix: {$capsule['prefix']}");

        $this->info("Capsule repository URI: {$capsule['repository_uri']}");

        $this->info("Capsule name: {$capsule['name']}");

        $this->info("Name: {$capsule['capsule']['name']}");

        $this->info("Module: {$capsule['module']}");

        $this->info("Namespace: {$capsule['namespace']}");

        $this->info("Service: {$capsule['service']}");

        $this->info("Branch: {$capsule['branch']}");

        $this->info("Repository URL: {$capsule['repository_url']}");

        $this->info("Config URL: {$capsule['config_url']}");

        $this->info("Zip URL: {$capsule['zip_address']}");

        $this->info("Temporary file: {$capsule['temp_file']}");
    }

    protected function getModule()
    {
        return Str::camel($this->name);
    }

    protected function canInstallCapsule($capsule)
    {
        if ($this->manager->capsuleExists($capsule['capsule']['module'])) {
            $this->error('A capsule with this name already exists!');

            return false;
        }

        if (file_exists($capsule['capsule']['root_path'])) {
            $this->error(
                'Capsule directory already exists: ' .
                $capsule['capsule']['root_path']
            );

            return false;
        }

        return true;
    }

    protected function installCapsule($capsule)
    {
        if (!$this->ensureBaseDirectoryExists($capsule)) {
            return false;
         }

        $installed =
            $this->canInstallCapsule($capsule) &&
            $this->download($capsule) &&
            $this->uncompress(
                $capsule,
                $capsule['temp_file'],
                $capsule['capsule']['base_path']
            ) &&
            $this->renameToCapsule($capsule);

        $this->comment('');

        if (!$installed) {
            $this->error("{$capsule['capsule']['name']} was not installed.");
        } else {
            $this->comment("{$capsule['capsule']['name']} was installed successfully!");
        }

        $this->cleanTempFile($capsule);

        return $installed;
    }

    protected function download($capsule)
    {
        if (!$this->repositoryExists($capsule)) {
            return false;
        }

        $this->info('Downloading zip file...');

        file_put_contents(
            $capsule['temp_file'],
            fopen($capsule['zip_address'], 'r')
        );

        return true;
    }

    protected function cleanTempFile($capsule)
    {
        if (file_exists($capsule['temp_file'])) {
            unlink($capsule['temp_file']);

            if (file_exists($capsule['temp_file'])) {
                $this->error(
                    'Unable to remove temporary file: ' .
                        $capsule['temp_file']
                );

                return false;
            }
        }

        return true;
    }

    protected function getTempFileName($capsule)
    {
        return $capsule['capsule']['base_path'] . "/{$capsule['capsule']['module']}-install.tmp.zip";
    }

    protected function repositoryExists($capsule)
    {
        $guzzle = new Client();

        try {
            $statusCode = $guzzle
                ->request('GET', $capsule['repository_url'])
                ->getStatusCode();
        } catch (Exception $exception) {
            $statusCode = $exception->getCode();
        }

        if ($statusCode !== 200) {
            $this->error('Repository not found: ' . $capsule['repository_url']);

            return false;
        }

        return true;
    }

    protected function uncompress($capsule, $zip, $directory)
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

    protected function unzipShellCommandExists()
    {
        $return = shell_exec('which unzip');

        return !empty($return);
    }

    protected function unzipWithExtension($zip, $directory)
    {
        $this->info('Unzipping with PHP zip extension...');

        $unzip = new \ZipArchive();

        $success = $unzip->open($zip) && $unzip->extractTo("$directory/");

        try {
            $unzip->close();
        } catch (Exception $exception) {
            //
        }

        unlink($zip);

        if (!$success) {
            $this->error("Cound not read zip file: $zip");

            return false;
        }

        return true;
    }

    protected function unzipWithShell($capsule, $zip, $directory)
    {
        $this->info('Unzipping with unzip shell command...');

        chdir($capsule['capsule']['base_path']);

        shell_exec('unzip install.zip');

        return file_exists($this->getUnzippedPath($capsule));
    }

    public function renameToCapsule($capsule)
    {
        $destination = $capsule['capsule']['psr4_path'];

        rename($this->getUnzippedPath($capsule), $destination);

        return file_exists($destination);
    }

    public function extractModuleName($name)
    {
        $name = Str::afterLast($name, '/');

        $name = Str::afterLast($name, 'twill-capsule-');

        return Str::studly($name);
    }

    public function makeConfigFile($capsule)
    {
        return "{$capsule['repository_raw_url']}/{$capsule['branch']}/twill.json";
    }

    public function downloadConfig($capsule)
    {
        $this->info("Reading config file for \"{$capsule['name']}\"...");

        $contents = @file_get_contents($capsule['config_url']);

        if (blank($contents))
        {
            $this->error("Config file not found: {$capsule['config_url']}");
        }

        return json_decode($contents ?? '[]', true) ?? [];
    }

    public function loadCapsule($capsule)
    {
        $capsule = $this->generateCapsuleData($capsule);

        $key = $this->makeCapsuleKey($capsule);

        if (filled($this->capsules[$key] ?? null)) {
            return;
        }

        $this->capsules[$key] = $capsule;
    }

    public function resolveDependencies()
    {
        $this->info('Resolving dependencies...');

        foreach ($this->capsules as $capsule) {
            if (!($this->resolved[$this->makeCapsuleKey($capsule)] ?? false)) {
                $this->resolveDependenciesForCapsule($capsule);

                $this->resolveDependencies();
            }
        }
    }

    public function makeCapsuleKey($capsule)
    {
        return $capsule['capsule']['root_path'];
    }

    public function resolveDependenciesForCapsule($capsule)
    {
        $this->resolved[$this->makeCapsuleKey($capsule)] = true;

        foreach ($capsule['config']['dependencies']['capsules'] ?? [] as $dependency)
        {
            $capsule = [
                'module' => $dependency['capsule'],
                'branch' => $dependency['branch'] ?? 'stable',
                'service' => $dependency['service'] ?? 'github.com',
                'prefix' => $dependency['prefix'] ?? '',
            ];

            $this->loadCapsule($capsule);
        }
    }

    public function canInstall()
    {
        $this->capsules = collect($this->capsules)->map(function ($capsule) {
            $capsule['can_install'] = $this->canInstallCapsule($capsule);

            if ($capsule['can_install']) {
                $this->comment("Will install Capsule \"{$capsule['capsule']['name']}\".");
            }

            return $capsule;
        });

        $first = $this->capsules->first();

        if (!$first['can_install']) {
            $this->error('Main Capsule is already installed, aborted.');

            return false;
        }

        return true;
    }

    public function manager()
    {
        return app('twill.capsules.manager');
    }

    public function ensureBaseDirectoryExists($capsule)
    {
        $path = $capsule['capsule']['base_path'];

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        if (!file_exists($path))
        {
            $this->error("Unable to create Capsules directory: $path");

            return false;
        }

        return true;
    }

    private function applicationBanner(): void
    {
        $this->info('Twill Capsule installer');
        $this->info('----------------------------------------------------------------------------------');
    }
}
