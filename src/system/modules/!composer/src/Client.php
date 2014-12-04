<?php

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

        foreach (array('repository', 'rep_base', 'rep_client') as $module) {
            if (isset($modules[$module])) {
                $modules[$module] = sprintf(
                    '<span style="text-decoration:line-through">%s</span> <span style="color:#f00">%s</span>',
                    $modules[$module],
                    $GLOBALS['TL_LANG']['MSG']['disabled_by_composer']
                );
            }
        }

        return $modules;
    }

    public function disableOldClientHook()
    {
        // disable the repo client
        $reset           = false;
        $activeModules   = $this->Config->getActiveModules();
        $inactiveModules = deserialize($GLOBALS['TL_CONFIG']['inactiveModules']);

        if (in_array('rep_base', $activeModules)) {
            $inactiveModules[] = 'rep_base';
            $reset             = true;
        }
        if (in_array('rep_client', $activeModules)) {
            $inactiveModules[] = 'rep_client';
            $reset             = true;
        }
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
