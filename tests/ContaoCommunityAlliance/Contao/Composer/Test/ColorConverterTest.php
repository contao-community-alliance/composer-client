<?php

namespace ContaoCommunityAlliance\Contao\Composer\Test;

use ContaoCommunityAlliance\Contao\Composer\ConsoleColorConverter;

class ColorConverterTest extends \PHPUnit_Framework_TestCase
{
	public function testConverter()
	{
		$converter = new ConsoleColorConverter();

		$this->assertEquals(
			'Hello world!',
			$converter->parse("Hello world!")
		);

		$this->assertEquals(
			'<span style="color: rgb(0,0,0,255);">Hello world!</span>',
			$converter->parse("\x1b[30mHello world!")
		);

		$this->assertEquals(
			'<span style="color: rgb(0,0,0,255);font-weight: bold;">Hello world!</span>',
			$converter->parse("\x1b[30;1mHello world!")
		);

		$this->assertEquals(
			'<span style="color: rgb(0,0,0,255);font-weight: bold;">&quot;Hello world!&quot;</span>',
			$converter->parse("\x1b[30;1m\"Hello world!\"")
		);

		$this->assertEquals(
			'<span style="color: rgb(0,0,0,255);font-weight: bold;">&quot;Hello Dude!&quot;</span>',
			$converter->parse("\x1b[30;1m\"Hello world\x08\x08\x08\x08\x08Dude!\"")
		);

		$this->assertEquals(
			'<span style="color: rgb(0,0,0,255);font-weight: bold;">&quot;Hello?&quot;</span>',
			$converter->parse("\x1b[30;1m\"Hello world\x08\x08\x08\x08\x08Dude!\x08\x08\x08\x08\x08\x08?\"")
		);

		$this->assertEquals(
			'<span style="color: rgb(0,0,0,255);font-weight: bold;">&quot;Hello...&quot;</span>',
			$converter->parse("\x1b[30;1m\"Hello world\x08\x08\x08\x08\x08Du\nde!\x08\x08\x08\x08\x08\x08\x08...\"")
		);

		$input = "\x1b[32mLoading composer repositories with package information\x1b[39m
\x1b[32mUpdating dependencies\x1b[39m
  - Removing \x1b[32mtest/testpackage\x1b[39m (\x1b[33m1.7\x1b[39m)
  - Installing \x1b[32mtest/testpackage\x1b[39m (\x1b[33m1.8.1\x1b[39m)
    Downloading

  - installed \x1b[32m2\x1b[39m files
\x1b[32mWriting lock file\x1b[39m
\x1b[32mGenerating autoload files\x1b[39m";

		$output =
			'<span style="color: rgb(0,170,0,255);">Loading composer repositories with package information</span><br />' .
			'<span style="color: rgb(0,170,0,255);">Updating dependencies</span><br />' .
			'  - Removing <span style="color: rgb(0,170,0,255);">test/testpackage</span> (<span style="color: rgb(170,85,0,255);">1.7</span>)<br />' .
			'  - Installing <span style="color: rgb(0,170,0,255);">test/testpackage</span> (<span style="color: rgb(170,85,0,255);">1.8.1</span>)<br />' .
			'    Downloading<br />' .
			'<br />' .
			'  - installed <span style="color: rgb(0,170,0,255);">2</span> files<br />' .
			'<span style="color: rgb(0,170,0,255);">Writing lock file</span><br />' .
			'<span style="color: rgb(0,170,0,255);">Generating autoload files</span>';
		$this->assertEquals($output, $converter->parse($input));
	}
}
