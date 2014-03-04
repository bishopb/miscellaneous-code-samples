<?php
/**
Implements the base functionality for all OpenLayers-based GIS maps.  This includes add controls to the map as well as new
layers.  In production code, pass the URL to the production (compressed) OpenLayers javascript, such as:
\code
$map = new GIS_OpenLayers_Map('mapContainerID', 'GIS/OpenLayers/js/OpenLayers.js');
\endcode

NOTE: You must have a proper HTML 4.0 DOM set-up for OpenLayers to work.  That means you have, at a minimum,
<html><body>...</body></html>

USING
To use this class, you should create an instance of the map, adjust the map parameters to fit your needs, then add controls,
layers, and styles.  For example:
\code
// create the map
$map = new GIS_OpenLayers_Map('map', 'common/code/Basis/GIS/OpenLayers/js/lib/OpenLayers.js');
$map->setInitialLocation(35.787114, -78.674641, 12);
$map->setScales(array (100000,50000,24000,9600,6000,4000,2400,1200,600,480,240,120), 'ft');

// add controls
$map->newControl('PanZoomBar');
$map->newControl('ScaleLine');
$map->newControl('OverviewMap');
$style = $map->newStyle(array (
    'Point' => array (
        'pointRadius'   => 4,
        'graphicName'   => 'circle',
        'fillColor'     => 'white',
        'fillOpacity'   => 1,
        'strokeWidth'   => 1,
        'strokeOpacity' => 1,
        'strokeColor'   => '#333333',
    ),
    'Line' => array (
        'strokeWidth'     => 3,
        'strokeOpacity'   => 1,
        'strokeColor'     => '#666666',
        'strokeDashstyle' => 'dash',
    ),
    'Polygon' => array (
        'strokeWidth'   => 2,
        'strokeOpacity' => 1,
        'strokeColor'   => '#666666',
        'fillColor'     => 'white',
        'fillOpacity'   => 0.3,
    ),
), 'Symbolizer');
$control = $map->newControl('Measure');
$control->setPrecision(2);
$control->setDisplaySystem('metric');
$control->useStyle($style);

// add layers
$layer = $map->newBaseLayer(
    'Google_Streets',
    'ABQIAAAAkrs8qxUOSjRBDQbK3ZZetxQSAVsutOXDH3AdMtG8Cu-Rok1E6hT9qUlVXaEPOCusgNtlmj2nG3cckQ'
);
$layer = $map->newOverlay(
    'MapServer', 'https://www.example.com/cgi-bin/mapserv', 'mylayer', '/path/to/mapfile.map'
);
$layer->setProjection('init=epsg:900913');
$layer->setImageType('AGG');
$layer->setTitle('My Layer');
$layer->setVisibility(false);

// add markers
$icon1 = $map->newIcon('http://boston.openguides.org/markers/AQUA.png');
$icon2 = $map->newIcon('path/to/OpenLayers/img/marker-gold.png');

$layer = $map->newOverlay('Markers');
$layer->setTitle('My Markers');
$layer->placeMarker(35.78280, -78.66807, $icon1);
$layer->placeMarker(35.78656, -78.67074, $icon1);
$layer->placeMarker(35.78353, -78.67932, $icon2);

// draw the map
$map->paint();
\endcode

DEBUGGING
To debug using the Firebug console logger, throw this out BEFORE you paint this map:
\code
<script src="GIS/OpenLayers/js/lib/Firebug/firebug.js"></script>
\endcode
This must be a relative URL to the basis GIS/OpenLayers/ code.

Also, make sure your <head> tag looks like the following to get the console always opened:
\code
<head debug='true'>
\endcode

Finally, consider using the debugging version of the OpenLayers library, which will throw more relevant line numbers if
something's broken, because the code's not compressed.  For example, pass as the second argument to
GIS_OpenLayers_Map::__construct():
\code
$map = new GIS_OpenLayers_Map();
$map->setContainerID('map');
$map->setOpenLayersURL('GIS/OpenLayers/js/lib/OpenLayers.js');
\endcode


SOURCE STRUCTURE
Nearly everything is placed under, but not necessarily a descendent of, Map.  To see the hierarchy, use the tree command:
\code
tree -I js
\endcode

This will show all the PHP source associated with the ideacode OpenLayers API.  (sudo emerge tree if you need to.)


PROJECTIONS
By default, we assume an output projection of EPSG:4326 ("geographic lat/long").  You can override this by calling
\code
$map->attributes->setLiterla('displayProjection', 'new OpenLayers.Projection("EPSG:whatever")');
\endcode

If you add any layer that implements spherical mercator, then the projection transforms to EPSG:900913, and you need to handle
that because you must **make absolutely certain that you request the same projection in your layers.**


\seealso http://gis.ibbeck.de/ginfo/apps/OLExamples/Index/index.html
*/
class GIS_OpenLayers_Map extends GIS_OpenLayers_Object {
    // {{{ __construct()

