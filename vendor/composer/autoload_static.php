<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7a8b7cbdb16018c3b1082f86f378a705
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'FEUL\\Admin\\Front_End_Users_List_Admin' => __DIR__ . '/../..' . '/admin/class-front-end-users-list-admin.php',
        'FEUL\\Front_End_Users_List' => __DIR__ . '/../..' . '/includes/class-front-end-users-list.php',
        'FEUL\\Short_Code\\Front_End_Users_List_Short_Code' => __DIR__ . '/../..' . '/front-end/class-front-end-users-list-short-code.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit7a8b7cbdb16018c3b1082f86f378a705::$classMap;

        }, null, ClassLoader::class);
    }
}
