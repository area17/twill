<?php

namespace A17\Twill\Commands;

use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\PhpExecutableFinder;

class ServeDocsCommand extends ServeCommand
{
    protected $signature = 'twill:staticdocs:serve {--host=127.0.0.1} {--port=} {--tries=} {--no-reload}';

    protected $description = 'Serve the static documentation';

    public function handle()
    {
        chdir(__DIR__ . '/../../docs/_build');

        $process = $this->startProcess(false);

        while ($process->isRunning()) {
            try {
                Artisan::call('twill:staticdocs:generate', ['--updatesOnly' => ''], $this->output);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                $this->error('sleeping 5 seconds');
                usleep(5000 * 1000);
            }
            usleep(1000 * 1000);
        }

        $status = $process->getExitCode();

        if ($status && $this->canTryAnotherPort()) {
            ++$this->portOffset;

            return $this->handle();
        }

        return $status;
    }

    /**
     * Get the full server command.
     *
     * @return array
     */
    protected function serverCommand()
    {
        return [
            (new PhpExecutableFinder())->find(false),
            '-S',
            $this->host() . ':' . $this->port(),
            __DIR__ . '/../../docs/generator/server.php',
        ];
    }
}
