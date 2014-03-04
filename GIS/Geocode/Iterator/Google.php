<?php
/**
* Implements an iterator over codings returned from a Google geocode driver.
*/
class GIS_Geocode_Iterator_Google extends ArrayIterator {
    /* public API */
    // {{{ __construct()

    public function __construct(SimpleXMLElement $response) {
        parent::__construct($this->convertToCodings($response));
    }

    // }}}

    /* protected API */
    // {{{ convertToCodings()

    /**
    * @param $response SimpleXMLElement The SimpleXMLElement representation from the Google response.
    * @return ArrayIterator The codings from the $response.
    */
    protected function convertToCodings(SimpleXMLElement $response) {
        $codings = array ();
        foreach ($response->xpath('/GeocodeResponse/result') as $result) {
            $coding = new GIS_Geocode_Coding_Google();
            $coding->setFormattedAddress((string)$result->formatted_address);
            foreach ($result->xpath('address_component') as $node) {
                $typeAr = $node->xpath('type');
                if (empty($typeAr)) {
                    continue;
                }
                $type = (string)$typeAr[0];

                if ($this->haveMethod($type)) {
                    $method = $this->nodeToMethod($type);
                    $abbrMethod = $this->nodeToAbbreviatedMethod($type);

                    $coding->$method((string)$node->long_name);
                    $coding->$abbrMethod((string)$node->short_name);
                }

            }
            $location = $result->xpath('geometry/location');
            $location = $location[0];           
    
            $coding->setLatitude((float)$location->lat);
            $coding->setLongitude((float)$location->lng);

            $codings[] = $coding;
        }

        return ($codings);
    }

    // }}}
    // {{{ private $mappings = array(...)
    
    private $mappings = array(
            'street_number'=>'setStreetNumber',
            'post_box'=>'setPostBox',
            'floor'=>'setFloor',
            'room'=>'setRoom',
            'route'=>'setRoute',
            'locality'=>'setLocality',
            'administrative_area_level_1'=>'setLevel1',
            'administrative_area_level_2'=>'setLevel2',
            'administrative_area_level_3'=>'setLevel3',
            'country'=>'setCountry',
            'postal_code'=>'setPostalCode'
        );
    
    // }}}
    // {{{ private haveMethod($node)
    
    private function haveMethod($node) {
        return array_key_exists($node, $this->mappings);
    }
        
    // }}}
    // {{{ private nodeToMethod($node)

    private function nodeToMethod($node) {
        return $this->mappings[$node];
    }

    // }}}
    // {{{ private nodeToAbbreviatedMethod($node) 

    private function nodeToAbbreviatedMethod($node) {
        return $this->mappings[$node] . 'Abbreviated';
    }

    // }}}
}

?>
