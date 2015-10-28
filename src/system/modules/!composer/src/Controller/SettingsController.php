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

use Composer\Config;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Package\RootPackage;

/**
 * Class SettingsController
 */
class SettingsController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public function handle(\Input $input)
    {
        /** @var RootPackage $rootPackage */
        $rootPackage = $this->composer->getPackage();
        /** @var Config $config */
        $config = $this->composer->getConfig();

        $minimumStability = new \SelectMenu(
            array(
                'id'          => 'minimum-stability',
                'name'        => 'minimum-stability',
                'label'       => $GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability'][0],
                'description' => $GLOBALS['TL_LANG']['composer_client']['widget_minimum_stability'][1],
                'options'     => array(
                    array('value' => 'stable', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_stable']),
                    array('value' => 'RC', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_rc']),
                    array('value' => 'beta', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_beta']),
                    array('value' => 'alpha', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_alpha']),
                    array('value' => 'dev', 'label' => $GLOBALS['TL_LANG']['composer_client']['stability_dev']),
                ),
                'value'       => $rootPackage->getMinimumStability(),
                'class'       => 'minimum-stability',
                'required'    => true,
            )
        );
        $preferStable     = new \CheckBox(
            array(
                'id'          => 'prefer-stable',
                'name'        => 'prefer-stable',
                'label'       => $GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable'][0],
                'description' => $GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable'][1],
                'options'     => array(
                    array(
                        'value' => '1',
                        'label' => $GLOBALS['TL_LANG']['composer_client']['widget_prefer_stable'][0]
                    ),
                ),
                'value'       => $rootPackage->getPreferStable(),
                'class'       => 'prefer-stable',
                'required'    => true,
            )
        );
        $preferredInstall = new \SelectMenu(
            array(
                'id'          => 'preferred-install',
                'name'        => 'preferred-install',
                'label'       => $GLOBALS['TL_LANG']['composer_client']['widget_preferred_install'][0],
                'description' => $GLOBALS['TL_LANG']['composer_client']['widget_preferred_install'][1],
                'options'     => array(
                    array('value' => 'source', 'label' => $GLOBALS['TL_LANG']['composer_client']['install_source']),
                    array('value' => 'dist', 'label' => $GLOBALS['TL_LANG']['composer_client']['install_dist']),
                    array('value' => 'auto', 'label' => $GLOBALS['TL_LANG']['composer_client']['install_auto']),
                ),
                'value'       => $config->get('preferred-install'),
                'class'       => 'preferred-install',
                'required'    => true,
            )
        );

        $configGithubOauth = $config->get('github-oauth');

        $githubOauth = new \TextField(
            array(
                'id'          => 'github-oauth',
                'name'        => 'github-oauth',
                'label'       => $GLOBALS['TL_LANG']['composer_client']['widget_github_oauth'][0],
                'description' => $GLOBALS['TL_LANG']['composer_client']['widget_github_oauth'][1],
                'value'       => $configGithubOauth['github.com'],
                'class'       => 'github-oauth'
            )
        );

        $discardChanges = new \SelectMenu(
            array(
                'id'          => 'discard-changes',
                'name'        => 'discard-changes',
                'label'       => $GLOBALS['TL_LANG']['composer_client']['widget_discard_changes'][0],
                'description' => $GLOBALS['TL_LANG']['composer_client']['widget_discard_changes'][1],
                'options'     => array(
                    array('value' => '', 'label' => $GLOBALS['TL_LANG']['composer_client']['discard_changes_no']),
                    array('value' => '1', 'label' => $GLOBALS['TL_LANG']['composer_client']['discard_changes_yes']),
                    array(
                        'value' => 'stash',
                        'label' => $GLOBALS['TL_LANG']['composer_client']['discard_changes_stash']
                    ),
                ),
                'value'       => (string) $config->get('discard-changes'),
                'class'       => 'github-oauth'
            )
        );

        if ($input->post('FORM_SUBMIT') == 'tl_composer_settings') {
            $doSave = false;
            $json   = new JsonFile(TL_ROOT . '/' . $this->configPathname);
            $config = $json->read();

            $minimumStability->validate();
            $preferStable->validate();
            $preferredInstall->validate();
            $githubOauth->validate();
            $discardChanges->validate();

            if (!$minimumStability->hasErrors()) {
                $config['minimum-stability'] = $minimumStability->value;
                $doSave                      = true;
            }

            if (!$preferStable->hasErrors()) {
                $config['prefer-stable'] = (bool) $preferStable->value;
                $doSave                  = true;
            }

            if (!$preferredInstall->hasErrors()) {
                $config['config']['preferred-install'] = $preferredInstall->value;
                $doSave                                = true;
            }

            if (!$githubOauth->hasErrors()) {
                if ($githubOauth->value) {
                    $config['config']['github-oauth']['github.com'] = $githubOauth->value;
                } else {
                    unset($config['config']['github-oauth']['github.com']);

                    if (empty($config['config']['github-oauth'])) {
                        unset($config['config']['github-oauth']);
                    }
                }
                $doSave = true;
            }

            if (!$discardChanges->hasErrors()) {
                if ($discardChanges->value) {
                    $config['config']['discard-changes'] = $discardChanges->value == 'stash'
                        ? 'stash'
                        : (bool) $discardChanges->value;
                } else {
                    unset($config['config']['discard-changes']);
                }
                $doSave = true;
            }

            if ($doSave) {
                // make a backup
                copy(TL_ROOT . '/' . $this->configPathname, TL_ROOT . '/' . $this->configPathname . '~');

                // update config file
                $json->write($config);
            }

            $this->redirect('contao/main.php?do=composer&settings=dialog');
        }

        $template                   = new \BackendTemplate('be_composer_client_settings');
        $template->composer         = $this->composer;
        $template->minimumStability = $minimumStability;
        $template->preferStable     = $preferStable;
        $template->preferredInstall = $preferredInstall;
        $template->githubOauth      = $githubOauth;
        $template->discardChanges   = $discardChanges;
        return $template->parse();
    }
}
