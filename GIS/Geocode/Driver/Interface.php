<?php
/**
* The interface that all drivers must support.
* Drivers may add other methods specific to their implementation, for example methods to set
* connection parameters (license keys, ports, etc.).
*/
interface GIS_Geocode_Driver_Interface {
    /**
    * Geocode the given address.
    * @param $address string The address to geocode.
    * @return ArrayIterator An array of codings for the given address, each a GIS_Geocode_Coding_Interface
    */
    public function geocode($address);
}

?>
