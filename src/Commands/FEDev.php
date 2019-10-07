<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class FEDev extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:fe-dev {--config=twill.config.js}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Build Twill assets (experimental)";

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /*
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $this->npmDev();

//        dd($this->laravel->publicPath());

        chdir($this->laravel->publicPath());

        $config_path = $this->option('config');

        list($host, $port) = $this->getHostAndPort($config_path);
        $this->info("Backend started on http://{$host}:{$port}/ as proxy");
        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));
        $base = ProcessUtils::escapeArgument($this->laravel->basePath());
//        dd($binary, $base);
        passthru("WEBPACK_DEV=running {$binary} -S {$host}:{$port} {$base}/server.php");
    }

    protected function getHostAndPort(string $config_path): array
    {
        $config = $this->getConfig($config_path);
        $proxies = Arr::get($config, 'devServer.proxy');
        $first_proxy = Arr::first(array_values($proxies));
        $host = parse_url($first_proxy, PHP_URL_HOST);
        $port = parse_url($first_proxy, PHP_URL_PORT);
        return [$host, $port];
    }

    protected function getConfig(string $config_path): array
    {
        static $config;
        if (is_null($config)) {
            if (strpos($config_path, '/') !== 0) {
                $base = $this->laravel->basePath();
                $config_path = "/$base/$config_path";
            }
            if (file_exists($config_path)) {
                // Run through node to "compile" the config
                exec("node -e 'console.log(JSON.stringify(require(\"$config_path\")))'", $json);
                $config = json_decode($json[0], true);
            } else {
                $config_path = "/$base/vendor/area17/twill/vue.config.js";
                if (file_exists($config_path)) {
                    exec("PHP_ENV=1 node -e 'console.log(JSON.stringify(require(\"$config_path\")))'", $json);
                    $config = json_decode($json[0], true);
                }
            }
        }
        return $config;
    }

    /**
     * @return void
     */
    private function npmInstall()
    {
        $npmInstallProcess = new Process(['npm', 'ci'], base_path('vendor/area17/twill'));
        $npmInstallProcess->setTty(true);
        $npmInstallProcess->mustRun();
    }

    /**
     * Start vue-cli-serve command
     *
     * @return void
     */
    private function npmDev()
    {
        $npmBuildProcess = new Process(['npm', 'run', 'serve'], base_path('vendor/area17/twill'));
        $npmBuildProcess->setTty(true);
        $npmBuildProcess->mustRun();
    }

    /**
     * @return void
     */
    private function copyBlocks()
    {
        $localCustomBlocksPath = resource_path('assets/js/blocks');
        $twillCustomBlocksPath = base_path('vendor/area17/twill/frontend/js/components/blocks/customs');

        if (!$this->filesystem->exists($twillCustomBlocksPath)) {
            $this->filesystem->makeDirectory($twillCustomBlocksPath);
        }

        $this->filesystem->cleanDirectory($twillCustomBlocksPath);

        if (!$this->filesystem->exists($localCustomBlocksPath)) {
            $this->filesystem->makeDirectory($localCustomBlocksPath);
        }

        $this->filesystem->copyDirectory($localCustomBlocksPath, $twillCustomBlocksPath);
    }
}
