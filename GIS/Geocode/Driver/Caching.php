<?php
/**
* Implements a connection to a geocode cache.
*/
class GIS_Geocode_Driver_Caching implements GIS_Geocode_Driver_Interface {
    /* public API */
    public function __construct(Zend_Cache_Core $cache) {
        $this->cache = $cache;
    }

    public function cache($address, $longitude, $latitude) {
        $this->cache->save(array ('address' => $address, 'longitude' => (float)$longitude, 'latitude' => (float)$latitude), $this->addressToId($address));
        return $this;
    }

    public function geocode($address) {
        if (is_string($address) && 0 < strlen($address)) {
            $result = $this->cache->load($this->addressToId($address), true /* true means ignore lifetime */); // BugzId:39307
            if (false == $result) {
                throw new GIS_Geocode_Exception_NoResults();
            } else {
                $hit = array (
                    'address'   => $address,
                    'longitude' => $result['longitude'],
                    'latitude'  => $result['latitude'],
                );
            }
            return new GIS_Geocode_Iterator_Caching($hit);
        } else {
            throw new InvalidArgumentException('Address must be a non-empty string');
        }
    }

    /* protected API */
    protected $cache = null;
    protected function addressToId($address) {
        return 'gcaddr' . md5($address);
    }
}

?>
