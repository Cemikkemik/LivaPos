<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit0e59bb45d8d8847eab973d070671ec01
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require dirname( __FILE__ ) . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit0e59bb45d8d8847eab973d070671ec01', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInit0e59bb45d8d8847eab973d070671ec01', 'loadClassLoader'));

        $useStaticLoader = PHP_VERSION_ID >= 50600 && !defined('HHVM_VERSION');
        if ($useStaticLoader) {
            require_once dirname( __FILE__ ) . '/autoload_static.php';

            call_user_func(\Composer\Autoload\ComposerStaticInit0e59bb45d8d8847eab973d070671ec01::getInitializer($loader));
        } else {
            $map = require dirname( __FILE__ ) . '/autoload_namespaces.php';
            foreach ($map as $namespace => $path) {
                $loader->set($namespace, $path);
            }

            $map = require dirname( __FILE__ ) . '/autoload_psr4.php';
            foreach ($map as $namespace => $path) {
                $loader->setPsr4($namespace, $path);
            }

            $classMap = require dirname( __FILE__ ) . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->register(true);

        if ($useStaticLoader) {
            $includeFiles = Composer\Autoload\ComposerStaticInit0e59bb45d8d8847eab973d070671ec01::$files;
        } else {
            $includeFiles = require dirname( __FILE__ ) . '/autoload_files.php';
        }
        foreach ($includeFiles as $fileIdentifier => $file) {
            composerRequire0e59bb45d8d8847eab973d070671ec01($fileIdentifier, $file);
        }

        return $loader;
    }
}

function composerRequire0e59bb45d8d8847eab973d070671ec01($fileIdentifier, $file)
{
    if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
        require $file;

        $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;
    }
}
