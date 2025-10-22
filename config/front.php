<?php

return [
    'url' => env('FRONT_URL', 'http://localhost'),
    
    'redirect_url' => env('FRONT_URL', 'http://localhost').env('FRONT_REDIRECT_PATH', '/redirect'),
    
    'pages' => [
    ],
];
