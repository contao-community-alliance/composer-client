<?php

/**
 * Hack the Contao2 Controller::classFileExists()
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *     Christian Schiffler <c.schiffler@cyberspectrum.de>
 *
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @license MIT
 */

/**
 * Class Contao2ClassLoaderHack
 *
 * @author Tristan Lins <tristan.lins@bit3.de>
 * @author Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
if (version_compare(VERSION, '3', '<')) {
    class Contao2ClassFileExistsHack extends FileCache
    {
        public static function register()
        {
            $cache = FileCache::getInstance('classes');

            if (!$cache instanceof Contao2ClassFileExistsHack) {
                FileCache::$arrInstances['classes'] = new Contao2ClassFileExistsHack($cache);
            }
        }

        /**
         * The internal cache.
         *
         * @var FileCache
         */
        protected $cache;

        /**
         * @param FileCache $cache
         */
        public function __construct(FileCache $cache)
        {
            $this->cache = $cache;
        }

        /**
         * {@inheritdoc}
         */
        public function __destruct()
        {
            // no op
        }

        /**
         * Trigger all class loaders and try to load the class through them.
         *
         * @param string $strKey
         *
         * @return bool
         */
        protected function classExists($strKey)
        {
            $exists = class_exists($strKey, false);
            if (!$exists) {
                $functions = spl_autoload_functions();
                while (!$exists && count($functions)) {
                    $function = array_shift($functions);

                    if ($function == '__autoload') {
                        continue;
                    }

                    call_user_func($function, $strKey);
                    $exists = class_exists($strKey, false);
                }
            }
            return $exists;
        }

        /**
         * {@inheritdoc}
         */
        public function __isset($strKey)
        {
            $isset = $this->cache->__isset($strKey);

            return $isset
                ? $isset
                : $this->classExists($strKey);
        }

        /**
         * {@inheritdoc}
         */
        public function __get($strKey)
        {
            $value = $this->cache->__get($strKey);

            return $value
                ? $value
                : $this->classExists($strKey);
        }

        /**
         * {@inheritdoc}
         */
        public function __set($strKey, $varValue)
        {
            $this->cache->__set($strKey, $varValue);
        }

        /**
         * {@inheritdoc}
         */
        public function __unset($strKey)
        {
            $this->cache->__unset($strKey);
        }
    }
} else {
    class Contao2ClassFileExistsHack
    {
        public static function register()
        {
            // no op
        }
    }
}
