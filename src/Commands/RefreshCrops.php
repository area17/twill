<?php

namespace A17\Twill\Commands;

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use A17\Twill\Models\Media;

class RefreshCrops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:refresh-crops
        {modelName : The fully qualified model name (e.g. App\Models\Post)}
        {roleName : The role name for which crops will be refreshed}
        {--dry : Print the operations that would be performed without modifying the database}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh all crops for an existing image role';

    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * The model FQCN for this operation.
     *
     * @var string
     */
    protected $modelName;

    /**
     * The role name for this operation.
     *
     * @var string
     */
    protected $roleName;

    /**
     * Available crops for the given model and role name.
     *
     * @var Collection
     */
    protected $crops;

    /**
     * Print the operations that would be performed without modifying the database.
     *
     * @var bool
     */
    protected $isDryRun = false;

    /**
     * Total number of crops created.
     *
     * @var int
     */
    protected $cropsCreated = 0;

    /**
     * Total number of crops deleted.
     *
     * @var int
     */
    protected $cropsDeleted = 0;

    /**
     * Cache for Media models queried during this operation.
     *
     * @var array
     */
    protected $mediaCache = [];

    /**
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct();

        $this->db = $db;
    }

    public function handle()
    {
        $this->isDryRun = $this->option('dry');

        $this->modelName = $this->locateModel($this->argument('modelName'));

        if (! $this->modelName) {
            $this->error("Model `{$this->argument('modelName')}` was not found`");

            return 1;
        }

        $this->roleName = $this->argument('roleName');

        $mediasParams = app($this->modelName)->mediasParams;

        if (! isset($mediasParams[$this->roleName])) {
            $this->error("Role `{$this->roleName}` was not found`");

            return 1;
        }

        $this->crops = collect($mediasParams[$this->roleName]);

        $mediables = $this->db
            ->table(config('twill.mediables_table', 'twill_mediables'))
            ->where(['mediable_type' => $this->modelName, 'role' => $this->roleName]);

        if ($mediables->count() === 0) {
            $this->warn("No mediables found for model `$this->modelName` and role `$this->roleName`");

            return 1;
        }

        if ($this->isDryRun) {
            $this->warn("**Dry Run** No changes are being made to the database");
            $this->warn("");
        }

        foreach ($mediables->get()->groupBy('locale') as $locale => $localeItems) {
            foreach ($localeItems->groupBy('mediable_id') as $mediableId => $items) {
                $this->processMediables($mediableId, $items, $locale);
            }
        }

        $this->printSummary();
    }

    /**
     * Print a summary of all crops created and deleted at the end of the command.
     *
     * @return void
     */
    protected function printSummary()
    {
        if ($this->cropsCreated + $this->cropsDeleted === 0) {
            $this->info("");
            $this->info("No crops to create or delete for this model and role");
            return;
        }

        $this->info("");
        $this->info("Summary:");

        $actionPrefix = $this->isDryRun ? 'to be ' : '';

        if ($this->cropsCreated > 0) {
            $noun = Str::plural('crop', $this->cropsCreated);
            $this->info("{$this->cropsCreated} {$noun} {$actionPrefix}created");
        }

        if ($this->cropsDeleted > 0) {
            $noun = Str::plural('crop', $this->cropsDeleted);
            $this->info("{$this->cropsDeleted} {$noun} {$actionPrefix}deleted");
        }
    }

    /**
     * Process a set of mediables.
     *
     * @param int $mediableId
     * @param Collection $mediables
     * @param string $locale
     * @return void
     */
    protected function processMediables($mediableId, $mediables, $locale)
    {
        foreach ($mediables->groupBy('media_id') as $mediaId => $items) {
            $existingCrops = $items->keyBy('crop')->keys();
            $allCrops = $this->crops->keys();

            if ($cropsToCreate = $allCrops->diff($existingCrops)->all()) {
                $this->createCrops($cropsToCreate, $mediableId, $mediaId, $locale);
            }

            if ($cropsToDelete = $existingCrops->diff($allCrops)->all()) {
                $this->deleteCrops($cropsToDelete, $mediableId, $mediaId);
            }
        }
    }

    /**
     * Create crops for a given item, media and locale.
     *
     * @param string[] $crops
     * @param int $mediableId
     * @param int $mediaId
     * @param string $locale
     * @return void
     */
    protected function createCrops($crops, $mediableId, $mediaId, $locale)
    {
        $this->cropsCreated += count($crops);

        if ($this->isDryRun) {
            $cropNames = collect($crops)->join(', ');
            $noun = Str::plural('crop', count($crops));
            $this->info("Create {$noun} `$cropNames` for mediable_id=`$mediableId` and media_id=`$mediaId`");
            return;
        }

        foreach ($crops as $crop) {
            $ratio = $this->crops[$crop][0];
            $cropParams = $this->getCropParams($mediaId, $ratio['ratio']);

            $this->db
                ->table(config('twill.mediables_table', 'twill_mediables'))
                ->insert([
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'mediable_id' => $mediableId,
                    'mediable_type' => $this->modelName,
                    'media_id' => $mediaId,
                    'role' => $this->roleName,
                    'crop' => $crop,
                    'lqip_data' => null,
                    'ratio' => $ratio['name'],
                    'metadatas' => '{"video": null, "altText": null, "caption": null}',
                    'locale' => $locale,
                ] + $cropParams);
        }
    }

    /**
     * Delete unused crops for a given item and media.
     *
     * @param string[] $crops
     * @param int $mediableId
     * @param int $mediaId
     * @return void
     */
    protected function deleteCrops($crops, $mediableId, $mediaId)
    {
        $this->cropsDeleted += count($crops);

        if ($this->isDryRun) {
            $cropNames = collect($crops)->join(', ');
            $noun = Str::plural('crop', count($crops));
            $this->info("Delete {$noun} `$cropNames` for mediable_id=`$mediableId` and media_id=`$mediaId`");
            return;
        }

        $this->db
            ->table(config('twill.mediables_table', 'twill_mediables'))
            ->where([
                'mediable_type' => $this->modelName,
                'mediable_id' => $mediableId,
                'media_id' => $mediaId,
                'role' => $this->roleName,
            ])
            ->whereIn('crop', $crops)
            ->delete();
    }

    /**
     * Attempt to locate the model from the given command argument.
     *
     * @param string $modelName
     * @return string|null  The model FQCN.
     */
    protected function locateModel($modelName)
    {
        $modelName = ltrim($modelName, "\\");
        $modelStudly = Str::studly($modelName);
        $moduleName = Str::plural($modelStudly);
        $namespace = config('twill.namespace', 'App');

        $attempts = [
            $modelName,
            "$namespace\\Models\\$modelStudly",
            "$namespace\\Twill\\Capsules\\$moduleName\\Models\\$modelStudly",
        ];

        foreach ($attempts as $phpClass) {
            if (class_exists($phpClass)) {
                return $phpClass;
            }
        }

        return null;
    }

    /**
     * Calculate crop params for a media from a given ratio.
     *
     * @param int $mediaId
     * @param float $ratio
     * @return array
     */
    protected function getCropParams($mediaId, $ratio)
    {
        if (!isset($this->mediaCache[$mediaId])) {
            $this->mediaCache[$mediaId] = Media::find($mediaId);
        }

        $width = $this->mediaCache[$mediaId]->width;
        $height = $this->mediaCache[$mediaId]->height;
        $originalRatio = $width / $height;

        if ($originalRatio <= $ratio) {
            $crop_w = $width;
            $crop_h = $width / $ratio;
            $crop_x = 0;
            $crop_y = ($height - $crop_h) / 2;
        } else {
            $crop_h = $height;
            $crop_w = $height * $ratio;
            $crop_y = 0;
            $crop_x = ($width - $crop_w) / 2;
        }

        return compact('crop_w', 'crop_h', 'crop_x', 'crop_y');
    }
}
