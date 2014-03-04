<?php
/**
* Provides a standardized, yet basic, container for a geocoding.  The possible parts of a coding are:
* - Street Number
* - Postal Box
* - Floor
* - Room
* - Route
* - Locality
* - Level 1
* - Level 2
* - Level 3
* - Country
* - Postal Code
*
* Not all of these need to be present in an address.  Each of these is the full text, and each may have an
* abbreviated version.  For example, country in full might be "United States" while the abbreviation might be "US".
*
* This class provides a standard /container/ for the data, but does not provide /canonical/ representations
* of countries, localities, counties, or anything else.
*/
abstract class GIS_Geocode_Coding_Abstract implements Serializable {
    /* public API */
    // parts of a geocoding, full text
    // {{{ setStreetNumber()

    public function setStreetNumber($streetNumber) {
        if (is_string($streetNumber)) {
            $this->coding['streetNumber'] = $streetNumber;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('streetNumber', $streetNumber);
        }
    }

    // }}}
    // {{{ getStreetNumber()

    public function getStreetNumber() {
        return $this->coding['streetNumber'];
    }

    // }}}
    // {{{ setPostBox()

    public function setPostBox($postBox) {
        if (is_string($postBox)) {
            $this->coding['postBox'] = $postBox;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('postBox', $postBox);
        }
    }

    // }}}
    // {{{ getPostBox()

    public function getPostBox() {
        return $this->coding['postBox'];
    }

    // }}}
    // {{{ setFloor()

    public function setFloor($floor) {
        if (is_string($floor)) {
            $this->coding['floor'] = $floor;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('floor', $floor);
        }
    }

    // }}}
    // {{{ getFloor()

    public function getFloor() {
        return $this->coding['floor'];
    }

    // }}}
    // {{{ setRoom()

    public function setRoom($room) {
        if (is_string($room)) {
            $this->coding['room'] = $room;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('room', $room);
        }
    }

    // }}}
    // {{{ getRoom()

    public function getRoom() {
        return $this->coding['room'];
    }

    // }}}
    // {{{ setRoute()

    public function setRoute($route) {
        if (is_string($route)) {
            $this->coding['route'] = $route;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('route', $route);
        }
    }

    // }}}
    // {{{ getRoute()

    public function getRoute() {
        return $this->coding['route'];
    }

    // }}}
    // {{{ setLocality()

    public function setLocality($locality) {
        if (is_string($locality)) {
            $this->coding['locality'] = $locality;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('locality', $locality);
        }
    }

    // }}}
    // {{{ getLocality()

    public function getLocality() {
        return $this->coding['locality'];
    }

    // }}}
    // {{{ setLevel1()

    public function setLevel1($level1) {
        if (is_string($level1)) {
            $this->coding['level1'] = $level1;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('level1', $level1);
        }
    }

    // }}}
    // {{{ getLevel1()

    public function getLevel1() {
        return $this->coding['level1'];
    }

    // }}}
    // {{{ setLevel2()

    public function setLevel2($level2) {
        if (is_string($level2)) {
            $this->coding['level2'] = $level2;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('level2', $level2);
        }
    }

    // }}}
    // {{{ getLevel2()

    public function getLevel2() {
        return $this->coding['level2'];
    }

    // }}}
    // {{{ setLevel3()

    public function setLevel3($level3) {
        if (is_string($level3)) {
            $this->coding['level3'] = $level3;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('level3', $level3);
        }
    }

    // }}}
    // {{{ getLevel3()

    public function getLevel3() {
        return $this->coding['level3'];
    }

    // }}}
    // {{{ setCountry()

    public function setCountry($country) {
        if (is_string($country)) {
            $this->coding['country'] = $country;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('country', $country);
        }
    }

    // }}}
    // {{{ getCountry()

    public function getCountry() {
        return $this->coding['country'];
    }

    // }}}
    // {{{ setPostalCode()

    public function setPostalCode($postalCode) {
        if (is_string($postalCode)) {
            $this->coding['postalCode'] = $postalCode;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('postalCode', $postalCode);
        }
    }

    // }}}
    // {{{ getPostalCode()

