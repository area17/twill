<?php

namespace A17\Twill\Commands;

use PhpCsFixer\Console\Application;
use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class BlockMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:make:block {name} {type} {icon}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new block ';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Filesystem $files
     * @param Composer $composer
     * @param Config $config
     */
    public function __construct(Filesystem $files, Composer $composer, Config $config)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
        $this->config = $config;
    }

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $type = $this->argument('type');
        $icon = $this->argument('icon');

        if (blank($icon = $this->getIconFile($this->argument('icon'))))
        {
            return $this->error("Icon '{$icon}' doesn't exists.");
        }

        if (blank($blockStub = $this->getBlockStub($this->argument('type'))))
        {
            return $this->error("Block '{$type}' doesn't exists.");
        }

        if (blank($blockFile = $this->getBlockFile($this->argument('name')))) {
            return $this->error('Aborted.');
        }

        // $this->files->copy($blockStub, $blockFile);

        $twill = config('twill');

        dd($this->printArray($twill));
    }

    public function getBlockFile($name)
    {
        $blockFile = resource_path("views/admin/blocks/{$name}.blade.php");

        if ($this->files->exists($blockFile)) {
            $answer = $this->ask("Local block file ({$blockFile}) exists. Replace it? (yes/no)");

            if (Str::lower($answer) !== 'yes') {
                return;
            }
        }

        return $blockFile;
    }

    public function getBlockStub($block)
    {
        $block .= '.blade.php';

        return collect($this->files->files(__DIR__ . '/stubs/blocks'))->reduce(function ($keep, $file) use ($block) {
            if ($keep) {
                return $keep;
            }

            return $file->getFilename() === $block ? $file->getPathName() : null;
        }, null);
    }

    public function getIconFile($icon)
    {
        $icon .= '.svg';

        return collect($this->files->files(__DIR__ . '/../../frontend/icons'))->reduce(function ($keep, $file) use ($icon) {
            if ($keep) {
                return $keep;
            }

            return $file->getFilename() === $icon ? $file->getPathName() : null;
        }, null);
    }

    public function printArray($array)
    {
        $array = var_export($array, true);

        $array = "<?php return $array;";

        $array = str_replace(",\n", ",\n\n", $array);

        file_put_contents($file = 'test.file.php', $array);

        shell_exec(base_path("vendor/bin/php-cs-fixer fix {$file} --rules=@Symfony"));
    }
}
