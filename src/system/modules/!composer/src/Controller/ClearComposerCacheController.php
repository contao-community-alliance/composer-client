<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Installer;
use ContaoCommunityAlliance\Contao\Composer\Runtime;

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
            $_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['composer_client']['composerCacheCleared'];
        }

        $this->redirect('contao/main.php?do=composer');
    }
}
