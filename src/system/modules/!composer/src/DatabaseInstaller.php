<?php

namespace ContaoCommunityAlliance\Contao\Composer;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Version\VersionParser;
use Composer\Util\Filesystem;

/**
 * Class DatabaseInstaller
 */
class DatabaseInstaller
{
    public function sqlCompileCommands($return)
    {
        if (!$GLOBALS['TL_CONFIG']['composerRemoveRepositoryTables'] && is_array($return['DROP'])) {
            $return['DROP'] = array_filter(
                $return['DROP'],
                function ($sql) {
                    return strpos($sql, 'DROP TABLE `tl_repository_') === false;
                }
            );

            if (empty($return['DROP'])) {
                unset($return['DROP']);
            }
        }

        return $return;
    }
}
