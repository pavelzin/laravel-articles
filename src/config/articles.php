<?php

return [
        'wordpress' => [
            'api_url' => env('WORDPRESS_API_URL'),
            'api_user' => env('WORDPRESS_API_USER'),
            'api_pass' => env('WORDPRESS_API_PASS'),
            'jwt_user' => '',
            'jwt_pass' => '',
            'categories' => [
                'default' => 8, // Domyślna kategoria dla jednojęzycznych stron
                'pl' => 8,      // Kategoria dla polskiego
                'en' => 9,      // Kategoria dla angielskiego
                'es' => 10,     // Kategoria dla hiszpańskiego
            ],
            'multilingual' => false, // Ustawienie wskazujące, czy strona obsługuje wiele języków
        ],
    ];
    