    public function getPostalCode() {
        return $this->coding['postalCode'];
    }

    // }}}

    // parts of a geocoding, abbreviated
    // {{{ setStreetNumberAbbreviated()

    public function setStreetNumberAbbreviated($streetNumberAbbreviated) {
        if (is_string($streetNumberAbbreviated)) {
            $this->coding['streetNumberAbbreviated'] = $streetNumberAbbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('streetNumberAbbreviated', $streetNumberAbbreviated);
        }
    }

    // }}}
    // {{{ getStreetNumberAbbreviated()

    public function getStreetNumberAbbreviated() {
        return $this->coding['streetNumberAbbreviated'];
    }

    // }}}
    // {{{ setPostBoxAbbreviated()

    public function setPostBoxAbbreviated($postBoxAbbreviated) {
        if (is_string($postBoxAbbreviated)) {
            $this->coding['postBoxAbbreviated'] = $postBoxAbbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('postBoxAbbreviated', $postBoxAbbreviated);
        }
    }

    // }}}
    // {{{ getPostBoxAbbreviated()

    public function getPostBoxAbbreviated() {
        return $this->coding['postBoxAbbreviated'];
    }

    // }}}
    // {{{ setFloorAbbreviated()

    public function setFloorAbbreviated($floorAbbreviated) {
        if (is_string($floorAbbreviated)) {
            $this->coding['floorAbbreviated'] = $floorAbbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('floorAbbreviated', $floorAbbreviated);
        }
    }

    // }}}
    // {{{ getFloorAbbreviated()

    public function getFloorAbbreviated() {
        return $this->coding['floorAbbreviated'];
    }

    // }}}
    // {{{ setRoomAbbreviated()

    public function setRoomAbbreviated($roomAbbreviated) {
        if (is_string($roomAbbreviated)) {
            $this->coding['roomAbbreviated'] = $roomAbbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('roomAbbreviated', $roomAbbreviated);
        }
    }

    // }}}
    // {{{ getRoomAbbreviated()

    public function getRoomAbbreviated() {
        return $this->coding['roomAbbreviated'];
    }

    // }}}
    // {{{ setRouteAbbreviated()

    public function setRouteAbbreviated($routeAbbreviated) {
        if (is_string($routeAbbreviated)) {
            $this->coding['routeAbbreviated'] = $routeAbbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('routeAbbreviated', $routeAbbreviated);
        }
    }

    // }}}
    // {{{ getRouteAbbreviated()

    public function getRouteAbbreviated() {
        return $this->coding['routeAbbreviated'];
    }

    // }}}
    // {{{ setLocalityAbbreviated()

    public function setLocalityAbbreviated($localityAbbreviated) {
        if (is_string($localityAbbreviated)) {
            $this->coding['localityAbbreviated'] = $localityAbbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('localityAbbreviated', $localityAbbreviated);
        }
    }

    // }}}
    // {{{ getLocalityAbbreviated()

    public function getLocalityAbbreviated() {
        return $this->coding['localityAbbreviated'];
    }

    // }}}
    // {{{ setLevel1Abbreviated()

    public function setLevel1Abbreviated($level1Abbreviated) {
        if (is_string($level1Abbreviated)) {
            $this->coding['level1Abbreviated'] = $level1Abbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('level1Abbreviated', $level1Abbreviated);
        }
    }

    // }}}
    // {{{ getLevel1Abbreviated()

    public function getLevel1Abbreviated() {
        return $this->coding['level1Abbreviated'];
    }

    // }}}
    // {{{ setLevel2Abbreviated()

    public function setLevel2Abbreviated($level2Abbreviated) {
        if (is_string($level2Abbreviated)) {
            $this->coding['level2Abbreviated'] = $level2Abbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('level2Abbreviated', $level2Abbreviated);
        }
    }

    // }}}
    // {{{ getLevel2Abbreviated()

    public function getLevel2Abbreviated() {
        return $this->coding['level2Abbreviated'];
    }

    // }}}
    // {{{ setLevel3Abbreviated()

