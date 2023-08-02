<?php

namespace App\Http\Controllers\Twill;

use A17\Twill\Http\Controllers\Admin\ModuleController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class AuthorController extends ModuleController
{
    protected $moduleName = 'authors';

    protected $indexOptions = [
        'create' => true,
        'edit' => true,
        'publish' => true,
        'bulkPublish' => true,
        'feature' => false,
        'bulkFeature' => false,
        'restore' => true,
        'bulkRestore' => true,
        'delete' => true,
        'bulkDelete' => true,
        'reorder' => true,
        'permalink' => true,
        'bulkEdit' => true,
        'editInModal' => false,
    ];

    protected $indexColumns = [
        'avatar' => [
            'thumb' => true,
            'variant' => [
                'role' => 'featured',
                'crop' => 'default',
            ],
        ],

        'name' => [
            'field' => 'name',
            'title' => 'Name',
            'sort' => true,
            'visible' => true,
        ],

        'year' => [
            'field' => 'year',
            'title' => 'Year',
            'sort' => true,
        ],

        'birthday' => [
            'field' => 'birthday',
            'title' => 'Birth day',
            'sort' => true,
        ],

        'categories' => [
            'relationship' => 'categories',
            'field' => 'title',
            'title' => 'Categories',
        ],
    ];

    protected $titleColumnKey = 'name';

    public function __construct(Application $app, Request $request)
    {
        parent::__construct($app, $request);

        $this->routePrefix = 'personnel';
        $this->indexOptions['editInModal'] = env('EDIT_IN_MODAL', false);

        $this->enableDraftRevisions = env('ENABLE_DRAFT_REVISIONS', false);
    }

    public function getIndexOptions()
    {
        return $this->indexOptions;
    }

    public function setIndexOptions($options)
    {
        return $this->indexOptions = $options;
    }
}