    /**
    Create a new map object
    */
    public function __construct() {
        // build our parent
        parent::__construct('map', 'Map');

        // set our default values
        $this->attributes->setLiteral('displayProjection', 'new OpenLayers.Projection("EPSG:4326")');
        $this->attributes->set('numZoomLevels', 20);
        $this->attributes->set('controls', array ()); // do not add default controls: @see http://docs.openlayers.org/library/controls.html
    }

    // }}}

    // settings
    // {{{ setDimensions()

    /**
    Set the map dimensions, in pixels.  Default is 640px square
    */
    public function setDimensions($width, $height) {
        if (is_numeric($width) && 0 < $width && is_numeric($height) && 0 < $height) {
            $this->width = (int)$width;
            $this->height = (int)$height;
            return $this;
        } else {
            throw new InvalidArgumentException('Width and height must both be positive numbers');
        }
    }

    // }}}
    // {{{ getDimensions()

    /**
    Get the map dimensions, as an array of pixels (w, h).
    */
    public function getDimensions() {
        return array ($this->width, $this->height);
    }

    // }}}
    // {{{ setInitialLocation()

    /**
    Set the initial location, that is the spot on the map over which you want to hover initially.

    If have set a zoom bounds, then set an initial location, your zoom bounds will not be respected.
    */
    public function setInitialLocation($latitude, $longitude, $zoom) {
        if (! $this->isLatitude($latitude)) {
            throw new InvalidArgumentException('First parameter (latitude) must be latitude');
        } else if (! $this->isLongitude($longitude)) {
            throw new InvalidArgumentException('Second parameter (longitude) must be longitude');
        } else if (! (is_integer($zoom) && 0 <= $zoom && $zoom <= $this->attributes->get('numZoomLevels'))) {
            throw new InvalidArgumentException('Third parameter (zoom) must be whole number less than the number of zoom levels');
        }

        $this->initialLocation = array ($latitude, $longitude, $zoom);
        $this->zoomToBounds    = null;
        return $this;
    }

    // }}}
    // {{{ getInitialLocation()

    /**
    Get the initial location. This is an array of (latitude, longitude, zoom) corresponding to the spot over the map you wish to
    initially hover.
    */
    public function getInitialLocation() {
        return $this->initialLocation;
    }

    // }}}
    // {{{ setZoomToBounds()

    /**
    Set the bounds to which you want to zoom.  If using fixed zoom levels, may not be precisely the same.  Provide coordinates
    in latitude and longitude.

    If you setZoomToBounds() then setInitialLocation(), your initial location trumps zoom to bounds.
    */
    public function setZoomToBounds(GIS_MBR $MBR) {
        $this->zoomToBounds    = $MBR;
        $this->initialLocation = null;
        return $this;
    }

    // }}}
    // {{{ getZoomToBounds()

    /**
    Get the bounds to which you want to zoom, or null if none set.
    */
    public function getZoomToBounds() {
        return $this->zoomToBounds;
    }

    // }}}
    // {{{ setScales()

