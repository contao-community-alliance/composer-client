<?php

/**
 * Composer integration for Contao.
 *
 * PHP version 5
 *
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
 */

namespace ContaoCommunityAlliance\Contao\Composer\Util;

class CaBundleWorkaround
{
    /**
     * Test if an open base dir has been defined.
     * If so, the list of well known root ca bundle locations will get matched against the list of defined basedirs
     * and if none matches, the fallback on the embedded bundle will be activated.
     *
     * @param \Phar $phar The composer phar file.
     *
     * @return void
     */
    public static function setCaFileIfOpenBaseDirInUse(\Phar $phar)
    {
        // No open basedir active - we do not need to check.
        if ('' === ($directories = ini_get('open_basedir'))) {
            return;
        }

        $directories = explode(':', $directories);
        // See list in \Composer\Util\RemoteFilesystem::
        $caBundlePaths = array(
            '/etc/pki/tls/certs/ca-bundle.crt',
            '/etc/ssl/certs/ca-certificates.crt',
            '/etc/ssl/ca-bundle.pem',
            '/usr/local/share/certs/ca-root-nss.crt',
            '/usr/ssl/certs/ca-bundle.crt',
            '/opt/local/share/curl/curl-ca-bundle.crt',
            '/usr/local/share/curl/curl-ca-bundle.crt',
            '/usr/share/ssl/certs/ca-bundle.crt',
            '/etc/ssl/cert.pem',
            '/usr/local/etc/ssl/cert.pem',
            // We add the system temp dir here as well, as it will get used in the composer.phar internal fallback
            // detection as target directory to unpack the certificate bundle to.
            sys_get_temp_dir()
        );

        // Scan for open base dir intersection of known ca bundle paths.
        foreach ($directories as $directory) {
            foreach ($caBundlePaths as $caBundlePath) {
                if (0 === strncmp($directory, dirname($caBundlePath), strlen($directory))) {
                    return;
                }
            }
        }

        // Fall back to the embedded certificate list otherwise.
        // Note that we can not use the internal mechanism of composer for this, as there sys_get_temp_dir() is used.
        // This will resort to /tmp on most systems which is almost certainly not within the allowed paths.
        $file = $phar['res/cacert.pem']->getPathname();
        // Try to unpack cacert.pem and use it.
        $hash       = hash_file('sha256', $file);
        $targetPath = rtrim(TL_ROOT . '/system/cache', '\\/') . '/composer-cacert-' . $hash . '.pem';

        if (!file_exists($targetPath) || $hash !== hash_file('sha256', $targetPath)) {
            self::streamCopy($file, $targetPath);
            chmod($targetPath, 0666);
        }

        Messages::addWarning('System certificate bundle not readable, will try to use embedded certificate list.');

        putenv('SSL_CERT_FILE=' . $targetPath);
    }

    /**
     * Copy the source file via stream copy to the target.
     *
     * @param string $source The source file name.
     *
     * @param string $target The target file name.
     *
     * @return void
     */
    private static function streamCopy($source, $target)
    {
        $source = fopen($source, 'r');
        $target = fopen($target, 'w+');

        stream_copy_to_stream($source, $target);
        fclose($source);
        fclose($target);

        unset($source, $target);
    }
}
