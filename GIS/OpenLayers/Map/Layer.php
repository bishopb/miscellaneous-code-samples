<?php
/**
Implements the base functionality of an OpenLayers map layer.
*/
class GIS_OpenLayers_Map_Layer extends GIS_OpenLayers_Map_Element {
    /* public API */
    // {{{ ORDER_UNDEFINED

    /**
    Comes from the OpenLayers definition of "not on the map".
    \seealso http://dev.openlayers.org/releases/OpenLayers-2.8/doc/apidocs/files/OpenLayers/Map-js.html#OpenLayers.Map.getLayerIndex
    */
    const ORDER_UNDEFINED = -1;

    // }}}
    // {{{ setTitle()

    /**
    Set the title.
    */
    public function setTitle($title) {
        if (is_string($title) && 0 < strlen($title = trim($title))) {
            $this->title = $title;
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (title) must be non-empty string');
        }
    }

    // }}}
    // {{{ getTitle()

    /**
    Get the title.
    */
    public function getTitle() {
        return $this->title;
    }

    // }}}
    // {{{ setIsBaseLayer()

    /**
    Set whether this layer is a base layer, or not.
    */
    public function setIsBaseLayer($isBaseLayer) {
        if (is_scalar($isBaseLayer)) {
            $this->attributes->set('isBaseLayer', true == $isBaseLayer ? true : false);
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (isBaseLayer) must be boolean-equivalent');
        }
    }

    // }}}
    // {{{ getIsBaseLayer()

    /**
    Get whether this layer is a base layer, or not.
    */
    public function getIsBaseLayer() {
        return $this->attributes->get('isBaseLayer');
    }

    // }}}
    // {{{ setIsSelectedBaseLayer()

    /**
    Set whether this layer is to be used as the current base layer.
    */
    public function setIsSelectedBaseLayer($isSelectedBaseLayer) {
        if (false == $this->getIsBaseLayer()) {
            throw new RuntimeException('Layer not a base layer and cannot, therefore, be the selected base layer');
        }

        if (is_scalar($isSelectedBaseLayer)) {
            // if the selected base layer doesn't want to be seen in the switcher
            if (false === $this->getDisplayInLayerSwitcher()) {
                // we panic, because if you have a selected base layer, it needs to be in the switcher
                throw new RuntimeException('You cannot make a layer, that is not shown in the switcher, the selected base layer');
            }

            $this->isSelectedBaseLayer = true;
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (isSelectedBaseLayer) must be boolean-compatible');
        }
    }

    // }}}
    // {{{ getIsSelectedBaseLayer()

    /**
    Get whether this layer has been selected as the base layer.
    */
    public function getIsSelectedBaseLayer() {
        return $this->isSelectedBaseLayer;
    }

    // }}}
    // {{{ setIsTransparent()

    /**
    Set the whether this layer is transparent, or not.
    */
    public function setIsTransparent($isTransparent) {
        if (is_scalar($isTransparent)) {
            $this->attributes->set('transparent', true == $isTransparent ? true : false);
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (isTransparent) must be boolean-equivalent');
        }
    }

    // }}}
    // {{{ getIsTransparent()

    /**
    Get the whether this layer is transparent, or not.
    */
    public function getIsTransparent() {
        return $this->attributes->get('transparent');
    }

    // }}}
    // {{{ setOpacity()

    /**
    Set how "see-through" the rendered content is.  This is different than transparent, which describes if the elements are
    rendered on a solid background or not.  Use a value between 0.0 (completely transparent) and 1.0 (completely opaque).
    */
    public function setOpacity($opacity) {
        if (is_numeric($opacity) && 0.0 <= $opacity && $opacity <= 1.0) {
            $this->attributes->set('opacity', $opacity);
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (opacity) must be float between 0.0 and 1.0');
        }
    }

    // }}}
    // {{{ getOpacity()

    /**
    Get how see-through this layer is.
    */
    public function getOpacity() {
        return $this->attributes->get('opacity');
    }

    // }}}
    // {{{ setVisibility()

    /**
    Set whether this layer is visible "turned on" or invisible "turned off".  Passing true turns the layer on (the default) and
    passing false turns the layer off.
    */
    public function setVisibility($visibility) {
        if (is_scalar($visibility)) {
            $this->attributes->set('visibility', (true == $visibility ? true : false));
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (visibility) must be boolean-compatible');
        }
    }

    // }}}
    // {{{ getVisibility()

