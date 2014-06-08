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
		$stack    = str_split($output);
		$sequence = array();
		$hasColor = 0;

		for ($index = 0; $index < count($stack); $index++) {
			$char = $stack[$index];

			// carriage return
			if ($char == "\r") {
				continue;
			}

			// backspace
			if ($char == "\x08") {
				// remove style-items from the sequences
				$char = end($sequence);
				while ($char !== false) {
					if (strlen($char) > 1 || $char == "\n") {
						array_pop($sequence);
					}
					else {
						break;
					}
					$char = end($sequence);
				}
				// remote the last char item from the sequence
				array_pop($sequence);
				continue;
			}

			if ($char == "\x1B") {
				if (static::parseColor($stack, $index, $sequence, $hasColor)) {
					continue;
				}
			}

			$sequence[] = $char;

			if (preg_match('~contao-community-alliance/translator$~', implode('', $sequence))) {
				xdebug_break();
			}
		}

		if ($hasColor) {
			$sequence[] = '</span>';
		}

		return implode('', $sequence);
	}

	static protected function parseColor(array $stack, &$index, array &$sequence, &$hasColor)
	{
		if ($stack[$index + 1] == '[') {
			$pre     = '';
			$post    = '';
			$isStyle = true;

			// parse color style
			for ($next = $index + 2; $next < count($stack); $next++) {
				if (is_numeric($stack[$next])) {
					if ($isStyle) {
						$pre .= $stack[$next];
					}
					else {
						$post .= $stack[$next];
					}
				}
				else if ($stack[$next] == ';') {
					$isStyle = false;
				}
				else if ($stack[$next] == 'm') {
					if ($isStyle) {
						$post = $pre;
						$pre  = 0;
					}
					break;
				}
				else {
					return false;
				}
			}

			$light = (bool) (int) $pre;
			$code  = (int) $post;
			$color = false;

			// pastel colors
			switch ($code) {
				// black
				case 30:
					$color = $light
						? '#709080'
						: '#3F3F3F';
					break;

				// red
				case 31:
					$color = $light
						? '#DCA3A3'
						: '#705050';
					break;

				// green
				case 32:
					$color = $light
						? '#72D5A3'
						: '#60B48A';
					break;

				// yellow / brown
				case 33:
					$color = $light
						? '#F0DFAF'
						: '#DFAF8F';
					break;

				// blue
				case 34:
					$color = $light
						? '#94BFF3'
						: '#9AB8D7';
					break;

				// purple
				case 35:
					$color = $light
						? '#EC93D3'
						: '#DC8CC3';
					break;

				// cyan
				case 36:
					$color = $light
						? '#93E0E3'
						: '#8CD0D3';
					break;

				// white / gray
				case 37:
					$color = $light
						? '#FFFFFF'
						: '#DCDCCC';
					break;
			}

			if ($color || $hasColor) {
				$hasColor   = false;
				$sequence[] = '</span>';
			}

			if ($color) {
				$hasColor   = true;
				$sequence[] = sprintf('<span style="color:%s">', $color);
			}

			$index = $next;
			return true;
		}

		return false;
	}
}
