<?php

return [
   'wordpress' => env('WORDPRESS_API_URL', 'https://api.museann.pl/wp-json'),
        'api_user' => env('WORDPRESS_API_USER', 'dddd-basic-auth-username'),
        'api_pass' => env('WORDPRESS_API_PASS', 'your-basic-auth-password'),
        'jwt_user' => env('WORDPRESS_JWT_USER', 'your-jwt-username'),
        'jwt_pass' => env('WORDPRESS_JWT_PASS', 'your-jwt-password'),
        'categories' => env('WORDPRESS_CATEGORIES', '42'),
];
