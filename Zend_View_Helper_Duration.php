<?php

/**
 * Formats a time representing a duration into an English representation.
 * 
 * @code
 * // Show 67 minutes as fractional hour formatted to 2 decimal places:
 * $duration = new Zend_Measure_Time(67, Zend_Measure_Time::MINUTE);
 * echo $this->
 *     duration($duration)->
 *     setFormat('%.2h')->
 *     setResolution(Zend_Measure_Time::HOUR)->
 *     setPrecision(2)
 * ;
 * @endcode
 *
 * @code
 * // Show instead as "H hours M minutes S seconds":
 * $duration = new Zend_Measure_Time(67, Zend_Measure_Time::MINUTE);
 * echo $this->
 *     duration($duration)->
 *     setFormat('%h hours %m minutes %s seconds')
 * ;
 * @endcode
 *
 * @seealso http://www.zfsnippets.com/snippets/view/id/114/zendviewhelperduration
 * @seealso http://www.ideacode.com/content/super-charged-view-helpers
 */
class Zend_View_Helper_Duration extends Zend_View_Helper_Abstract
{
    /**
     * @var Zend_Measure_Time $duration The duration you want to format.
     */
    public function duration(\Zend_Measure_Time $duration)
    {
        $this->duration = $duration->setType(\Zend_Measure_Time::SECOND);
        return clone $this;
    }
 
    /**
     * Define a printf()-style string into which to put the parsed components.
     * Use these replacements:
     * - %y is the number of years in the duration
     * - %w is the number of weeks in the duration
     * - %d is the number of days in the duration, after subracting out any weeks or years specified
     * - %h is the number of hours in the duration, after subtracting out any days, weeks, or years specified
     * - %m is the number of minutes in the duration, after subtracting out any hours, days, weeks, or years specified
     * - %s is the number of seconds in the duration, after subtracting out any minutes, hours, days, weeks, or years specified
     * - %D is the duration itself as a whole number, in the given resolution and precision
     *
     * Default is "%D"
     *
     * @var SplString $format The format to use.
     */
    public function setFormat(\SplString $format)
    {
        $this->format = $format;
        return $this;
    }
 
    /**
     * Set the resolution of the %D format.
     * Can be one of Zend_Measure_Time constants.
     *
     * Default is Zend_Measure_Time::SECOND
     *
     * @var int $resolution The resolution to use.
     */
    public function setResolution($resolution)
    {
        $this->size = array_search($resolution, $this->resolutions);
        if (false === $this->size) {
            $this->size = 1;
        }
        return $this;
    }
 
    /**
     * The precision to show in the %D format.
     *
     * Default is 0
     *
     * @var SplInt $precision The precision to use.
     */
    public function setPrecision(\SplInt $precision)
    {
        $this->precision = $precision;
        return $this;
    }
 
    /**
     * Using the given format, resolution, and precision, render the duration in the given format.
     */
    public function render()
    {
        // map the given sizes to the corresponding format specifier (eg Zend_Measure_Time::YEAR = 'y')
        $mapSize2Specifier = array_combine(array_keys($this->resolutions), array ('y','w','d','h','m','s'));

        // the string into which we will put the values, initially the format itself
        $output = $this->format;

        // the duration, broken up into amounts for each resolution (# of years, # of weeks, etc.)
        $chunks = $this->chunk();
 
        // replace each specifier in the format with the corresponding value from the duration,
        // accounting for precision as necessary
        foreach ($mapSize2Specifier as $size => $specifier) {
            // look for the specifier, so long as it's given in printf() format 
            $count = preg_match("/%([^ywdhmsD]*)$specifier/", $output, $matches);
            if (0 < $count) {
                $letter = ($size === $this->size && 0 < $this->precision ? 'f' : 'd');
                $output = str_replace($matches[0], sprintf('%' . $matches[1] . $letter, $chunks[$size]), $output);
            }
        }
 
        // if we have the duration (D) specifier, replace that one last
        $count = preg_match("/%([^ywdhmsD]*)D/", $output, $matches);
        if (0 < $count) {
            $output = str_replace($matches[0], sprintf('%' . $matches[1] . 'd', (int)$this->duration->getValue()), $output);
        }
 
        return $output;
    }
 
    /**
     * Convenience method to dump this view helper to a string.
     */
    public function __toString()
    {
        return $this->render();
    }


    /* protected API */
    protected $duration;
    protected $format = '%D';
    protected $size = 1;
    protected $precision = 0;
    protected $resolutions = array (
        31449600 => Zend_Measure_Time::YEAR,
        604800 => Zend_Measure_Time::WEEK,
        86400 => Zend_Measure_Time::DAY,
        3600 => Zend_Measure_Time::HOUR,
        60 => Zend_Measure_Time::MINUTE,
        1 => Zend_Measure_Time::SECOND,
    );

    /**
     * Breaks up the duration into pieces of each size in the resolutions array.
     */
    protected function chunk()
    {
        $chunks = array_combine(array_keys($this->resolutions), array (0,0,0,0,0,0));
        $remainder = (int)$this->duration->getValue();
        foreach ($this->resolutions as $size => $resolution) {
            if ($seconds <= $remainder) {
                $chunks[$size] = $value = floor($remainder / $size);
                $remainder -= ($size * $value);
 
            } else {
                $chunks[$size] = 0;
            }
 
            if ($size === $this->size || 1 === $size) {
                $chunks[$size] += round($remainder / $size, $this->precision);
                break;
            }
        }
 
        return $chunks;
    }
}

