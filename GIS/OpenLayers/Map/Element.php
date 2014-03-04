<?php
/**
Implements an element that may appear on a map, such as controls, layers, and popups.
*/
abstract class GIS_OpenLayers_Map_Element extends GIS_OpenLayers_Object {
    /* public API */
    // {{{ setMap()

    /**
    Set the map object.
    */
    public function setMap(GIS_OpenLayers_Map $map) {
        $this->map = $map;
        return $this;
    }

    // }}}
    // {{{ getMap()

    /**
    Get the map object.
    */
    public function getMap() {
        return $this->map;
    }

    // }}}

    /* protected API */
    // {{{ $map

    /**
    The map object
    */
    protected $map = null;

    // }}}
}

?>
