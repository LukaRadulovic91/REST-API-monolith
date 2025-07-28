<?php

return [

    /*
    |--------------------------------------------------------------------------
    | L5 Swagger
    |--------------------------------------------------------------------------
    |
    | Paket za generisanje OpenAPI/Swagger dokumentacije u Laravelu.
    |
    */

    'default' => 'api',

    'documentations' => [

        'api' => [

            'api' => [
                'title' => env('L5_SWAGGER_API_TITLE', 'Laravel API'),
            ],

            // Putanja gde se čuvaju generisani OpenAPI fajlovi
            'paths' => [
                /*
                 * Putanja za anotacije (gde pretražuješ kod za @OA anotacije)
                 */
                'annotations' => [
                    base_path('app/Http/Controllers'),
                ],

                /*
                 * Putanja gde se generiše json fajl dokumentacije
                 */
                'docs_json' => storage_path('api-docs/api-docs.json'),

                /*
                 * Putanja gde se generiše yaml fajl dokumentacije (opciono)
                 */
                'docs_yaml' => storage_path('api-docs/api-docs.yaml'),

                /*
                 * Putanja do Swagger UI assets u public folderu
                 */
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                /*
                 * Putanja gde se kopira Swagger UI (publikuje)
                 */
                'swagger_ui_assets_path' => 'vendor/swagger-api/swagger-ui/dist',
            ],

            'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

            'generate_yaml_copy' => false,

            'proxy' => false,

            'additional_config_url' => null,

            'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

            'validator_url' => null,

            'constants' => [
                'L5_SWAGGER_CONST_HOST' => env('APP_URL', 'http://localhost'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route configuration
    |--------------------------------------------------------------------------
    */

    'routes' => [

        /*
         * Ruta za prikaz Swagger UI (interaktivna dokumentacija)
         */
        'api' => 'api/documentation',

        /*
         * Ruta za JSON fajl OpenAPI dokumentacije
         */
        'docs' => 'api-docs.json',

        /*
         * Ruta za YAML fajl OpenAPI dokumentacije (ako koristiš)
         */
        'docs_yaml' => 'api-docs.yaml',

        /*
         * Ruta za assets Swagger UI (JS, CSS)
         */
        'assets' => 'vendor/swagger-api/swagger-ui',

        /*
         * Middleware za pristup Swagger UI (možeš podesiti auth ako želiš)
         */
        'middleware' => [
            'api' => [],
            'asset' => [],
            'docs' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Definitions
    |--------------------------------------------------------------------------
    |
    | Podesi ako koristiš OAuth2, API keys, ili JWT auth u dokumentaciji
    |
    */

    'securityDefinitions' => [],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    */

    'ui' => [
        'display' => true,
        'validatorUrl' => null,
        'docExpansion' => 'none',
        'defaultModelRendering' => 'schema',
        'filter' => false,
        'showRequestHeaders' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Options
    |--------------------------------------------------------------------------
    */

    'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

    'generate_yaml_copy' => false,

];