    /**
    Get whether this layer is visible ("turned on", the default) or invisible ("turned off").
    */
    public function getVisibility() {
        return $this->attributes->get('visibility');
    }

    // }}}
    // {{{ setDisplay()

    /**
    Set whether this layer is visible "turned on" or invisible "turned off".  Passing true turns the layer on (the default) and
    passing false turns the layer off.
    */
    public function setDisplay($display) {
        if (is_scalar($display)) {
            $this->attributes->set('display', (true == $display ? true : false));
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (display) must be boolean-compatible');
        }
    }

    // }}}
    // {{{ getDisplay()

    /**
    Get whether this layer is visible ("turned on", the default) or invisible ("turned off").
    */
    public function getDisplay() {
        return $this->attributes->get('display');
    }

    // }}}
    // {{{ setTileBuffer()

    /**
    Set the depth of tiles around the view port. These are downloaded to affect the smooth, slippy scrolling effect;
    the more tiles you have, the further you can pan without waiting for render.  The number you pass in his the depth of tiles
    around the viewport: think of the viewport as being surround by concentric circles of tiles.  A value of 1 means give one
    tile deep worth of surrounding tiles; 2 means give 2 deep, and so on.

    Keep in mind one important point: OpenLayers loads all tiles for a layer then moves on to the next layer, rather than
    loading all tiles in the view port, then loading buffer.  For that reason, the larger the tile buffer value, the longer it
    will take subsequent layers to display.
    */
    public function setTileBuffer($tileBuffer) {
        if (is_numeric($tileBuffer) && 0 <= $tileBuffer) {
            $this->attributes->set('buffer', (int)$tileBuffer);
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (tileBuffer) must be whole number greater than zero');
        }
    }

    // }}}
    // {{{ getTileBuffer()

    /**
    Get the number of tiles around the view port.
    */
    public function getTileBuffer() {
        return $this->attributes->get('buffer');
    }

    // }}}
    // {{{ setTileSize()

    /**
    Set the size of tiles, in pixels.  The larger these are, the fewer you need but the longer each takes to display.

    NOTE: Actual tile sizes requested by the OpenLayers map may be larger than this: specifically, the gutter size is added to
    the top, left, bottom, and right.  So a tile size of (256,256) with a 15px gutter will ask for 286x286 tiles
    (256 + 2*15, 256 + 2*15).  See also the GIS_OpenLayers_Map_Layer::setGutterSize() method.
    */
    public function setTileSize($width, $height) {
        if (is_numeric($width) && 0 < $width && is_numeric($height) && 0 < $height) {
            $this->attributes->setLiteral('tileSize', "new OpenLayers.Size($width,$height)");
            $this->tileSize = array ($width, $height);
            return $this;
        } else {
            throw new InvalidArgumentException('First and second parameters (width, height) must be whole numbers greater than zero');
        }
    }

    // }}}
    // {{{ getTileSize()

    /**
    Get the size of tiles, as array(width, height).
    */
    public function getTileSize() {
        return $this->tileSize;
    }

    // }}}
    // {{{ setGutterSize()

    /**
    Set the gutter size, in pixels, to ignore around tile images.
    */
    public function setGutterSize($gutterSize) {
        if (is_numeric($gutterSize) && 0 <= $gutterSize) {
            $this->attributes->set('gutter', $gutterSize);
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (gutterSize) must be whole number greater than or equal to zero');
        }
    }

    // }}}
    // {{{ getGutterSize()

    /**
    Get the gutter size, in pixels, to ignore around each tile.
    */
    public function getGutterSize() {
        return $this->attributes->get('gutter');
    }

    // }}}
    // {{{ setSingleTile()

    /**
    Set whether you want a single tile returned, or not.  This has the advantage of being fastest to navigate, but slowest to
    display.  For small datasets, or limited caching, this makes sense.

    Note that this gets the entire rendered layer, not just the viewport.  Thus, setTileSize() and setGutterSize() have no
    affect when $singleTile = true.
    */
    public function setSingleTile($singleTile) {
        if (is_scalar($singleTile)) {
            $this->attributes->set('singleTile', true == $singleTile ? true : false);
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (singleTile) must be boolean-equivalent');
        }
    }

    // }}}
    // {{{ getSingleTile()

    /**
    Get whether you want to return a single tile, or not.
    */
    public function getSingleTile() {
        return $this->attributes->get('singleTile');
    }

