<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CapsuleRename extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:capsule:rename {currentName} {newName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename a Twill Capsule';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $currentName;

    /**
     * @var string
     */
    protected $newName;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->currentName = Str::studly($this->argument('currentName'));
        $this->newName = Str::studly($this->argument('newName'));

        $this->capsule = capsules($this->currentName);

        if (!$this->capsule) {
            $this->error('Capsule not found');

            return Command::FAILURE;
        }

        $this->replaceFile('Controller', 'controllers_dir');
        $this->replaceFile('Request', 'requests_dir');
        $this->replaceFile('', 'models_dir');
        $this->replaceFile('Revision', 'models_dir');
        $this->replaceFile('Translation', 'models_dir');
        $this->replaceFile('Slug', 'models_dir');
        $this->replaceFile('Repository', 'repositories_dir');

        $this->replaceRoutes();

        $this->replaceSeeder();

        $this->generateMigration();

        $this->renameCapsuleDirectory();

        return Command::SUCCESS;
    }

    private function replaceFile($type, $directory)
    {
        $newFile = join([
            $this->capsule[$directory],
            '/',
            Str::singular($this->newName),
            $type,
            '.php',
        ]);

        $this->filesystem->move(
            join([
                $this->capsule[$directory],
                '/',
                Str::singular($this->currentName),
                $type,
                '.php',
            ]),
            $newFile
        );

        $this->replaceInFile(
            $this->currentName,
            $this->newName,
            $newFile
        );

        $this->replaceInFile(
            Str::singular($this->currentName),
            Str::singular($this->newName),
            $newFile
        );

        $this->replaceInFile(
            lcfirst($this->currentName),
            lcfirst($this->newName),
            $newFile
        );

        $this->replaceInFile(
            strtolower(Str::snake(Str::singular($this->currentName))),
            strtolower(Str::snake(Str::singular($this->newName))),
            $newFile
        );
    }

    private function replaceRoutes()
    {
        $this->replaceInFile(
            lcfirst($this->currentName),
            lcfirst($this->newName),
            $this->capsule['routes_file']
        );
    }

    private function replaceSeeder()
    {
        $this->replaceInFile(
            $this->currentName,
            $this->newName,
            $this->capsule['seeds_psr4_path'] . '/DatabaseSeeder.php'
        );
    }

    private function generateMigration()
    {
        $table = strtolower(Str::snake($this->currentName));
        $newTable = strtolower(Str::snake($this->newName));

        $migrationName = 'rename_' . $table . '_tables';

        $migrationPath = $this->capsule['migrations_dir'];

        $fullPath = $this->laravel['migration.creator']->create($migrationName, $migrationPath);

        $stub = str_replace(
            [
                '{{table}}',
                '{{newTable}}',
                '{{singularTableName}}',
                '{{newSingularTableName}}',
                '{{tableClassName}}',
            ],
            [
                $table,
                $newTable,
                Str::singular($table),
                Str::singular($newTable),
                $this->currentName,
            ],
            $this->filesystem->get(__DIR__ . '/stubs/migration_rename.stub')
        );

        $this->filesystem->put($fullPath, $stub);
    }

    private function renameCapsuleDirectory()
    {
        $newCapsulePath = $this->capsule['base_path'] . '/' . $this->newName;

        $this->filesystem->move($this->capsule['root_path'], $newCapsulePath);
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  array|string  $search
     * @param  array|string  $replace
     * @param  string  $path
     * @return void
     */
    private function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}
