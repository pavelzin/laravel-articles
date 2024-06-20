<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5ef9cd4dbd99a43030df443540276314
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Pawel\\LaravelArticles\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Pawel\\LaravelArticles\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5ef9cd4dbd99a43030df443540276314::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5ef9cd4dbd99a43030df443540276314::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5ef9cd4dbd99a43030df443540276314::$classMap;

        }, null, ClassLoader::class);
    }
}
