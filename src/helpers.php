<?php

if (!function_exists('get_featured_image_url')) {
    function get_featured_image_url($article)
    {
        if (isset($article['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            return $article['_embedded']['wp:featuredmedia'][0]['source_url'];
        }

        return null;
    }
}
