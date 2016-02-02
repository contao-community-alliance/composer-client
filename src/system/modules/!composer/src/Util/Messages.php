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

class Messages extends \Message
{
    /**
     * Add a warning message to the error list.
     *
     * @param string $message The message to add.
     *
     * @return void
     */
    public static function addWarning($message)
    {
        $_SESSION['TL_RAW'][] = sprintf("<p class=\"composer_warn\">Warning: %s</p>\n", $message);
    }
}
