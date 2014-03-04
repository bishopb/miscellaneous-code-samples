<?php

class GIS_Geocode_Exception_Google_OverQueryLimit extends GIS_Geocode_Exception_Google {
    public function __construct($message = null, $code = null) {
        if (null === $message) {
            $message = 'Too many queries today'; // Google gives 1 IP up to 2500 per day
        }
        parent::__construct($message, $code);
    }
}

?>
