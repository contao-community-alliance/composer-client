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
