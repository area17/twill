<?php

namespace App\Http\Controllers\Admin;

use A17\Twill\Http\Controllers\Admin\ModuleController;

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

    /*
     * Available columns of the index view
     */
    protected $indexColumns = [
        'name' => [
            'field' => 'name',
            'title' => 'Name',
            'sort' => true,
            'visible' => true,
        ],

        'birthday' => [
            'field' => 'birthday',
            'title' => 'Birth day',
            'sort' => true,
        ],
    ];

    protected $titleColumnKey = 'name';
}
