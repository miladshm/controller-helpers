<?php
return [
    'response' => [
        'field_names' => [
            'data' => 'data',
            'code' => 'status',
            'message' => 'message'
        ]
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

    'sort_direction' => 'asc', // acceptable values would be [asc,desc]
    'order_column' => 'order'

];