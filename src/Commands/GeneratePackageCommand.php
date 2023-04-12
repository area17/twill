<?php

namespace A17\Twill\Commands;

use A17\Twill\Commands\Traits\HandlesStubs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GeneratePackageCommand extends Command
{
    use HandlesStubs;

    protected $signature = 'twill:make:package';

    protected $description = 'Make a new twill package';

    /**
     * @var string
     */
    private $packageName;

    /**
     * @var string
     */
    private $packageVendor;

    /**
     * @var string
     */
    private $homepage;

    /**
     * @var string
     */
    private $licence;

    /**
     * @var string
     */
    private $psr4Base;

    /**
     * @var string
     */
    private $targetDirectory;

    public function handle(): void
    {
        $this->packageName = $this->ask("What's the package name", 'twill-extension');
        $this->packageVendor = $this->ask("What's the package vendor", 'area17');
        $this->homepage = $this->ask(
            'The package homepage',
            'https://github.com/' . $this->packageVendor . '/' . $this->packageName
        );
        $this->licence = $this->askWithCompletion('What is the licence', ['MIT', 'Apache-2.0'], 'MIT');
        $this->psr4Base = $this->ask('The psr4 base name', Str::studly($this->packageName));
        $this->targetDirectory = $this->ask(
            'Where should we put the package',
            base_path('packages') . '/' . $this->packageName
        );

        $this->generatePackage();
    }

    protected function generatePackage(): void
    {
        File::ensureDirectoryExists($this->targetDirectory);

        $this->writeComposerJson();
        $this->writeServiceProvider();
        $this->displayMessage();
    }

    protected function writeComposerJson(): void
    {
        $stub = file_get_contents(__DIR__ . '/stubs/package/package-composer-json.stub');

        $stub = $this->replaceVariables([
            'name' => $this->packageVendor . '/' . $this->packageName,
            'homepage' => $this->homepage,
            'licence' => $this->licence,
            'namespace' => $this->psr4Base,
            'providerName' => $this->getProviderName(),
        ], $stub);

        file_put_contents($this->targetDirectory . '/composer.json', $stub);
    }

    protected function writeServiceProvider(): void
    {
        File::ensureDirectoryExists($this->targetDirectory . '/src');

        $stub = file_get_contents(__DIR__ . '/stubs/package/package-service-provider.stub');
        $providerName = $this->getProviderName();

        $stub = $this->replaceVariables([
            'namespace' => $this->psr4Base,
            'providerName' => $providerName,
        ], $stub);

        file_put_contents($this->targetDirectory . "/src/{$providerName}ServiceProvider.php", $stub);
    }

    protected function displayMessage(): void
    {
        $stub = file_get_contents(__DIR__ . '/stubs/package/instructions.stub');

        $path = $this->targetDirectory;

        if (Str::startsWith($path, base_path())) {
            $path = '.' . Str::replaceFirst(base_path(), '', $path);
        }

        $stub = $this->replaceVariables([
            'path' => $path,
            'vendor' => $this->packageVendor,
            'name' => $this->packageName,
            'namespace' => $this->getProviderName(),
        ], $stub);

        $this->line($stub);
    }

    protected function getProviderName()
    {
        return str_after_last($this->psr4Base, '\\');
    }
}
