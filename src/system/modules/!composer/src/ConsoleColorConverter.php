<?php

namespace ContaoCommunityAlliance\Contao\Composer;

/**
 * Class ConsoleColorConverter
 *
 * Parse some command line output and generate proper html representation out of it.
 */
class ConsoleColorConverter
{
    /**
     * Color lookup table.
     *
     * @var array
     */
    protected static $ANSICOLORS = array(
        array(0, 0, 0, 255),  // Black
        array(170, 0, 0, 255),  // Red
        array(0, 170, 0, 255),  // Green
        array(170, 85, 0, 255),  // Yellow
        array(0, 0, 170, 255),  // Blue
        array(170, 0, 170, 255),  // Magenta
        array(0, 170, 170, 255),  // Cyan
        array(170, 170, 170, 255),  // White
        // Bright:
        array(85, 85, 85, 255),
        array(255, 85, 85, 255),
        array(85, 255, 85, 255),
        array(255, 255, 85, 255),
        array(85, 85, 255, 255),
        array(255, 85, 255, 255),
        array(85, 255, 255, 255),
        array(255, 255, 255, 255)
    );

    /**
     * The currently active styles.
     *
     * @var array
     */
    protected $styles = array();

    /**
     * Convert the current styling into a CSS string.
     *
     * @return string
     */
    protected function getStyles()
    {
        $result = '';
        foreach ($this->styles as $type => $style) {
            $result .= $type . ': ' . implode(' ', $style) . ';';
        }

        return $result;
    }

    /**
     * Set a css value for a flag.
     *
     * Returns true when there was a modification, false otherwise.
     *
     * @param string $flag  The name of the flag to set.
     *
     * @param string $value The value to set.
     *
     * @return bool
     */
    protected function setFlag($flag, $value)
    {
        if (!isset($this->styles[$flag][$value]) || $this->styles[$flag][$value] !== $value) {
            $this->styles[$flag][$value] = $value;

            return true;
        }

        return false;
    }

    /**
     * Reset a value for a flag.
     *
     * Returns true when there was a modification, false otherwise.
     *
     * @param string $flag  The name of the flag to unset a value for.
     *
     * @param string $value The value to unset (defaults to null in which case all values of the flag will get reset).
     *
     * @return bool
     */
    protected function unsetFlag($flag, $value = null)
    {
        if ($value === null) {
            if (isset($this->styles[$flag])) {
                unset($this->styles[$flag]);

                return true;
            }
        } else {
            if (isset($this->styles[$flag][$value])) {
                unset($this->styles[$flag][$value]);
                if (empty($this->styles[$flag])) {
                    unset($this->styles[$flag]);
                }

                return true;
            }
        }

        return false;
    }

    protected function getColor($index, $highIntensity = false)
    {
        $color = $highIntensity ? self::$ANSICOLORS[$index + 8] : self::$ANSICOLORS[$index];
        return sprintf('rgba(%s,%s,%s,%s)', $color[0], $color[1], $color[2], $color[3] / 255);
    }

