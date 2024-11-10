<?php
return [
    'response' => [
        'field_names' => [
            'data' => 'data',
            'code' => 'status',
            'message' => 'message'
        ]
    ],
    'get_all_wrapping' => [
        'enabled' => false,
        'wrapper' => 'data'
    ],
    'params' => [
        'page_length' => 'page_length',
        'search' => 'q',
        'sort' => 'sort',
        'searchable_columns' => 'searchable',
        'page_number' => 'page'
    ],

    'search' => [
        'default_searchable' => ['id', 'name', 'title']
    ],

    'sort_direction' => 'desc', // acceptable values would be [asc,desc]
    'order_column' => 'order',
    'default_page_length' => 15,
    'default_pagination_type' => 'default', // value can be [default,simple,cursor]
    'resources' => [
        'enabled' => true,
        'path' => app_path("Http/Resources"),
    ],

];
