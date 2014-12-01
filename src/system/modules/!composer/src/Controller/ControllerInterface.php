<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Installer;

/**
 * Class ControllerInterface
 */
interface ControllerInterface
{
    /**
     * Handle the request and return the output html.
     *
     * @param \Input $input
     *
     * @return string
     */
    public function handle(\Input $input);
}
