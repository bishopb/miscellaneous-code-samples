<?php
class GIS_OpenLayers_Map_Control_PanZoom extends GIS_OpenLayers_Map_Control {
    /* friend API */
    public function __construct($attributes) {
        parent::__construct('Control.PanZoom', $attributes);
    }
}

?>
