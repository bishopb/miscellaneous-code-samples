<?php
final class GIS_OpenLayers_Map_Layer_Google_Hybrid extends GIS_OpenLayers_Map_Layer_Google {
    /* public API */
    // {{{ __construct()

    public function __construct() {
        parent::__construct('G_HYBRID_MAP');
        $this->setTitle('Google Hybrid');
    }

    // }}}
}

?>
