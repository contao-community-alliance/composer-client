<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Config;
use Composer\Installer;

/**
 * Class ToolsController
 */
class ToolsController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        $template           = new \BackendTemplate('be_composer_client_tools');
        $template->composer = $this->composer;
        return $template->parse();
    }
}