    public function setLevel3Abbreviated($level3Abbreviated) {
        if (is_string($level3Abbreviated)) {
            $this->coding['level3Abbreviated'] = $level3Abbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('level3Abbreviated', $level3Abbreviated);
        }
    }

    // }}}
    // {{{ getLevel3Abbreviated()

    public function getLevel3Abbreviated() {
        return $this->coding['level3Abbreviated'];
    }

    // }}}
    // {{{ setCountryAbbreviated()

    public function setCountryAbbreviated($countryAbbreviated) {
        if (is_string($countryAbbreviated)) {
            $this->coding['countryAbbreviated'] = $countryAbbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('countryAbbreviated', $countryAbbreviated);
        }
    }

    // }}}
    // {{{ getCountryAbbreviated()

    public function getCountryAbbreviated() {
        return $this->coding['countryAbbreviated'];
    }

    // }}}
    // {{{ setPostalCodeAbbreviated()

    public function setPostalCodeAbbreviated($postalCodeAbbreviated) {
        if (is_string($postalCodeAbbreviated)) {
            $this->coding['postalCodeAbbreviated'] = $postalCodeAbbreviated;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('postalCodeAbbreviated', $postalCodeAbbreviated);
        }
    }

    // }}}
    // {{{ getPostalCodeAbbreviated()

    public function getPostalCodeAbbreviated() {
        return $this->coding['postalCodeAbbreviated'];
    }

    // }}}

    // miscellaneous
    // {{{ setFormattedAddress()

    public function setFormattedAddress($formattedAddress) {
        if (is_string($formattedAddress)) {
            $this->coding['formattedAddress'] = $formattedAddress;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('formattedAddress', $formattedAddress);
        }
    }

    // }}}
    // {{{ getFormattedAddress()

    public function getFormattedAddress() {
        return $this->coding['formattedAddress'];
    }

    // }}}
    // {{{ setLatitude()

    public function setLatitude($latitude) {
        if (is_numeric($latitude) && -85.0511 <= (float)$latitude && (float)$latitude <= 85.0511) {
            $this->coding['latitude'] = (float)$latitude;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('latitude', $latitude);
        }
    }

    // }}}
    // {{{ getLatitude()

    public function getLatitude() {
        return $this->coding['latitude'];
    }

    // }}}
    // {{{ setLongitude()

    public function setLongitude($longitude) {
        if (is_numeric($longitude) && -180 <= (float)$longitude && (float)$longitude <= 180) {
            $this->coding['longitude'] = (float)$longitude;
            return $this;
        } else {
            throw new GIS_Geocode_Exception_Coding_InvalidArgument('longitude', $longitude);
        }
    }

    // }}}
    // {{{ getLongitude()

    public function getLongitude() {
        return $this->coding['longitude'];
    }

    // }}}

    // implements Serializable
    // {{{ serialize()

    public function serialize() {
        return serialize($this->coding);
    }

    // }}}
    // {{{ unserialize()

    public function unserialize($serialized) {
        $this->coding = unserialize($serialized);
        if (! is_array($this->coding)) {
            throw new GIS_Geocode_Exception_Coding_UnserializeFailed();
        }
    }

    // }}}

    /* private API */
    // {{{ $coding

    /**
    * The information in this coding.
    */
    private $coding = array (
                          'streetNumber'            => null,
                          'postBox'                 => null,
                          'floor'                   => null,
                          'room'                    => null,
                          'route'                   => null,
                          'locality'                => null,
                          'level1'                  => null,
                          'level2'                  => null,
                          'level3'                  => null,
                          'country'                 => null,
                          'postalCode'              => null,
                          'streetNumberAbbreviated' => null,
                          'postBoxAbbreviated'      => null,
                          'floorAbbreviated'        => null,
                          'roomAbbreviated'         => null,
                          'routeAbbreviated'        => null,
                          'localityAbbreviated'     => null,
                          'level1Abbreviated'       => null,
                          'level2Abbreviated'       => null,
                          'level3Abbreviated'       => null,
                          'countryAbbreviated'      => null,
                          'postalCodeAbbreviated'   => null,
                          'formattedAddress'        => null,
                          'latitude'                => null,
                          'longitude'               => null,
                      );

    // }}}
}

?>
