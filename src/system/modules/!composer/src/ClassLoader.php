<?php

namespace ContaoCommunityAlliance\Contao\Composer;

/**
 * Class ClassLoader
 *
 * Custom class loader that run on Contao 2 and Contao 3.
 */
class ClassLoader
{
    public static function register()
    {
        spl_autoload_register('ContaoCommunityAlliance\Contao\Composer\ClassLoader::load', true, true);
    }

    public static function load($className)
    {
        if (strpos($className, 'ContaoCommunityAlliance\\Contao\\Composer\\') === 0) {
            $className = substr($className, 40);
            $className = str_replace('\\', '/', $className);
            $className .= '.php';

            $pathname = dirname(__FILE__) . '/' . $className;
            if (is_file($pathname)) {
                require($pathname);
            }
        }
    }
}
