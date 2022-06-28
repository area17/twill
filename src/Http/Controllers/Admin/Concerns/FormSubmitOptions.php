<?php

namespace A17\Twill\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Model;

trait FormSubmitOptions
{
    public function getSubmitOptions(Model $item): ?array
    {
        if ($item->cmsRestoring ?? false) {
            return [
                'draft' => [
                    [
                        'name' => 'restore',
                        'text' => twillTrans('twill::lang.publisher.restore-draft'),
                    ],
                    [
                        'name' => 'restore-close',
                        'text' => twillTrans('twill::lang.publisher.restore-draft-close'),
                    ],
                    [
                        'name' => 'restore-new',
                        'text' => twillTrans('twill::lang.publisher.restore-draft-new'),
                    ],
                    [
                        'name' => 'cancel',
                        'text' => twillTrans('twill::lang.publisher.cancel'),
                    ],
                ],
                'live' => [
                    [
                        'name' => 'restore',
                        'text' => twillTrans('twill::lang.publisher.restore-live'),
                    ],
                    [
                        'name' => 'restore-close',
                        'text' => twillTrans('twill::lang.publisher.restore-live-close'),
                    ],
                    [
                        'name' => 'restore-new',
                        'text' => twillTrans('twill::lang.publisher.restore-live-new'),
                    ],
                    [
                        'name' => 'cancel',
                        'text' => twillTrans('twill::lang.publisher.cancel'),
                    ],
                ],
                'update' => [
                    [
                        'name' => 'restore',
                        'text' => twillTrans('twill::lang.publisher.restore-live'),
                    ],
                    [
                        'name' => 'restore-close',
                        'text' => twillTrans('twill::lang.publisher.restore-live-close'),
                    ],
                    [
                        'name' => 'restore-new',
                        'text' => twillTrans('twill::lang.publisher.restore-live-new'),
                    ],
                    [
                        'name' => 'cancel',
                        'text' => twillTrans('twill::lang.publisher.cancel'),
                    ],
                ],
            ];
        }

        return [
            'draft' => [
                [
                    'name' => 'save',
                    'text' => twillTrans('twill::lang.publisher.save'),
                ],
                [
                    'name' => 'save-close',
                    'text' => twillTrans('twill::lang.publisher.save-close'),
                ],
                [
                    'name' => 'save-new',
                    'text' => twillTrans('twill::lang.publisher.save-new'),
                ],
                [
                    'name' => 'cancel',
                    'text' => twillTrans('twill::lang.publisher.cancel'),
                ],
            ],
            'live' => [
                [
                    'name' => 'publish',
                    'text' => twillTrans('twill::lang.publisher.publish'),
                ],
                [
                    'name' => 'publish-close',
                    'text' => twillTrans('twill::lang.publisher.publish-close'),
                ],
                [
                    'name' => 'publish-new',
                    'text' => twillTrans('twill::lang.publisher.publish-new'),
                ],
                [
                    'name' => 'cancel',
                    'text' => twillTrans('twill::lang.publisher.cancel'),
                ],
            ],
            'update' => [
                [
                    'name' => 'update',
                    'text' => twillTrans('twill::lang.publisher.update'),
                ],
                [
                    'name' => 'update-close',
                    'text' => twillTrans('twill::lang.publisher.update-close'),
                ],
                [
                    'name' => 'update-new',
                    'text' => twillTrans('twill::lang.publisher.update-new'),
                ],
                [
                    'name' => 'cancel',
                    'text' => twillTrans('twill::lang.publisher.cancel'),
                ],
            ],
        ];
    }
}
