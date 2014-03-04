<?php
/**
* Implements an iterator over codings returned from a caching geocoder.
*/
class GIS_Geocode_Iterator_Caching extends ArrayIterator {
    /* public API */
    public function __construct($hit) {
        parent::__construct($this->convertToCodings($hit));
    }

    /* protected API */
    /**
    * @param $hit mixed The value from the cache
    * @return ArrayIterator The codings from the $hit.
    */
    protected function convertToCodings($hit) {
        // if no hit, no codings: done
        if (false === $hit) {
            return array ();
        }

        // otherwise, we're expecting an array
        $coding = new GIS_Geocode_Coding_Caching();
        $coding->setFormattedAddress($hit['address']);
        $coding->setLatitude($hit['latitude']);
        $coding->setLongitude($hit['longitude']);

        return array ($coding);
    }
}

?>
