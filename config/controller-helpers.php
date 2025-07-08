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
        'page_number' => 'page',
        'relations' => 'relation',
        'counts' => 'count',
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

    // Performance optimization settings
    'performance' => [
        'max_page_length' => 500,
        'max_records_without_pagination' => 10000,
        'enable_query_cache' => true,
        'enable_performance_metrics' => false,
        'chunk_size' => 1000,
    ],

    // Transaction settings
    'transactions' => [
        'use_for_simple_operations' => false,
        'use_for_store' => true,
        'use_for_update' => true,
        'use_for_delete' => true,
    ],

    // Caching settings
    'cache' => [
        'enable_config_cache' => true,
        'enable_schema_cache' => true,
        'config_cache_ttl' => 3600, // 1 hour
        'schema_cache_ttl' => 7200, // 2 hours
    ],
];
