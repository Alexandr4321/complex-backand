<?php

return [
    'host' => env('DOCUSIGN_HOST', 'https://account-d.docusign.com'),
    'integrationKey' => env('DOCUSIGN_INTEGRATION_KEY', 'b86a0b69-5a26-4f2e-9148-c949d625b358'),
    'userId' => env('DOCUSIGN_USER_ID', 'a699189b-db6c-45be-a6b8-59a833cc92ee'),
    'apiId' => env('DOCUSIGN_API_ID', 'beeacf0e-eac5-45c4-a35f-32991048037e'),
    'apiHost' => env('DOCUSIGN_API_HOST', 'https://demo.docusign.net/restapi'),
    'permittedFor' => env('DOCUSIGN_PERMITTED_FOR', 'account-d.docusign.com'),
];
