<?php

namespace A17\CmsToolkit\Commands;

use A17\CmsToolkit\Models\Media;
use DB;
use Illuminate\Console\Command;
use ImageService;

class RefreshLQIP extends Command
{
    protected $signature = 'cms-toolkit:lqip {--all=0}';

    protected $description = 'Refresh Low Quality Image Placeholders.';

    // TODO: document this and actually think about moving to queuable job after content type updates
    public function handle()
    {
        DB::table('mediables')->chunk(100, function ($attached_medias) {
            foreach ($attached_medias as $attached_media) {
                $uuid = Media::withTrashed()->find($attached_media->media_id, ['uuid'])->uuid;

                $lqip_width = config('lqip.' . $attached_media->mediable_type . '.' . $attached_media->role . '.' . $attached_media->crop);

                if ($lqip_width && (!$attached_media->lqip_data || $this->option('all'))) {
                    $url = ImageService::getLQIPUrl($uuid, [
                        'rect' => $attached_media->crop_x . ',' . $attached_media->crop_y . ',' . $attached_media->crop_w . ',' . $attached_media->crop_h,
                        'w' => $lqip_width,
                    ]);

                    $data = file_get_contents($url);
                    $dataUri = 'data:image/gif;base64,' . base64_encode($data);

                    DB::table('mediables')->where('id', $attached_media->id)->update(['lqip_data' => $dataUri]);
                }
            }
        });
    }
}
