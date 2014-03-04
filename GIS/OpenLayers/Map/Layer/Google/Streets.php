<?php
final class GIS_OpenLayers_Map_Layer_Google_Streets extends GIS_OpenLayers_Map_Layer_Google {
    /* public API */
    // {{{ __construct()

    public function __construct() {
        parent::__construct('G_NORMAL_MAP');
        $this->setTitle('Google Map');
    }

    // }}}
}

?>
