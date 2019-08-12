<?php

namespace A17\Twill\Commands;

use A17\Twill\Models\Media;
use Illuminate\Config\Repository as Config;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use ImageService;

class RefreshLQIP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:lqip {--all=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Low Quality Image Placeholders.';

    /**
     * @var DatabaseManager
     */
    protected $db;

    /**
     * @var Config
     */
    protected $config;

    protected $cropParamsKeys = [
        'crop_x',
        'crop_y',
        'crop_w',
        'crop_h',
    ];

    /**
     * @param DatabaseManager $db
     * @param Config $config
     */
    public function __construct(DatabaseManager $db, Config $config)
    {
        parent::__construct();

        $this->db = $db;
        $this->config = $config;
    }

    // TODO: document this and actually think about moving to queuable job after content type updates
    public function handle()
    {
        $this->db->table('mediables')->orderBy('id')->chunk(100, function ($attached_medias) {
            foreach ($attached_medias as $attached_media) {
                $uuid = Media::withTrashed()->find($attached_media->media_id, ['uuid'])->uuid;

                $lqip_width = $this->config->get('lqip.' . $attached_media->mediable_type . '.' . $attached_media->role . '.' . $attached_media->crop, 30);

                if ($lqip_width && (!$attached_media->lqip_data || $this->option('all'))) {

                    $crop_params = Arr::only((array) $attached_media, $this->cropParamsKeys);

                    $url = ImageService::getLQIPUrl($uuid, $crop_params + ['w' => $lqip_width]);

                    $data = file_get_contents($url);
                    $dataUri = 'data:image/gif;base64,' . base64_encode($data);

                    $this->db->table('mediables')->where('id', $attached_media->id)->update(['lqip_data' => $dataUri]);
                }
            }
        });
    }
}
