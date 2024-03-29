<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb24f09c6b40834322ec88ffc7f125747
{
    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Inc\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb24f09c6b40834322ec88ffc7f125747::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb24f09c6b40834322ec88ffc7f125747::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb24f09c6b40834322ec88ffc7f125747::$classMap;

        }, null, ClassLoader::class);
    }
}