    /**
    Set the scales you want to use on the map.
    */
    public function setScales($scales, $unit) {
        // if we have a non-empty array
        if (is_array($scales) && 0 < count($scales) && is_scalar($unit) && in_array($unit = trim($unit), self::$validUnits)) {
            // assume the array contains only numerics
            $valid = true;

            // check that it does, normalizing each to float
            foreach ($scales as $key => $scale) {
                if (is_numeric($scale) && 0 < $scale) {
                    $scales[$key] = (float)$scale;
                } else {
                    $valid = false;
                    break;
                }
            }

            // if indeed it did only contain valids
            if ($valid) {
                // sort numerically, biggest first (which means smallest zoom first)
                sort($scales);

                // set map values based on these
                // FIXME: do this by setting resolutions, not scale... since TileCache loves resolutions and we need the numbers
                // to be exact, down to every decimal place... in other words, the code below that sets resolutions works with
                // a similarly configured tilecache (so long as you have a 0 gutter in OpenLayers!!!!)
                /*
                $this->attributes->set('scales',        $scales);
                $this->attributes->set('numZoomLevels', count($scales));
                $this->attributes->set('maxScale',      min($scales));
                $this->attributes->set('minScale',      max($scales));
                $this->attributes->set('minResolution', 'auto');
                $this->attributes->set('maxResolution', 'auto');
                $this->attributes->set('units',         $unit);
                */
                /**/
                $this->attributes->set('numZoomLevels', 12);
                $this->attributes->set('minResolution', 0.14);
                $this->attributes->set('maxResolution', 115.74);
                $this->attributes->set(
                    'resolutions',
                    array (115.74,57.87,27.78,11.11,6.94,4.63,2.78,1.39,0.69,0.56,0.28,0.14)
                );
                $this->attributes->set('units',         'ft');
                /**/

                // tuck them away for our own use
                $this->scales = $scales;

                // return this object
                return $this;
            }
        }

        // problem with the input
        throw new InvalidArgumentException(
            'First parameter (scales) must be non-empty array of all numbers greater than 0'
        );
    }

    // }}}
    // {{{ getScales()

    /**
    Get the scales you want to use on the map.
    */
    public function getScales() {
        return $this->scales;
    }

    // }}}
    // {{{ setContainerID()

    public function setContainerID($containerID) {
        if (is_string($containerID) && 0 < strlen($containerID = trim($containerID))) {
            $this->containerID = $containerID;
            return $this;
        } else {
            throw new InvalidArgumentException('containerID must be non-empty string');
        }
    }

    // }}}
    // {{{ getContainerID()

    public function getContainerID() {
        return $this->containerID;
    }

    // }}}
    // {{{ setOpenLayersURL()

    public function setOpenLayersURL($openLayersURL) {
        if (is_string($openLayersURL) && 0 < strlen($openLayersURL = trim($openLayersURL))) {
            $this->openLayersURL = $openLayersURL;
            return $this;
        } else {
            throw new InvalidArgumentException('openLayersURL must be non-empty string');
        }
    }

    // }}}
    // {{{ getOpenLayersURL()

    public function getOpenLayersURL() {
        return $this->openLayersURL;
    }

    // }}}
    // {{{ renderStatusIn()

    /**
    * @test
    * data ''
    * expect InvalidArgumentException
    * data 'foo'
    * expect instanceof __CLASS__ & paint to have map.status set to $('foo')
    */
    public function renderStatusIn($statusDivID) {
        if (is_scalar($statusDivID) && 0 < strlen($statusDivID)) {
            $this->statusDivID = $statusDivID;
            return $this;
        } else {
            throw new InvalidArgumentException('Status <div> ID must be a non-empty scalar');
        }
    }

    // }}}

    // controls
    // {{{ newControl()

    /**
    Get a new control of the given class.  Controls appear on the map as interactive tools to change how the map works
    (eg Pan, Zoom, Measure, etc.
    */
    public function newControl($class, $args = array ()) {
        // get an implementation of that control
        $control = $this->factory('Control_%s', $class, $args);

        // hook this map into the control
        $control->setMap($this);

        // store and return
        $this->controls[$class] = $control;
        return $control;
    }

    // }}}
    // {{{ amendControl()

    /**
    Amend a previously added control.  To replace it, pass a new control.  To remove it, pass null.
    */
    public function amendControl($oldClass, $newClass, $args = array ()) {
        // if we're given a new class
        if (isset($newClass)) {
            // overwrite the old one with this new one
            $this->controls[$oldClass] = $this->newControl($newClass, $args);

        // otherwise, delete the old control
        } else {
            unset($this->controls[$oldClass]);
        }
    }

    // }}}
    // {{{ getControls()

    public function getControls() {
        return $this->controls;
    }

    // }}}

    // layers
    // {{{ newBaseLayer()

    /**
    Get a new base layer of the given class.  A base layer is the layer on the bottom.  Only one may be selected at a time.

    NOTE: We allow up to 5 arguments to be passed to the layer constructor.  If you have a layer that requires more, add a few
    more in the method signature below.
    */
    public function newBaseLayer($class, $a1 = null, $a2 = null, $a3 = null, $a4 = null, $a5 = null) {
        // get an implementation of that layer class
        $layer = $this->factory('Layer_%s', $class, $a1, $a2, $a3, $a4, $a5);

        // hook this map into the layer
        $layer->setMap($this);

        // mark this as being a base layer
        $layer->setIsBaseLayer(true);
        $layer->setIsTransparent(false);

        // adjust ourself based on this layer, if necessary
        $this->adjustMapForNewLayer($layer);

        // store and return
        $this->layers[] = $layer;
        return $layer;
    }

