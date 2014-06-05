<?php

namespace ContaoCommunityAlliance\Contao\Composer;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Version\VersionParser;
use Composer\Util\Filesystem;

/**
 * Class Utils
 */
class Utils
{
	static public function evaluateTerminalSequences($output)
	{
		// carriage return
		$output = preg_replace("~([\r\n]).*\r~", '$1', $output);

		// backspace
		do {
			$output = preg_replace("~[^\x08\r\n]\x08~", '', $output, -1, $count);
		}
		while ($count);

		return $output;
	}
}
