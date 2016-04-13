<?php

/**
 * Composer integration for Contao.
 *
 * PHP version 5
 *
 * @copyright  ContaoCommunityAlliance 2013
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    Composer
 * @license    LGPLv3
 * @filesource
 */

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Installer;
use ContaoCommunityAlliance\Contao\Composer\Runtime;
use ContaoCommunityAlliance\Contao\Composer\Util\Messages;

/**
 * Class ClearComposerCacheController
 */
class ClearComposerCacheController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        if (Runtime::clearComposerCache()) {
            Messages::addConfirmation($GLOBALS['TL_LANG']['composer_client']['composerCacheCleared']);
        }

        $this->redirect('contao/main.php?do=composer');
    }
}