    // }}}
    // {{{ newOverlay()

    /**
    Get a new overlay of the given class.  An overlay rests on top of the base layer, stacked vertically. They are transparent,
    so that layers below them show through.

    NOTE: We allow up to 5 arguments to be passed to the layer constructor.  If you have a layer that requires more, add a few
    more in the method signature below.
    */
    public function newOverlay($class, $a1 = null, $a2 = null, $a3 = null, $a4 = null, $a5 = null) {
        // get an implementation of that layer class
        $layer = $this->factory('Layer_%s', $class, $a1, $a2, $a3, $a4, $a5);

        // hook this map into the layer
        $layer->setMap($this);

        // mark this as being a base layer
        $layer->setIsBaseLayer(false);
        $layer->setIsTransparent(true);

        // adjust ourself based on this layer, if necessary
        $this->adjustMapForNewLayer($layer);

        // store and return
        $this->layers[] = $layer;
        return $layer;
    }

    // }}}
    // {{{ getLayers()

    public function getLayers() {
        return $this->layers;
    }

    // }}}

    // others
    // {{{ newIcon()

    /**
    Add a new icon to the map's symbol set.
    */
    public function newIcon($url, $width = null, $height = null) {
        // get an implementation of the icon
        $icon = $this->factory('Icon', '', $url, $width, $height);

        // hook this map into the icon
        $icon->setMap($this);

        // store and return
        $this->icons[] = $icon;
        return $icon;
    }

    // }}}
    // {{{ newStyle()

    /**
    Add a new style to the map, optionally a derived style (like GIS_OpenLayers_Style_Symbolizer).
    */
    public function newStyle($attributes = array (), $class = null) {
        // get an implementation of the style class
        if (is_null($class)) {
            $style = $this->factory('Style', '', $attributes);
        } else {
            $style = $this->factory('Style_%s', $class, $attributes);
        }

        // hook this style into the layer
        $style->setMap($this);

        // store and return
        $this->styles[] = $style;
        return $style;
    }

    // }}}
    // {{{ newButton()

    /**
    Add a new button to the map.
    */
    public function newButton($button, array $attributes = array ()) {
        // get an implementation of the button
        $button = $this->factory('Button_%s', $button, $attributes);

        // hook this map into the button
        $button->setMap($this);

        // store and return
        $this->buttons[] = $button;
        return $button;
    }

    // }}}
    // {{{ newPanel()

    /**
    Add a new panel to the map.
    */
    public function newPanel(array $attributes = array ()) {
        // get an implementation of the panel
        $panel = $this->factory('Panel', '', $attributes);

        // hook this map into the panel
        $panel->setMap($this);

        // store and return
        $this->panels[] = $panel;
        return $panel;
    }

    // }}}

    // {{{ paint()

