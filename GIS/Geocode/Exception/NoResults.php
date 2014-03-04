<?php

class GIS_Geocode_Exception_NoResults extends RuntimeException {
    public function __construct($message = null, $code = null) {
        if (null === $message) {
            $message = 'No results';
        }
        parent::__construct($message, $code);
    }
}

?>