    /**
     * Decode the parameter and update the style array with the new information.
     *
     * @param string $parameter The parameter.
     *
     * @return bool True when the parameter changed the style array, false otherwise.
     */
    protected function decodeParameter($parameter)
    {
        $parsedParameter = intval($parameter);
        switch ($parsedParameter) {
            case 0:
                // Reset / Normal
                $this->styles = array();
                return true;
            case 1:
                // Bold or increased intensity
                return $this->setFlag('font-weight', 'bold');
            case 2:
                // Faint (decreased intensity) Not widely supported.
                return $this->setFlag('font-weight', 'lighter');
            case 3:
                // Italic: on Not widely supported. Sometimes treated as inverse.
                return $this->setFlag('font-style', 'italic');
            case 4: // Underline: Single
                return $this->setFlag('text-decoration', 'underline');
            case 5: // Blink: Slow  (less than 150 per minute)
            case 6: // Blink: Rapid (MS-DOS ANSI.SYS; 150+ per minute; not widely supported)
                return $this->setFlag('text-decoration', 'blink');
            case 7:
                // Image: Negative (inverse or reverse; swap foreground and background).
                return $this->setFlag('-webkit-filter', 'invert(100%)')
                       || $this->setFlag('filter', 'invert(100%)');
            case 8:
                // Conceal (Not widely supported).
                return $this->setFlag('visibility', 'hidden');
            case 9:
                // Crossed-out (Characters legible, but marked for deletion. Not widely supported).
                return $this->setFlag('text-decoration', 'line-through');
            case 10:
                // Primary(default) font.
                return $this->setFlag('font-family', 'inherit');
            case 11:
            case 12:
            case 13:
            case 14:
            case 15:
            case 16:
            case 17:
            case 18:
            case 19:
                // n-th alternate font
                // TODO: determine good font-names here.
                return $this->setFlag('font-family', '');
            case 20:
                // Fraktur hardly ever supported.
                // @import url(
                //   'http://fonts.googleapis.com/css?family=UnifrakturMaguntia|UnifrakturCook:bold&subset=all'
                // );
                return $this->setFlag('font-family', 'UnifrakturMaguntia')
                       || $this->setFlag('text-rendering', 'optimizeLegibility')
                       || $this->setFlag('-webkit-font-feature-settings', '"liga", "rlig", "ccmp"')
                       || $this->setFlag('-ms-font-feature-settings', '"liga", "rlig", "ccmp"');
            case 21:
                // Bold: off or Underline: Double.
                // (Bold off not widely supported; double underline hardly ever supported).
                return $this->unsetFlag('font-weight', 'bold');
            case 22:
                // Normal color or intensity (Neither bold nor faint).
                return $this->unsetFlag('font-weight', 'bold')
                       || $this->unsetFlag('font-weight', 'lighter');
            case 23:
                // Not italic, not Fraktur.
                return $this->unsetFlag('font-style', 'italic')
                       || $this->unsetFlag('font-family', 'UnifrakturMaguntia')
                       || $this->unsetFlag('text-rendering', 'optimizeLegibility')
                       || $this->unsetFlag('-webkit-font-feature-settings', '"liga", "rlig", "ccmp"')
                       || $this->unsetFlag('-ms-font-feature-settings', '"liga", "rlig", "ccmp"');
            case 24:
                // Underline: None (Not singly or doubly underlined)
                return $this->unsetFlag('text-decoration', 'underline')
                       || $this->unsetFlag('text-decoration', 'double');
            case 25:
                // Blink: off
            case 26:
                // Reserved.
                return false;
            case 27:
                // Image: Positive.
                return $this->unsetFlag('-webkit-filter', 'invert(100%)')
                       || $this->unsetFlag('filter', 'invert(100%)');
            case 28:
                // Reveal (conceal off)
                return $this->unsetFlag('visibility', 'hidden');
            case 29:
                // Not crossed out
                return $this->unsetFlag('text-decoration', 'line-through');
            case 30:
            case 31:
            case 32:
            case 33:
            case 34:
            case 35:
            case 36:
            case 37:
                // Set text color (foreground).
                // 30 + x, where x is from the color table.
                return $this->setFlag('color', $this->getColor($parameter - 30));
            case 38:
                // Reserved for extended set foreground color
                // typical supported next arguments are 5;x where x is color index (0..255) or
                // 2;r;g;b where r,g,b are red, green and blue color channels (out of 255).
                // TODO: this needs the next parameter argument. We might need a stack for this then.
                return false;
            case 39:
                // Default text color (foreground) (implementation defined (according to standard)).
                return $this->unsetFlag('color');
            case 40:
                // Set background color
                // 40 + x, where x is from the color table.
                return $this->setFlag(
                    'background-color',
                    $this->getColor($parameter - 40)
                );
            case 48:
                // Reserved for extended set background color
                // typical supported next arguments are 5;x where x is color index (0..255) or
                // 2;r;g;b where r,g,b are red, green and blue color channels (out of 255).
                // TODO: this needs the next parameter argument. We might need a stack for this then.
                return false;
            case 49:
                // Default background color (implementation defined (according to standard)).
                return $this->unsetFlag('background-color');
            case 51:
                // Framed.
                return $this->setFlag('border-style', 'solid');
            case 52:
                // Encircled.
                return $this->setFlag('border-style', 'solid');
            // TODO: find a CSS alternative for encircled, border-radius comes into mind but with which px?
            case 53:
                // Overlined.
                return $this->setFlag('text-decoration', 'overline');
            case 54:
                // Not framed or encircled.
                return $this->unsetFlag('border-style');
            case 55:
                // Not overlined.
                return $this->unsetFlag('text-decoration', 'overline');
            case 56:
            case 57:
            case 58:
            case 59:
                // Reserved.
                return false;
            case 60:
                // ideogram underline or right side line (hardly ever supported).
                return $this->setFlag('text-decoration', 'underline');
            case 61:
                // ideogram double underline or double line on the right side (hardly ever supported).
                return $this->setFlag('text-decoration', 'underline')
                       || $this->setFlag('text-decoration', 'double');
            case 62:
                // ideogram overline or left side line (hardly ever supported).
                return $this->setFlag('text-decoration', 'overline');
            case 63:
                // ideogram double overline or double line on the left side (hardly ever supported).
                return $this->setFlag('text-decoration', 'overline')
                       || $this->setFlag('text-decoration', 'double');
            case 64:
                // ideogram stress marking (hardly ever supported).
                return false;
            case 65:
                // ideogram attributes off (hardly ever supported, reset the effects of all of 60–64).
                // TODO: this is not exactly reverting the commands as we would be in need to preserve the previous
                // values.
                return $this->unsetFlag('text-decoration', 'underline')
                       || $this->unsetFlag('text-decoration', 'double')
                       || $this->unsetFlag('text-decoration', 'overline');
            case 90:
            case 91:
            case 92:
            case 93:
            case 94:
            case 95:
            case 96:
            case 97:
                // Set foreground text color, high intensity (aixterm (not in standard)).
                return $this->setFlag('color', $this->getColor($parameter - 90, true));
            case 100:
            case 101:
            case 102:
            case 103:
            case 104:
            case 105:
            case 106:
            case 107:
                // Set background color, high intensity (aixterm (not in standard)).
                return $this->setFlag('background-color', $this->getColor($parameter - 100, true));
        }
        return false;
    }