    /**
    Paint this map.
    */
    public function paint() {
        // output the map initialization
        echo <<<EOHTML
<script type='text/javascript' src='{$this->openLayersURL}'></script>
<script type='text/javascript'>
//<![CDATA[
if ('undefined' == typeof(OpenLayers)) {
    throw 'OpenLayers not found at URL: ' + {$this->getAsJSON($this->openLayersURL)};
}
var {$this->jsVariable} = new OpenLayers.Map({$this->getAsJSON($this->containerID)}, {$this->attributes->getAsJSON()});
//]]>
</script>

EOHTML;
        $this->log("Created map ({$this->jsVariable})");

        // output the styles
        foreach ($this->styles as $style) {
            $style->paint();
            $this->log(sprintf('Added style to map: %s (%s)', get_class($style), $style->getJavascriptVariable()));
        }

        // output the controls
        foreach ($this->controls as $control) {
            $control->paint();
            $this->log(sprintf('Added control to map: %s (%s)', get_class($control), $control->getJavascriptVariable()));
        }

        // output the buttons
        foreach ($this->buttons as $button) {
            $button->paint();
            $this->log(sprintf('Added button to map: %s (%s)', get_class($button), $button->getJavascriptVariable()));
        }

        // output the panels
        // NOTE: we add panels after controls and buttons, so that the controls'/buttons' javascript has been run
        // NOTE: and the variables exist as needed for adding to the panel.
        foreach ($this->panels as $panel) {
            $panel->paint();
            $this->log(sprintf('Added panel to map: %s (%s)', get_class($panel), $panel->getJavascriptVariable()));
        }

        // output the layers
        foreach ($this->layers as $layer) {
            // paint the layer and spit out a debug message indicating as much
            $layer->paint();
            $this->log(sprintf('Added layer to map: %s (%s)', $layer->getTitle(), $layer->getJavascriptVariable()));

            // mark as the base layer, if appropriate
            if ($layer->getIsSelectedBaseLayer()) {
                echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
{$this->jsVariable}.setBaseLayer({$layer->getJavascriptVariable()});
//]]>
</script>

EOHTML;
                $this->log(sprintf('Setting base layer: %s', $layer->getTitle()));
            }

            // set the layer order
            if (GIS_OpenLayers_Map_Layer::ORDER_UNDEFINED != ($layerOrder = $layer->getLayerOrder())) {
                echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
{$this->jsVariable}.setLayerIndex({$layer->getJavascriptVariable()}, $layerOrder);
//]]>
</script>

EOHTML;
                $this->log(sprintf('Setting layer order: %s %s', $layer->getTitle(), $layerOrder));
            }

            // monitor loads, if the layer supports load progress
            if (in_array('GIS_OpenLayers_Map_Layer_iLoadProgress', class_implements($layer))) { 
                echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
{$layer->getJavascriptVariable()}.events.register('loadstart',  {$layer->getJavascriptVariable()}, function() {
    OpenLayers.Console.info('Layer event: {$layer->getTitle()}: Start load');
});
{$layer->getJavascriptVariable()}.events.register('tileloaded', {$layer->getJavascriptVariable()}, function() {
    if (0 < this.numLoadingTiles && 0 == (this.numLoadingTiles % 10)) {
        OpenLayers.Console.info('Layer event: {$layer->getTitle()}: ' + this.numLoadingTiles + ' tiles remaining');
    }
});
{$layer->getJavascriptVariable()}.events.register('loadend',    {$layer->getJavascriptVariable()}, function() {
    OpenLayers.Console.info('Layer event: {$layer->getTitle()}: Finish load');
});
//]]>
</script>

EOHTML;
            }
        }

        // set the initial location
        if (isset($this->initialLocation)) {
            echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
var {$this->jsVariable}centrum = new OpenLayers.LonLat({$this->initialLocation[1]}, {$this->initialLocation[0]});
{$this->jsVariable}centrum.transform({$this->jsVariable}.displayProjection, {$this->jsVariable}.getProjectionObject());
{$this->jsVariable}.setCenter({$this->jsVariable}centrum, {$this->initialLocation[2]});
OpenLayers.Console.info(
    'Initial location: <{$this->initialLocation[0]}, {$this->initialLocation[1]}, {$this->initialLocation[2]}>'
);
//]]>
</script>

EOHTML;

        // or the zoom bounds
        } else if ($this->zoomToBounds instanceof GIS_MBR) {
            $minx = $this->zoomToBounds->getMinX();
            $miny = $this->zoomToBounds->getMinY();
            $maxx = $this->zoomToBounds->getMaxX();
            $maxy = $this->zoomToBounds->getMaxY();
            echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
var {$this->jsVariable}upperLeft  = new OpenLayers.LonLat({$minx}, {$miny});
var {$this->jsVariable}lowerRight = new OpenLayers.LonLat({$maxx}, {$maxy});
{$this->jsVariable}upperLeft.transform ({$this->jsVariable}.displayProjection, {$this->jsVariable}.getProjectionObject());
{$this->jsVariable}lowerRight.transform({$this->jsVariable}.displayProjection, {$this->jsVariable}.getProjectionObject());
var {$this->jsVariable}zoomBounds = new OpenLayers.Bounds();
{$this->jsVariable}zoomBounds.extend({$this->jsVariable}upperLeft);
{$this->jsVariable}zoomBounds.extend({$this->jsVariable}lowerRight);
{$this->jsVariable}.zoomToExtent({$this->jsVariable}zoomBounds, true);
{$this->jsVariable}.zoomOut();
OpenLayers.Console.info('Zoom bounds: <{$miny}, {$miny}>, <{$maxx}, {$maxy}>');
//]]>
</script>

EOHTML;

        // or max extents
        } else {
            echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
{$this->jsVariable}.zoomToMaxExtent();
//]]>
</script>

EOHTML;
        }

        // set the status div
        // NOTE: the element may not exist yet in the dom, so connect it after we load
        if (null === $this->statusDivID) {
            $statusDivID = uniqid('mapStatus');
            echo <<<EOHTML
<span id='$statusDivID' class='mapStatus'></span>
EOHTML;
        } else {
            $statusDivID = $this->statusDivID;
        }
        echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
OpenLayers.Event.observe(window, 'load', function () {
{$this->jsVariable}.status = $('$statusDivID');
});
//]]>
</script>

EOHTML;
    }

