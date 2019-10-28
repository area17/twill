<?php

return [
    'dashboard' => [
        'modules' => [
            App\Models\Author::class => [
                'name' => 'authors', // module name
                'label' => 'authors', // optional, if the name of your module above does not work as a label
                'label_singular' => 'author', // optional, if the automated singular version of your name/label above does not work as a label
                'routePrefix' => 'personnel', // optional, if the module is living under a specific routes group
                'count' => true, // show total count with link to index of this module
                'create' => true, // show link in create new dropdown
                'activity' => true, // show activities on this module in actities list
                'draft' => true, // show drafts of this module for current user
                'search' => true, // show results for this module in global search
                'search_fields' => ['name'],
            ],

            App\Models\Category::class => [
                'name' => 'categories', // module name
                'label' => 'categories', // optional, if the name of your module above does not work as a label
                'label_singular' => 'category', // optional, if the automated singular version of your name/label above does not work as a label
                'routePrefix' => '', // optional, if the module is living under a specific routes group
                'count' => true, // show total count with link to index of this module
                'create' => true, // show link in create new dropdown
                'activity' => true, // show activities on this module in actities list
                'draft' => true, // show drafts of this module for current user
                'search' => true, // show results for this module in global search
            ],
        ],

        'analytics' => ['enabled' => false],

        'search_endpoint' => 'admin.search',
    ],
];
