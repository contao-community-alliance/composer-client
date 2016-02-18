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
use Composer\Util\ConfigValidator;
use ContaoCommunityAlliance\Contao\Composer\Util\Messages;

/**
 * Class ExpertsEditorController
 */
class ExpertsEditorController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        $configFile = new \File($this->configPathname);

        if ($input->post('save')) {
            $tempPathname = $this->configPathname . '~';
            $tempFile     = new \File($tempPathname);

            $config = $input->postRaw('config');
            $config = html_entity_decode($config, ENT_QUOTES, 'UTF-8');

            $tempFile->write($config);
            $tempFile->close();

            $validator = new ConfigValidator($this->io);
            list($errors, $publishErrors, $warnings) = $validator->validate(TL_ROOT . '/' . $tempPathname);

            if (!$errors && !$publishErrors) {
                Messages::addConfirmation($GLOBALS['TL_LANG']['composer_client']['configValid']);
                $this->import('Files');
                $this->Files->rename($tempPathname, $this->configPathname);
            } else {
                $tempFile->delete();
                $_SESSION['COMPOSER_EDIT_CONFIG'] = $config;

                if ($errors) {
                    foreach ($errors as $message) {
                        Messages::addError('Error: ' . $message);
                    }
                }

                if ($publishErrors) {
                    foreach ($publishErrors as $message) {
                        Messages::addError('Publish error: ' . $message);
                    }
                }
            }

            if ($warnings) {
                foreach ($warnings as $message) {
                    Messages::addWarning('Warning: ' . $message);
                }
            }

            $this->reload();
        }

        if (isset($_SESSION['COMPOSER_EDIT_CONFIG'])) {
            $config = $_SESSION['COMPOSER_EDIT_CONFIG'];
            unset($_SESSION['COMPOSER_EDIT_CONFIG']);
        } else {
            $config = $configFile->getContent();
        }

        $template           = new \BackendTemplate('be_composer_client_editor');
        $template->composer = $this->composer;
        $template->config   = $config;
        return $template->parse();
    }
}
