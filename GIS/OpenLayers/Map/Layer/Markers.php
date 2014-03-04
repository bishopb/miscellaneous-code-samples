<?php
/**
* This class implements a marker layer, which is a transparent canvas onto which you can icons for "marking" spots on the map.
*/
  class GIS_OpenLayers_Map_Layer_Markers
extends GIS_OpenLayers_Map_Layer {
    /* public API */
    // {{{ __construct()

    public function __construct() {
        // build our parent
        parent::__construct('Layer.Markers', array ());
    }

    // }}}
    // {{{ placeMarker()

    /**
    * Place a marker at the given latitude/longitude using the given icon.  Optionally supply some Javascript
    * when particular events happen.
    * @code
    * $layer->placeMarker($lat, $lon, $icon, array ('click' => 'alert("Hi!");', 'mouseover' => 'alert("Hover!");'));
    * @endcode
    */
    public function placeMarker($latitude, $longitude, GIS_OpenLayers_Map_Icon $icon, $events = null) {
        // validate our arguments
        if (! $this->isLatitude($latitude)) {
            throw new InvalidArgumentException('First parameter (latitude) must be latitude');
        } else if (! $this->isLongitude($longitude)) {
            throw new InvalidArgumentException('Second parameter (longitude) must be longitude');
        } else if (! (null === $events || is_array($events))) {
            throw new InvalidArgumentException('Fourth parameter (events) must be an array when given');
        }

        // place the marker
        $this->markers[] = array ($latitude, $longitude, $icon, $events);
    }

    // }}}
    // {{{ paint()

    /**
    * Paint the markers onto the map.
    */
    public function paint() {
        // output the markers layer (courtesy of our parent)
        parent::paint();

        // walk the markers, outputing each
        // NOTE: We keep track of which icons we've painted, as we only want to do that once
        $painted = array ();
        $i = 0;
        foreach ($this->markers as $marker) {
            list ($latitude, $longitude, $icon, $events) = $marker;
            $i++;

            // paint the icon, if not already
            if (! isset($painted[$icon->getJavascriptVariable()])) {
                $icon->paint();
                $painted[$icon->getJavascriptVariable()] = true;
            }

            // convert the events array into Javscripts
            $eventsJavascript = '';
            if (is_array($events)) {
                foreach ($events as $event => $javascript) {
                    $eventsJavascript .=<<<EOJS
{$this->jsVariable}marker$i.events.register('{$event}', {$this->jsVariable}marker$i, function (e) { {$javascript} });

EOJS;
                }
            }

            // add the icon to the map
            // NOTE: We get the point on the map where the icon should go, then project that, then add to the layer
            echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
var {$icon->getJavascriptVariable()}point = new OpenLayers.LonLat({$longitude}, {$latitude});
{$icon->getJavascriptVariable()}point.transform(
    {$this->getMap()->getJavascriptVariable()}.displayProjection,
    {$this->getMap()->getJavascriptVariable()}.getProjectionObject()
);
{$this->jsVariable}marker$i = new OpenLayers.Marker({$icon->getJavascriptVariable()}point, {$icon->getJavascriptVariable()}.clone())
{$eventsJavascript}
{$this->jsVariable}.addMarker({$this->jsVariable}marker$i);
//]]>
</script>

EOHTML;
        }
    }

    // }}}
    
    /* protected API */
    // {{{ $markers

    /**
    * The array of markers we've placed on the map.
    */
    protected $markers = array ();

    // }}}
}

?>
