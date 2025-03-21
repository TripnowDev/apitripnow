<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5c8852ad60ce1da7f1198206b89624bf
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5c8852ad60ce1da7f1198206b89624bf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5c8852ad60ce1da7f1198206b89624bf::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5c8852ad60ce1da7f1198206b89624bf::$classMap;

        }, null, ClassLoader::class);
    }
}
