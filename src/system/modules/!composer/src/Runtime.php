<?php

/**
 * Composer integration for Contao.
 *
 * PHP version 5
 *
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @author     Dominik Zogg <dominik.zogg@gmail.com>
 * @author     Oliver Hoff <oliver@hofff.com>
 * @author     Nicky Hoff <nick@hofff.com>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
 */

namespace ContaoCommunityAlliance\Contao\Composer;

use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Util\Filesystem;
use Composer\Util\Silencer;
use ContaoCommunityAlliance\Contao\Composer\Util\CaBundleWorkaround;
use ContaoCommunityAlliance\Contao\Composer\Util\ErrorHandler;

/**
 * Class Runtime
 *
 * Composer runtime control.
 */
class Runtime
{
    const APC_MIN_VERSION_RUNTIME_CACHE_BY_DEFAULT = '3.0.13';

    const HTACCESS = <<<EOF
<IfModule !mod_authz_core.c>
  Order deny,allow
  Deny from all

  <FilesMatch "\.(js|css|htc|png|gif|jpe?g|ico|swf|flv|mp4|webm|ogv|mp3|ogg|oga|eot|otf|tt[cf]|woff|woff2|svg|svgz)$">
    Order allow,deny
    Allow from all
  </FilesMatch>
</IfModule>

<IfModule mod_authz_core.c>
  Require all denied

  <FilesMatch "\.(js|css|htc|png|gif|jpe?g|ico|swf|flv|mp4|webm|ogv|mp3|ogg|oga|eot|otf|tt[cf]|woff|woff2|svg|svgz)$">
    Require all granted
  </FilesMatch>
</IfModule>
EOF;

    const HTACCESS_OLD = <<<EOF
<IfModule !mod_authz_core.c>
  Order deny,allow
  Deny from all
</IfModule>
<IfModule mod_authz_core.c>
  Require all denied
</IfModule>
EOF;

    const COMPOSER_JSON = <<<EOF
{
    "name": "local/website",
    "description": "A local website project",
    "type": "project",
    "license": "proprietary",
    "require": {
        "contao-community-alliance/composer-client": "~0.12"
    },
    "prefer-stable": true,
    "minimum-stability": "dev",
    "config": {
        "preferred-install": "dist",
        "cache-dir": "cache",
        "component-dir": "../assets/components"
    },
    "repositories": [
        {
            "type": "composer",
            "url": "https://legacy-packages-via.contao-community-alliance.org/"
        },
        {
            "type": "artifact",
            "url": "packages"
        },
        {
            "type": "composer",
            "url": "http://legacy-packages-via.contao-community-alliance.org/"
        }
    ]
}
EOF;

    /**
     * Flag if curl is enabled and not disabled.
     *
     * @var bool
     */
    private static $curlIsEnabled = null;

    /**
     * List of curl functions to check against disabled functions.
     *
     * @var array
     */
    private static $curlFunctions = array(
        'curl_close',
        'curl_copy_handle',
        'curl_errno',
        'curl_error',
        'curl_escape',
        'curl_exec',
        'curl_file_create',
        'curl_getinfo',
        'curl_init',
        'curl_multi_add_handle',
        'curl_multi_close',
        'curl_multi_exec',
        'curl_multi_getcontent',
        'curl_multi_info_read',
        'curl_multi_init',
        'curl_multi_remove_handle',
        'curl_multi_select',
        'curl_multi_setopt',
        'curl_multi_strerror',
        'curl_pause',
        'curl_reset',
        'curl_setopt_array',
        'curl_setopt',
        'curl_share_close',
        'curl_share_init',
        'curl_share_setopt',
        'curl_strerror',
        'curl_unescape',
        'curl_version',
    );

    /**
     * List of disabled functions.
     *
     * @var array
     */
    private static $disabledFunctions = null;

    /**
     * Initialize the composer environment.
     */
    public static function initialize()
    {
        if (version_compare(PHP_VERSION, COMPOSER_MIN_PHPVERSION, '<')) {
            return;
        }

        if (TL_MODE == 'BE') {
            $GLOBALS['TL_HOOKS']['loadLanguageFile']['composer'] = array(
                'ContaoCommunityAlliance\Contao\Composer\Client',
                'disableOldClientHook'
            );

            $input = \Input::getInstance();
            if ($input->get('do') == 'repository_manager') {
                $environment = \Environment::getInstance();

                header('Location: ' . $environment->base . 'contao/main.php?do=composer');
                exit;
            }
        }

        static::registerVendorClassLoader();
    }

