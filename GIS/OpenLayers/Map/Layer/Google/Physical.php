<?php
final class GIS_OpenLayers_Map_Layer_Google_Physical extends GIS_OpenLayers_Map_Layer_Google {
    /* public API */
    // {{{ __construct()

    public function __construct() {
        parent::__construct('G_PHYSICAL_MAP');
        $this->setTitle('Google Terrain');
    }

    // }}}
}

?>