    // }}}
    // {{{ setDisplayInLayerSwitcher()

    /**
    Set whether this layer should be shown in the layer switcher, or not.  Shown layers are candidates for manipulation: eg,
    turn on, turn off, shuffle, etc.
    */
    public function setDisplayInLayerSwitcher($displayInLayerSwitcher) {
        if (is_scalar($displayInLayerSwitcher)) {
            // get the requested setting
            $displayInLayerSwitcher = (true == $displayInLayerSwitcher ? true : false);

            // if they want to disable it from the layer switcher, but it's the selected base layer
            if (false === $displayInLayerSwitcher && true === $this->getIsSelectedBaseLayer()) {
                // we panic, because if you have a selected base layer, it needs to be in the switcher
                throw new RuntimeException('You cannot remove the selected base layer from the layer switcher');
            }

            $this->displayInLayerSwitcher = $displayInLayerSwitcher;
            return $this;
        } else {
            throw new InvalidArgumentException('First parameter (displayInLayerSwitcher) must be boolean-equivalent');
        }
    }

    // }}}
    // {{{ getDisplayInLayerSwitcher()

    /**
    Get whether this layer should be shown in the layer switcher, or not
    */
    public function getDisplayInLayerSwitcher() {
        return $this->displayInLayerSwitcher;
    }

    // }}}
    // {{{ setLayerOrder()

    /**
    Set the layer order.
    */
    public function setLayerOrder($layerOrder) {
        if (self::ORDER_UNDEFINED === $layerOrder) {
            $this->layerOrder = self::ORDER_UNDEFINED;
            return $this;
        } else if (is_numeric($layerOrder) && 0 <= $layerOrder) {
            $this->layerOrder = (int)$layerOrder;
            return $this;
        } else {
            throw new InvalidArgumentException(
                'First parameter (layerOrder) must be whole number greater than or equal to zero or class constant ORDER_UNDEFINED'
            );
        }
    }

    // }}}
    // {{{ getLayerOrder()

    /**
    Get the layer order.
    */
    public function getLayerOrder() {
        return $this->layerOrder;
    }

    // }}}

    /* friend API */
    // {{{ __construct()

    public function __construct($implementation, $attributes) {
        // pass on down to our parent
        parent::__construct('layer', $implementation, $attributes);

        // set our default values
        $this->setTitle(uniqid('Layer'));

        // ... assume a visible base layer
        $this->setIsBaseLayer(true);
        $this->setIsTransparent(false);
        $this->setVisibility(true);
    }

    // }}}
    // {{{ paint()

    /**
    Paint this layer.  This a brain-dead simple painter, designed for the basic layers that can be created and added to the
    map with a single object initialization.  If you need any more complicated painting, then you should override this method.
    */
    public function paint() {
        // output the layer
        $displayInLayerSwitcher = ($this->displayInLayerSwitcher ? 'true' : 'false');
        echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
var {$this->jsVariable} = new OpenLayers.{$this->implementation}(
  {$this->getAsJSON($this->title)},
  {$this->attributes->getAsJSON()}
);
{$this->jsVariable}.displayInLayerSwitcher = {$displayInLayerSwitcher};
{$this->getMap()->getJavascriptVariable()}.addLayer({$this->jsVariable});
//]]>
</script>

EOHTML;
    }

    // }}}

    /* protected API */
    // {{{ $title

    /**
    The layer title.
    */
    protected $title = null;

    // }}}
    // {{{ $tileSize

    /**
    The set tile size, as an array of (width, height).  We cache this value only to support getTileSize() -- we push the correct
    setting into the attributes at setTileSize() time.
    */
    protected $tilesize = null;
    
    // }}}
    // {{{ $isSelectedBaseLayer

    /**
    Is this the selected base layer?
    \seealso GIS_OpenLayers_Map_Layer::setIsSelectedBaseLayer()
    */
    protected $isSelectedBaseLayer = false;
    
    // }}}
    // {{{ $displayInLayerSwitcher

    /**
    Should this layer be shown in the LayerSwitcher?  Generally, yes, but depending upon
    the control you're using, you might not want this.
    \seealso AERES_GIS_Map_Control_ViewManager
    */
    protected $displayInLayerSwitcher = true;
    
    // }}}
    // {{{ $layerOrder

    /**
    What z-order does this layer come in?
    */
    protected $layerOrder = self::ORDER_UNDEFINED;
    
    // }}}
}

?>
