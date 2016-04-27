<?php

namespace ContaoCommunityAlliance\Contao\Composer\Test;

use ContaoCommunityAlliance\Contao\Composer\Runtime;

class RuntimeTest extends \PHPUnit_Framework_TestCase
{
    public function providerReadComposerDevWarningTime()
    {
        return array(
            array(false, ''),
            array(1234567890, 'define(\'COMPOSER_DEV_WARNING_TIME\', 1234567890);'),
            array(1234567890, 'const RELEASE_DATE = \'' . date('Y-m-d H:i:s', 1234567890) . '\';'),
        );
    }

    /**
     * Test the readComposerDevWarningTime
     *
     * @return boolean
     *
     * @dataProvider providerReadComposerDevWarningTime
     */
    public function testReadComposerDevWarningTime($expected, $testArg)
    {
        $tempFile = tmpfile();
        fwrite($tempFile, str_repeat(PHP_EOL, 10) . $testArg . str_repeat(PHP_EOL, 10));
        fseek($tempFile, 0);
        $actual = Runtime::readComposerDevWarningTimeFromStream($tempFile);
        fclose($tempFile);

        $this->assertEquals($expected, $actual);
    }
}
