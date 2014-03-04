<?php
/**
Implements the base functionality of every OpenLayers object.  
*/
abstract class GIS_OpenLayers_Object {
    /**
    Paint the Javascript necessary to create this object. This could be, but is likely not, as simple as:
    \code
    echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
var {$this->jsVariable} = new OpenLayers.{$this->implementation}({$this->attributes->getAsJSON()});
//]]>
</script>
EOHTML;
    \endcode
    */
    abstract public function paint();
    // {{{ __construct()

    /**
    Prefix is a short string that will prefix Javascript variables; make this representative of whatever this object represents.
    For example, if the implementation is 'Map', a good prefix is 'map'.

    Example implementation: 'Bounds', 'Point', 'Projection', 'Layer.Google', and 'Layer.WMS'
    
    \seealso http://dev.openlayers.org/releases/OpenLayers-2.7/doc/apidocs/files/OpenLayers-js.html
    */
    public function __construct($prefix, $implementation, $attributes = array ()) {
        // store our implementation and attributes
        if (is_string($implementation) && 0 < strlen($implementation = trim($implementation))) {
            $this->implementation = $implementation;
        } else {
            throw new InvalidArgumentException('Object implementation must be non-empty string');
        }
        $this->attributes = new Javascript_Object($attributes);

        // create a unique javascript variable to hold this object
        $this->jsVariable = uniqid($prefix);
    }

    // }}}
    // {{{ setJavascriptVariable()

    /**
    Set the Javascript variable that holds this object.
    */
    public function setJavascriptVariable($jsVariable) {
        if (is_string($jsVariable) && 0 < strlen($jsVariable = trim($jsVariable))) {
            $this->jsVariable = $jsVariable;
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (jsVariable) must be non-empty string');
        }
    }

    // }}}
    // {{{ getJavascriptVariable()

    /**
    Return the Javascript variable holding this object.
    */
    public function getJavascriptVariable() {
        return $this->jsVariable;
    }

    // }}}
    // {{{ [static] isLatitude()

    /**
    Is the given value a valid latitude?
    */
    public static function isLatitude($latitude) {
        return (is_numeric($latitude) && -85.0511 <= (float)$latitude && (float)$latitude <= 85.0511 ? true : false);
    }

    // }}}
    // {{{ [static] isLongitude()

    /**
    Is the given value a valid longitude?
    */
    public static function isLongitude($longitude) {
        return (is_numeric($longitude) && -180 <= (float)$longitude && (float)$longitude <= 180 ? true : false);
    }

    // }}}
    // {{{ [static] log()

    /**
    Send a log message out to the OpenLayers console.
    */
    public function log($message, $method = 'info') {
        static $methods = array ('log','debug','info','warn','error');
        if (is_string($message) && in_array($method, $methods)) {
            $message = self::getAsJSON($message);
            echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
OpenLayers.Console.{$method}({$message});
//]]>
</script>

EOHTML;

        } else {
            throw new InvalidArgumentException(sprintf(
                'First parameter (message) must be a string; second parameter must be one of %s',
                implode(',', $methods)
            ));
        }
    }

    // }}}

    /* protected API */
    // {{{ $implementation

    /**
    The class (in the OpenLayers.* namespace) that implements this object
    */
    protected $implementation = null;

    // }}}
    // {{{ $attributes

    /**
    The attributes to pass into Javascript for this object.
    */
    protected $attributes = null;

    // }}}
    // {{{ $jsVariable

    /**
    The Javascript variable holding this OpenLayers object.
    */
    protected $jsVariable = null;

    // }}}
    // {{{ [static] getAsJSON()

    /**
    Get a value encoded in JSON.  You should call this on any value you stick into Javascript to properly escape quotes, handle
    boolean and null values, etc.
    
    This is provided as a method so that you can call it inline in a string, eg "{$this->getAsJSON($variable)}"
    */
    static protected function getAsJSON($value) {
        return Javascript_Object::getEncodedAsJSON($value);
    }

    // }}}
}

?>
