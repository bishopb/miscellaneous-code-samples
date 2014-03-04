<?php
/**
This class implements the majority of the Google Maps layer functionality.

NOTE: This class is intentionally defined abstract without any abstract methods: we don't want this class instantiated; rather,
we want the concrete classes derived from this class to be instantiated.  Setting the constructor to protected is not
sufficient, because it must remain public as defined in our parent class.

\seealso http://docs.openlayers.org/library/spherical_mercator.html
*/
abstract class GIS_OpenLayers_Map_Layer_Google
       extends GIS_OpenLayers_Map_Layer
    implements GIS_OpenLayers_Map_Layer_iSphericalMercator {
    /* public API */
    // {{{ setAPIKey()

    public function setAPIKey($apiKey) {
        if (is_string($apiKey) && 0 < strlen($apiKey)) {
            $this->apiKey = $apiKey;
            return $this;
        } else {
            throw new InvalidArgumentException('API key must be non-empty string');
        }
    }

    // }}}
    // {{{ getAPIKey()

    public function getAPIKey() {
        return $this->apiKey;
    }

    // }}}
    // {{{ setUseSecure()

    public function setUseSecure($useSecure) {
        if (is_scalar($useSecure)) {
            $this->useSecure = (bool)$useSecure;
            return $this;
        } else {
            throw new InvalidArgumentException('Use secure must be boolean-equivalent');
        }
    }

    // }}}
    // {{{ getUseSecure()

    public function getUseSecure() {
        return $this->useSecure;
    }

    // }}}
    // {{{ paint()

    /**
    Echo the code necessary to create this layer.
    */
    public function paint() {
        // output where we can find Google stuff
        if (self::$gmapsNeedsInitialization) {
            // panic if we don't have a key
            if (null === $this->apiKey) {
                throw new LogicException('Must call setAPIKey() prior to calling paint()');
            }

            // BugzID: 2006
            $scheme = 'http' . ($this->useSecure ? 's' : '');
            echo <<<EOHTML
<script type='text/javascript' src="{$scheme}://www.google.com/jsapi?key={$this->apiKey}"></script>
<script type='text/javascript'>
if (google && google.load) {
   google.load('maps', '2.x', {'other_params':'sensor=false'});
} else {
   OpenLayers.Console.error("Google API did not load using apiKey: {$this->apiKey}");
}
</script>
EOHTML;
            self::$gmapsNeedsInitialization = false;
        }

        // paint our parent
        parent::paint();
    }

    // }}}

    /* friend API */
    // {{{ __construct()

    public function __construct($typecode) {
        // create our parent
        parent::__construct('Layer.Google', array ());

        // note that we are using Spherical Mercator projection
        $this->attributes->set('sphericalMercator', true);
        $this->attributes->setLiteral('maxExtent', 'new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34)');
        $this->attributes->setLiteral('projection', 'new OpenLayers.Projection("EPSG:900913")');
        $this->attributes->set('units', 'm');
        $this->attributes->set('maxResolution', 156543.0339);

        // make sure we have a type
        if (is_string($typecode) && 0 < strlen($typecode)) {
            $this->attributes->setLiteral('type', $typecode);
        } else {
            throw new InvalidArgumentException('First parameter (typecode) must be non-empty string');
        }
    }

    // }}}

    /* protected API */
    // {{{ $apiKey

    protected $apiKey = null;

    // }}}
    // {{{ $useSecure

    protected $useSecure = false;

    // }}}

    /* private API */
    // {{{ static $gmapsNeedsInitialization

    /**
    Has the Google Maps code been initialized?
    */
    static private $gmapsNeedsInitialization = true;

    // }}}
}

?>