    /**
     * Decode the parameters and update the style array with the new information.
     *
     * @param string $parameters The parameter.
     *
     * @return bool True when the parameters changed the style array, false otherwise.
     */
    protected function decodeParameters($parameters)
    {
        $result = false;
        foreach (explode(';', $parameters) as $parameter) {
            $result = $this->decodeParameter($parameter) || $result;
        }

        return $result;
    }

    /**
     * Parse all backspace sequences and interpret them as character deletion.
     *
     * @param string $buffer The buffer to parse.
     *
     * @return string
     */
    protected function parseBackspace($buffer)
    {
        preg_match_all('/([\x08]+)/im', $buffer, $matches, PREG_OFFSET_CAPTURE);
        $pos    = 0;
        $output = '';
        foreach (array_keys($matches[0]) as $i) {
            $match    = $matches[0][$i][0];
            $offset   = $matches[0][$i][1];
            $rollBack = strlen($match);
            $portion  = substr($buffer, $pos, $offset - $pos);
            $pos      = $offset + $rollBack;
            $output .= $portion;
            $output = substr($output, 0, -$rollBack);
        }
        $portion = substr($buffer, $pos);
        $output .= $portion;

        return $output;
    }

    public function parse($buffer)
    {
        // DOS treats Ctrl-Z (SUB) as EOF. Some ANSI artists hid their alias in a file by placing it after the EOF.
        // Therefore we cut it away.
        $buffer = explode(chr(0x1a), $buffer, 2);
        $buffer = $this->parseBackspace($buffer[0]);
        $buffer = htmlspecialchars($buffer);
        $buffer = str_replace(array("\r", "\n"), array('', '<br />'), $buffer);

        preg_match_all('/(\x1b\[)([0-9;]*?)m/im', $buffer, $matches, PREG_OFFSET_CAPTURE);

        $pos          = 0;
        $output       = '';
        $this->styles = array();
        $lastspan     = '';
        foreach (array_keys($matches[0]) as $i) {
            $match     = $matches[0][$i][0];
            $offset    = $matches[0][$i][1];
            $parameter = $matches[2][$i][0];
            $portion   = substr($buffer, $pos, $offset - $pos);
            $pos       = $offset + strlen($match);

            $output .= $portion;

            if ($this->decodeParameters($parameter)) {
                $newSpan = '<span style="' . $this->getStyles() . '">';
                if ($lastspan && ($lastspan != $newSpan)) {
                    $output .= '</span>';
                }

                if (count($this->styles)) {
                    $output .= $lastspan = $newSpan;
                } else {
                    $lastspan = $newSpan = '';
                }
            }
        }
        $portion = substr($buffer, $pos);
        $output .= $portion;
        if ($lastspan) {
            $output .= '</span>';
        }

        return $output;
    }
}
