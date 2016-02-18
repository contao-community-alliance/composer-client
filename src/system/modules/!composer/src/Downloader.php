<?php

/**
 * Composer integration for Contao.
 *
 * PHP version 5
 *
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @author     SyZn <zalan@gmx.net>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
 */

namespace ContaoCommunityAlliance\Contao\Composer;

use ContaoCommunityAlliance\Contao\Composer\Util\Messages;

/**
 * Class Downloader
 *
 * Convenience class for downloading files.
 */
class Downloader
{
    /**
     * Download an url and return or store contents.
     *
     * @param string $url
     * @param bool   $file
     *
     * @return bool|null|string
     * @throws \Exception
     */
    public static function download($url, $file = false)
    {
        if (Runtime::isCurlEnabled()) {
            return static::curlDownload($url, $file);
        } else {
            if (Runtime::isAllowUrlFopenEnabled()) {
                return static::fgetDownload($url, $file);
            } else {
                throw new \RuntimeException('No download mechanism available');
            }
        }
    }

    /**
     * @param      $url
     * @param bool $file
     *
     * @return bool|null|string
     * @throws \Exception
     *
     * @SuppressWarnings("unused")
     */
    public static function fgetDownload($url, $file = false)
    {
        $return = null;

        if ($file === false) {
            $return = true;
            $file   = 'php://temp';
        }

        $fileStream = fopen($file, 'wb+');

        fwrite($fileStream, file_get_contents($url));
        $headers              = $http_response_header;
        $firstHeaderLine      = $headers[0];
        $firstHeaderLineParts = explode(' ', $firstHeaderLine);

        if ($firstHeaderLineParts[1] == 301 || $firstHeaderLineParts[1] == 302) {
            foreach ($headers as $header) {
                $matches = array();
                preg_match('/^Location:(.*?)$/', $header, $matches);
                $url = trim(array_pop($matches));
                return static::fgetDownload($url, $file);
            }
            throw new \Exception("Can't get the redirect location");
        }

        if ($return) {
            rewind($fileStream);
            $return = stream_get_contents($fileStream);
        }

        fclose($fileStream);

        return $return;
    }

    /**
     * @param      $url
     * @param bool $file
     *
     * @return bool|null|string
     * @throws \Exception
     */
    public static function curlDownload($url, $file = false)
    {
        $return = null;

        if ($file === false) {
            $return = true;
            $file   = 'php://temp';
        }

        $curl = curl_init($url);

        $headerStream = fopen('php://temp', 'wb+');
        $fileStream   = fopen($file, 'wb+');

        // SNI workaround for very old systems like CentOS who do not support SNI.
        self::checkSNI($url, $curl);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($curl, CURLOPT_WRITEHEADER, $headerStream);
        curl_setopt($curl, CURLOPT_FILE, $fileStream);

        curl_exec($curl);

        rewind($headerStream);
        $header = stream_get_contents($headerStream);

        if ($return) {
            rewind($fileStream);
            $return = stream_get_contents($fileStream);
        }

        fclose($headerStream);
        fclose($fileStream);

        if (curl_errno($curl)) {
            throw new \Exception(
                curl_error($curl),
                curl_errno($curl)
            );
        }

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($code == 301 || $code == 302) {
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $url = trim(array_pop($matches));

            return static::curlDownload($url, $file);
        }

        return $return;
    }

    /**
     * Check if SNI is available and disable remote host checking if not.
     *
     * @param string   $url  The url.
     *
     * @param resource $curl The curl resource.
     *
     * @return void
     */
    private static function checkSNI($url, $curl)
    {
        if ('https' !== substr($url, 0, 5)) {
            return;
        }

        $curlVersion = curl_version();
        // Curl 7.18.1 - March 30 2008 supports server name indication (RFC 4366), aka SNI
        // (http://curl.haxx.se/changes.html)
        if (version_compare(trim($curlVersion['version']), '7.18.1', '<')) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            Messages::addWarning(
                'SNI support not available, curl version too old. Host verification has been deactivated.'
            );
            return;
        }
        // We only check for OpenSSL.
        $ssl = explode('/', trim($curlVersion['ssl_version']));
        if ((count($ssl) !== 2) || ($ssl[0] !== 'OpenSSL')) {
            return;
        }

        // OpenSSL 0.9.8f support SNI, 0.9.8k and later has this enabled by default
        // (https://wiki.apache.org/httpd/NameBasedSSLVHostsWithSNI)
        if (version_compare($ssl[1], '0.9.8f', '<')) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            Messages::addWarning(
                'SNI support not available, OpenSSL version too old. Host verification has been deactivated.'
            );
        }
    }
}
