<?php

return [
    'imports' => [
        'System',
        'Base',
        'Auth',
        'App',
    ],
    
    'base' => [
        'file' => [
            'ownerTypes' => [
                'project' => \App\App\Models\Project::class,
                'banner' => \App\App\Models\Banner::class,
            ],
        ],
    ],
    
    /**
     * key - grant name
     * value - grant modelType
     */
    'grants' => [
        'moderator' => null,
        'admin' => null,
    ],
    
    'permits' => [
        'moderator' => null,
        'admin' => null,
    ],
    
    'permits_grants' => [
        'moderator' => [
            'moderator' => false,
        ],
        'admin' => [
            'admin' => false,
        ],
    ],
];
