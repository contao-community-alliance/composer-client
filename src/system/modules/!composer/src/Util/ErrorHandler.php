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

class ErrorHandler
{
    /**
     * Replace the Contao error handler.
     *
     * @return void
     */
    public static function replaceErrorHandler()
    {
        $previous = set_error_handler(array(__CLASS__, 'handleError'));
        // If the previous error handler was not the one from Contao, restore it as we assume it was behaving good.
        if ('__error' !== $previous) {
            restore_error_handler();
        }
    }

    /**
     * Error handler function - this one differs from the error handler in use in Contao as it honors the silenced
     * error levels.
     *
     * @param int $level The error level of the current error.
     *
     * @return void
     *
     * @internal
     */
    public static function handleError($level)
    {
        // Check if the error should be reported.
        if (!(error_reporting() & $level)) {
            return;
        }

        call_user_func_array('__error', func_get_args());
    }
}
