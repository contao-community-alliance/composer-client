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

namespace ContaoCommunityAlliance\Contao\Composer;

/**
 * Class Client
 *
 * Composer client integration.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Client extends \System
{

    /**
     * @var Client
     */
    protected static $instance;

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected $inactiveModulesOptionsCallback = null;

    public function setInactiveModulesOptionsCallback($inactiveModulesOptionsCallback)
    {
        $this->inactiveModulesOptionsCallback = $inactiveModulesOptionsCallback;
        return $this;
    }

    public function getInactiveModulesOptionsCallback()
    {
        return $this->inactiveModulesOptionsCallback;
    }

    public function getModules()
    {
        $callback = $this->inactiveModulesOptionsCallback;
        $this->import($callback[0]);
        $modules = $this->$callback[0]->$callback[1]();

        if (isset($modules['repository'])) {
            $modules['repository'] = sprintf(
                '<span style="text-decoration:line-through">%s</span> <span style="color:#f00">%s</span>',
                $modules['repository'],
                $GLOBALS['TL_LANG']['MSG']['disabled_by_composer']
            );
        }

        return $modules;
    }

    public function disableOldClientHook()
    {
        // disable the repo client
        $reset           = false;
        $activeModules   = $this->Config->getActiveModules();
        $inactiveModules = deserialize($GLOBALS['TL_CONFIG']['inactiveModules']);

        if (in_array('repository', $activeModules)) {
            $inactiveModules[] = 'repository';
            $skipFile          = new \File('system/modules/repository/.skip');
            $skipFile->write('Remove this file to enable the module');
            $skipFile->close();
            $reset = true;
        }
        if ($reset) {
            $this->Config->update("\$GLOBALS['TL_CONFIG']['inactiveModules']", serialize($inactiveModules));
            $this->reload();
        }
        unset($GLOBALS['TL_HOOK']['loadLanguageFile']['composer']);
    }
}
