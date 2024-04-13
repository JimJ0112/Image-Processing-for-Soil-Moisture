<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit05777ea102b65e535a535c16ebab841b
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Phpml\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Phpml\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-ai/php-ml/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'I' => 
        array (
            'Imagick' => 
            array (
                0 => __DIR__ . '/..' . '/calcinai/php-imagick/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit05777ea102b65e535a535c16ebab841b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit05777ea102b65e535a535c16ebab841b::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit05777ea102b65e535a535c16ebab841b::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit05777ea102b65e535a535c16ebab841b::$classMap;

        }, null, ClassLoader::class);
    }
}
