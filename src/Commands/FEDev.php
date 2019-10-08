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
        if (!FEInstall::checkNodeModules()) {
            $this->info('Twill frontend dependencies are not installed.');
            $this->info('Install it right now !');
            $this->call('twill:fe-install');
        }

        config(['twill.fe_prod' => false]);
        $this->npmDev();
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
     * Start vue-cli-serve command
     *
     * @return void
     */
    private function npmDev()
    {
        $process = new Process(['npm', 'run', 'serve', ''], base_path(config('twill.vendor_path')));
        $process->setTty(true);
        $process->setTimeout(3600);
        $process->mustRun();
    }
}
