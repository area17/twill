<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class SyncLang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:lang {--toCsv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Sync the translations";

    protected string $langDirPath = __DIR__ . '/../../lang';

    protected string $csvPath = __DIR__ . '/../../lang/lang.csv';

    protected string $langStubPath = __DIR__ . '/stubs/lang.stub';

    /**
     * @param ValidatorFactory $validatorFactory
     * @param Config $config
     */
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    /**
     * Create super admin account.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('toCsv')) {
            return $this->toCsv();
        }

        $this->info('Converting csv to lang files...');
        if ($this->confirm('This operation will overwrite all your changes made on lang file, are you sure?')) {
            $this->cleanup();
            $this->info('Generating the new lang directories...');
            $this->generateLangFiles();
            $this->info('Done!');
        }
    }

    // BETA METHOD, NOT STABLE
    private function toCsv(): void
    {
        if ($this->confirm('This operation will overwrite the lang.csv file, are you sure?')) {
            $csvArray = $this->getArrayFromCsv();
            foreach ($this->files->directories($this->langDirPath) as $directory) {
                $code = $this->files->basename($directory);
                $columnIndex = $this->getIndexByCode($code);
                $translations = $this->files->getRequire($directory . '/lang.php');
                foreach (Arr::dot($translations) as $key => $translation) {
                    $rowIndex = array_search($key, Arr::pluck(array_slice($csvArray, 2), 0));
                    if ($rowIndex === FALSE) {
                        $newRow = array_fill(0, count($csvArray[0]), "");
                        $newRow[0] = $key;
                        $newRow[$columnIndex] = $translation;
                        $csvArray[] = $newRow;
                    } else {
                        $csvArray[$rowIndex + 2][$columnIndex] = $translation;
                    }
                }
            }

            $fp = fopen($this->csvPath, 'w');

            foreach ($csvArray as $row) {
                fputcsv($fp, $row);
            }

            fclose($fp);
        }
    }

    /**
     * @return mixed[][]
     */
    private function getArrayFromCsv(): array
    {
        return array_map('str_getcsv', file($this->csvPath));
    }

    private function getLangCodesFromCsv()
    {
        $codes = $this->getArrayFromCsv()[1];

        // Remove the first element: row name
        array_shift($codes);

        return $codes;
    }

    private function generateLangFiles(): void
    {
        foreach ($this->getLangCodesFromCsv() as $code) {
            $langDirPath = $this->langDirPath . '/' . $code;
            $this->files->makeDirectory($langDirPath);
            $this->files->put(
                $this->langDirPath . '/' . $code . '/lang.php',
                // increment the index by one to match its actual index in csv
                $this->generateLangFileContent($code)
            );
        }
    }

    private function generateLangFileContent($code): string
    {
        $content = [];
        $index = $this->getIndexByCode($code);

        foreach (array_slice($this->getArrayFromCsv(), 2) as $row) {
            $key = $row[0];
            $translation = $row[$index];
            if (!empty($translation)) {
                Arr::set($content, $key, $translation);
            }
        }

        return str_replace(
            ['{{content}}'],
            [$this->convertArrayToString($content)],
            $this->files->get($this->langStubPath)
        );
    }

    private function getIndexByCode($code)
    {
        return array_search($code, $this->getArrayFromCsv()[1]);
    }

    private function convertArrayToString($array): string
    {
        $export = var_export($array, true);
        $export = preg_replace("#^([ ]*)(.*)#m", '$1$1$2', $export);

        $array = preg_split("/\r\n|\n|\r/", $export);
        $array = preg_replace(["/\s*array\s\($/", "/\)(,)?$/", "/\s=>\s$/"], [null, ']$1', ' => ['], $array);

        return implode(PHP_EOL, array_filter(["["] + $array));
    }

    private function cleanup(): void
    {
        $this->info('Cleaning up the lang directories...');
        $this->files->deleteDirectories($this->langDirPath);
    }
}