    /**
     * Initialize the composer environment.
     */
    public static function setUp()
    {
        if (version_compare(PHP_VERSION, COMPOSER_MIN_PHPVERSION, '<')) {
            return;
        }

        // Hijack the error handler as we can not use the one from Contao.
        ErrorHandler::replaceErrorHandler();

        // check composer folder exists
        if (!is_dir(COMPOSER_DIR_ABSOULTE)) {
            \Files::getInstance()
                  ->mkdir(COMPOSER_DIR_RELATIVE);
        }

        // check artifacts folder exists
        if (!is_dir(COMPOSER_DIR_ABSOULTE . '/packages')) {
            \Files::getInstance()
                  ->mkdir(COMPOSER_DIR_RELATIVE . '/packages');
        }

        // check .htaccess exists and is up to date
        if (!file_exists(COMPOSER_DIR_ABSOULTE . '/.htaccess')
            || str_replace(array("\r", "\n", "\t", ' '), '', file_get_contents(COMPOSER_DIR_ABSOULTE . '/.htaccess'))
               == str_replace(array("\r", "\n", "\t", ' '), '', static::HTACCESS_OLD)
        ) {
            file_put_contents(COMPOSER_DIR_ABSOULTE . '/.htaccess', static::HTACCESS);
        }

        // check composer.json exists
        if (!file_exists(COMPOSER_DIR_ABSOULTE . '/composer.json')) {
            file_put_contents(COMPOSER_DIR_ABSOULTE . '/composer.json', static::COMPOSER_JSON);
        }

        if (!getenv('COMPOSER_HOME')) {
            putenv('COMPOSER_HOME=' . COMPOSER_DIR_ABSOULTE);
        }

        // see #54
        if (!getenv('PATH')) {
            if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                putenv('PATH=%SystemRoot%\system32;%SystemRoot%;%SystemRoot%\System32\Wbem');
            } else {
                putenv('PATH=/opt/local/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin');
            }
        }
    }

    /**
     * Load and install the composer.phar.
     *
     * @return bool
     */
    public static function updateComposer()
    {
        $url  = 'https://getcomposer.org/composer.phar';
        $file = COMPOSER_DIR_ABSOULTE . '/composer.phar';
        Downloader::download($url, $file);
        return true;
    }

    /**
     * Try to increase memory.
     *
     * Inspired by Nils Adermann, Jordi Boggiano
     *
     * @see https://github.com/composer/composer/blob/master/bin/composer
     */
    public static function increaseMemoryLimit()
    {
        if (function_exists('ini_set')) {
            @ini_set('display_errors', 1);

            $memoryInBytes = function ($value) {
                $unit  = strtolower(substr($value, -1, 1));
                $value = (int) $value;
                switch ($unit) {
                    case 'g':
                        $value *= 1024;
                    // no break (cumulative multiplier)
                    case 'm':
                        $value *= 1024;
                    // no break (cumulative multiplier)
                    case 'k':
                        $value *= 1024;
                }

                return $value;
            };

            $memoryLimit = trim(ini_get('memory_limit'));
            // Increase memory_limit if it is lower than 512M
            if ($memoryLimit != -1 && $memoryInBytes($memoryLimit) < 1024 * 1024 * 1024) {
                @ini_set('memory_limit', '1024M');
            }
            unset($memoryInBytes, $memoryLimit);
        }
    }

    /**
     * Read the stub from the composer.phar and return the warning timestamp.
     *
     * @return bool|int
     */
    public static function readComposerDevWarningTime()
    {
        $configPathname = new \File(COMPOSER_DIR_RELATIVE . '/composer.phar');
        $buffer         = '';
        do {
            $buffer .= fread($configPathname->handle, 1024);
        } while (!preg_match('#define\(\'COMPOSER_DEV_WARNING_TIME\',\s*(\d+)\);#', $buffer, $matches)
            && !feof($configPathname->handle)
        );
        if ($matches[1]) {
            return (int) $matches[1];
        }
        return false;
    }

    /**
     * Clear the composer cache.
     *
     * @param \Input $input
     */
    public static function clearComposerCache()
    {
        $filesystem = new Filesystem();
        return $filesystem->removeDirectory(COMPOSER_DIR_ABSOULTE . '/cache');
    }

    /**
     * Determinate if safe mode hack is enabled.
     *
     * @return bool
     */
    public static function isSafeModeHackEnabled()
    {
        return (bool) $GLOBALS['TL_CONFIG']['useFTP'];
    }

    /**
     * Determinate if the php version is supported by composer.
     *
     * @return bool
     */
    public static function isPhpVersionSupported()
    {
        return (bool) version_compare(PHP_VERSION, COMPOSER_MIN_PHPVERSION, '>=');
    }

    /**
     * Determinate if curl is enabled.
     *
     * @return bool
     */
    public static function isCurlEnabled()
    {
        if (null === self::$curlIsEnabled) {
            self::$curlIsEnabled = true;

            if (!function_exists('curl_init')) {
                self::$curlIsEnabled = false;
            }

            if (count(array_intersect(self::$curlFunctions, self::getDisabledFunctions()))) {
                self::$curlIsEnabled = false;
            }
        }

        return self::$curlIsEnabled;
    }

    /**
     * Determinate if allow_url_fopen is enabled.
     *
     * @return bool
     */
    public static function isAllowUrlFopenEnabled()
    {
        return (bool) ini_get('allow_url_fopen');
    }

    /**
     * Determinate if apc is enabled.
     *
     * @return bool
     */
    public static function isApcEnabled()
    {
        if (extension_loaded('apcu')) {
            return false;
        }

        if (!function_exists('apc_clear_cache')) {
            return false;
        }

        return extension_loaded('apc') && ini_get('apc.enabled') && ini_get('apc.cache_by_default');
    }

    /**
     * Determinate if suhosin is enabled.
     *
     * @return bool
     */
    public static function isSuhosinEnabled()
    {
        if (!extension_loaded('suhosin')) {
            return false;
        }
        if (strpos(ini_get('suhosin.executor.include.whitelist'), 'phar') > -1) {
            return false;
        }
        return true;
    }

    /**
     * Determinate if downloading is possible.
     *
     * @return bool
     */
    public static function isDownloadImpossible()
    {
        if (class_exists('ZipArchive') || static::testProcess('unzip')) {
            return false;
        }

        return true;
    }

    /**
     * Get the disabled functions.
     *
     * @return array
     */
    public static function getDisabledFunctions()
    {
        if (null === self::$disabledFunctions) {
            self::$disabledFunctions = array_map('trim', explode(',', ini_get('disable_functions')));
        }

        return self::$disabledFunctions;
    }

    /**
     * Try to disable APC.
     *
     * @return bool Return true on success, false if not.
     */
    public static function disableApc()
    {
        if (in_array('ini_set', self::getDisabledFunctions())) {
            return false;
        }

        $apc = new \ReflectionExtension('apc');
        if (version_compare($apc->getVersion(), self::APC_MIN_VERSION_RUNTIME_CACHE_BY_DEFAULT, '<')) {
            return false;
        }

        return ini_set('apc.cache_by_default', 0) !== false;
    }

    /**
     * Check the local environment, return true if everything is fine, an array of errors otherwise.
     *
     * @return bool|array
     */
    public static function checkEnvironment()
    {
        $errors = array();

        if (static::isSafeModeHackEnabled()) {
            $errors[] = $GLOBALS['TL_LANG']['composer_client']['ftp_mode'];
        }

        // check for php version
        if (!static::isPhpVersionSupported()) {
            $errors[] = sprintf(
                $GLOBALS['TL_LANG']['composer_client']['php_version'],
                COMPOSER_MIN_PHPVERSION,
                PHP_VERSION
            );
        }

        // check for curl
        if (!static::isCurlEnabled()) {
            $errors[] = $GLOBALS['TL_LANG']['composer_client']['curl_missing'];
        }

        // check for apc and try to disable

        if (static::isApcEnabled() && !static::disableApc()) {
            $errors[] = $GLOBALS['TL_LANG']['composer_client']['could_not_disable_apc'];
        }

        // check for suhosin
        if (static::isSuhosinEnabled()) {
            $errors[] = $GLOBALS['TL_LANG']['composer_client']['suhosin_enabled'];
        }

        if (static::isDownloadImpossible()) {
            $errors[] = $GLOBALS['TL_LANG']['composer_client']['download_impossible'];
        }

        if (count($errors)) {
            return $errors;
        }

        return true;
    }

    /**
     * Register the vendor class loader.
     */
    public static function registerVendorClassLoader()
    {
        static $registered = false;

        if ($registered) {
            return;
        }

        $registered = true;

        if (file_exists(COMPOSER_DIR_ABSOULTE . '/vendor/autoload.php')) {
            // register composer vendor class loader
            require_once(COMPOSER_DIR_ABSOULTE . '/vendor/autoload.php');
        }
    }

    /**
     * Register the composer class loader from composer.phar.
     */
    public static function registerComposerClassLoader()
    {
        static $registered = false;

        if ($registered) {
            return;
        }

        $registered = true;

        // register composer class loader
        if (file_exists(COMPOSER_DIR_ABSOULTE . '/composer.phar')) {
            $phar             = new \Phar(COMPOSER_DIR_ABSOULTE . '/composer.phar');
            $autoloadPathname = $phar['vendor/autoload.php'];
            require_once($autoloadPathname->getPathname());

            CaBundleWorkaround::setCaFileIfOpenBaseDirInUse($phar);
        }
    }

    /**
     * Load composer and the composer class loader.
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public static function createComposer(IOInterface $io)
    {
        chdir(COMPOSER_DIR_ABSOULTE);

        // try to increase memory limit
        static::increaseMemoryLimit();

        // register composer class loader
        static::registerComposerClassLoader();

        // create composer factory
        /** @var \Composer\Factory $factory */
        $factory = new Factory();

        // create composer
        if (class_exists('\Composer\Util\Silencer')) {
            $composer = Silencer::call(array($factory, 'createComposer'), $io);
        } else {
            $composer = $factory->createComposer($io);
        }

        return $composer;
    }

    /**
     * Run a process for testing.
     *
     * @param string $cmd
     *
     * @return bool Return true if the process terminate without error, false otherwise.
     *
     * @SuppressWarnings("unused")
     */
    public static function testProcess($cmd)
    {
        if (in_array('proc_open', self::getDisabledFunctions())) {
            return false;
        }

        $proc = proc_open(
            $cmd,
            array(
                array('pipe', 'r'),
                array('pipe', 'w'),
                array('pipe', 'w')
            ),
            $pipes
        );

        if (is_resource($proc)) {
            return proc_close($proc) != -1;
        }

        return false;
    }
}
