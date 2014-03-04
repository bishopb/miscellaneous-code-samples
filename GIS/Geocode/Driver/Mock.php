<?php
/**
* Implements a connection to a mock geocoder.
*/
class GIS_Geocode_Driver_Mock implements GIS_Geocode_Driver_Interface {
    /* public API */
    public function geocode($address) {
        if (is_string($address) && 0 < strlen($address)) {
            return new GIS_Geocode_Iterator_Mock($this->getCodings());
        } else {
            throw new InvalidArgumentException('Address must be a non-empty string');
        }
    }

    /* protected API */
    protected function getCodings($address) {
        $mult = (0 == (mt_rand(0,1) % 2) ? 1 : -1);
        return array (array (
              'streetNumber'            => '123',
              'postBox'                 => null,
              'floor'                   => null,
              'room'                    => null,
              'route'                   => 'Main St.',
              'locality'                => null,
              'level1'                  => null,
              'level2'                  => 'Anywhere',
              'level3'                  => 'NC',
              'country'                 => 'US',
              'postalCode'              => mt_rand(20000,99999),
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
              'formattedAddress'        => $address,
              'latitude'                => $mult * (mt_rand(0, 85) + mt_rand(0, 10000) / 10000),
              'longitude'               => $mult * (mt_rand(0, 180) + mt_rand(0, 10000) / 10000),
          ));
    }
}

?>
