<?php
/**
* Invalid argument given to a coding.
*/
class GIS_Geocode_Exception_Coding_InvalidArgument extends InvalidArgumentException {
    public function getParameter() {
        return $this->parameter;
    }
    public function getValue() {
        return $this->value;
    }
    public function __construct($parameter, $value) {
        $this->parameter = $parameter;
        $this->value     = $value;
        parent::__construct();
    }
}

?>
