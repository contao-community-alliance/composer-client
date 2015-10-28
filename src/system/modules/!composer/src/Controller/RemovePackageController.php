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
use Composer\Json\JsonFile;

/**
 * Class RemovePackageController
 */
class RemovePackageController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        $removeNames =
            $input->post('packages') ? explode(',', $input->post('packages')) : array($input->post('remove'));

        // filter undeletable packages
        $removeNames = array_filter(
            $removeNames,
            function ($removeName) {
                return !in_array($removeName, InstalledController::$UNDELETABLE_PACKAGES);
            }
        );

        // skip empty
        if (empty($removeNames)) {
            $this->redirect('contao/main.php?do=composer');
        }

        // make a backup
        copy(TL_ROOT . '/' . $this->configPathname, TL_ROOT . '/' . $this->configPathname . '~');

        // update requires
        $json   = new JsonFile(TL_ROOT . '/' . $this->configPathname);
        $config = $json->read();
        if (!array_key_exists('require', $config)) {
            $config['require'] = array();
        }
        foreach ($removeNames as $removeName) {
            unset($config['require'][$removeName]);
        }
        $json->write($config);

        $_SESSION['TL_INFO'][] = sprintf(
            $GLOBALS['TL_LANG']['composer_client']['removeCandidate'],
            implode(', ', $removeNames)
        );

        $_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

        $this->redirect('contao/main.php?do=composer');
    }
}