    // }}}

    /* protected API */
    // {{{ static $validUnits

    /**
    An array of units that OpenLayers supports.
    */
    static protected $validUnits = array ('inches','in','ft','mi','m','nmi','km','dd','degrees','yd');

    // }}}
    // {{{ $containerID

    /**
    The ID of the HTML div container into which the map will be rendered.  Set the style (dimensions, border, etc) as you want
    this to appear.
    */
    protected $containerID = null;

    // }}}
    // {{{ $openLayersURL

    /**
    The URL where the OpenLayers library can be found.  Must be resolvable by the browser.
    */
    protected $openLayersURL = null;

    // }}}
    // {{{ $width

    protected $width = 640;

    // }}}
    // {{{ $height

    protected $height = 640;

    // }}}
    // {{{ $initialLocation

    /**
    The initial position, if one is desired.  If not set, the map will be fully zoomed to extents and centered.
    */
    protected $initialLocation = null;

    // }}}
    // {{{ $zoomToBounds

    /**
    \seealso AERES_GIS_Map::setZoomToBounds()
    */
    private $zoomToBounds = null;

    // }}}
    // {{{ $scales

    /**
    An array of scales to use on the map, or null if none.
    */
    protected $scales = null;

    // }}}
    // {{{ $controls

    /**
    An array of controls to show on the map.
    */
    protected $controls = array ();

    // }}}
    // {{{ $layers

    /**
    An array of layers to show on the map.
    */
    protected $layers = array ();

    // }}}
    // {{{ $icons

    /**
    An array of icons to make available on the map.  This is like the "symbol set"; it's all possible icons, but only a few may
    actually be used.
    */
    protected $icons = array ();

    // }}}
    // {{{ $styles

    /**
    An array of styles to make available on the map.
    */
    protected $styles = array ();

    // }}}
    // {{{ $panels

    /**
    An array of panels to make available on the map.
    */
    protected $panels = array ();

    // }}}
    // {{{ $buttons

    /**
    An array of buttons to make available on the map.
    */
    protected $buttons = array ();

    // }}}
    // {{{ $statusDivID

    /**
    The ID of the element into which we want the map to output messages.  If not set, an element of class "mapStatus"
    will be created for this purpose.
    */
    protected $statusDivID = null;

    // }}}

    /* private API */
    // {{{ factory()

    /**
    Generate an object.
    */
    private function factory($template, $class, $a1 = null, $a2 = null, $a3 = null, $a4 = null, $a5 = null) {
        // get an implementation of the class
        // NOTE: We check the namespace of the calling object first, and if that doesn't exist, we check our namespace.
        // NOTE: This allows classes that extend this one to have a parallel structure for extending controls, layersl, icons,
        // NOTE: and styles without having to override the newXXX() methods.
        $klass = new Klass(get_class($this) . '_' . $template, $class);
        if (! $klass->exists()) {
            $klass = new Klass(__CLASS__ . '_' . $template, $class);
        }

        return $klass->instantiate($a1, $a2, $a3, $a4, $a5);
    }

    // }}}
    // {{{ adjustMapForNewLayer()

    /**
    Change the map, if needed, to compensate for a newly added layer.
    */
    private function adjustMapForNewLayer(GIS_OpenLayers_Map_Layer $layer) {
        // if the layer implements spherical mercator projection
        if (in_array('GIS_OpenLayers_Map_Layer_iSphericalMercator', class_implements($layer))) {
            // set the needed properties on the map
            $this->attributes->setLiteral('projection', 'new OpenLayers.Projection("EPSG:900913")');
            $this->attributes->setLiteral('maxExtent',  'new OpenLayers.Bounds(-20037508,-20037508,20037508,20037508)');

            // if we've not manually set our scales, update the defaults for spherical mercator
            if (is_null($this->scales)) {
                $this->attributes->set('units',         'm');
                $this->attributes->set('maxResolution', 156543.0339);
            }
        }
    }

    // }}}
}

?>
