<?php
/**
* Implements an iterator over codings returned from a Yahoo geocode driver.
*/
class GIS_Geocode_Iterator_Yahoo extends ArrayIterator {
    /* public API */
    // {{{ __construct()

    public function __construct(SimpleXMLElement $response) {
        parent::__construct($this->convertToCodings($response));
    }

    // }}}

    /* protected API */
    // {{{ convertToCodings()
    
    protected function convertToCodings(SimpleXMLElement $response) {
        $codings = array ();
        foreach ($response->xpath('/ResultSet/Result') as $result) {
            $coding = new GIS_Geocode_Coding_Yahoo();
            foreach ($result as $type => $node) {
                if ($this->haveMethod($type)) {
                    $method = $this->nodeToMethod($type);
                    $coding->$method((string)$node[0]);
                }

            }
            $latitude = $result->xpath('latitude');
            $latitude = $latitude[0];
        
            $longitude = $result->xpath('longitude');
            $longitude = $longitude[0];            
 
            $coding->setLatitude((float)$latitude);
            $coding->setLongitude((float)$longitude);

            // Yahoo doesn't have a notion of a formatted address, so set it from what we have
            $formatted = '';
            $elements = array (
                            'StreetNumber' => ' ', 'Route' => ' ',
                            'Level3' => ', ', 'Level2' => ', ', 'Level1' => ' ',
                            'PostalCode' => ' ', 'Country' => ' '
                        );
            foreach ($elements as $element => $separator) {
                $method = 'get' . $element;
                $value = $coding->$method();
                if (! empty($value)) {
                    $formatted .= $value . $separator;
                }
            }
            if (! empty($formatted)) {
                $coding->setFormattedAddress($formatted);
            }

            $codings[] = $coding;
        }

        return $codings;
    }   
    
    // }}}

    /* private API */
    // {{{ $mappings
    
    private $mappings = array (
                            'house' => 'setStreetNumber',
                            'unit' => 'setPostBox',
                            'street' => 'setRoute',
                            'neighborhood' => 'setLocality',
                            'level1' => 'setLevel1',
                            'level2' => 'setLevel2',
                            'level3' => 'setLevel3',
                            'level0' => 'setCountry',
                            'postal' => 'setPostalCode',
                            'level1code' => 'setLevel1Abbreviated',
                            'level2code' => 'setLevel2Abbreviated',
                            'level3code' => 'setLevel3Abbreviated',
                            'level0code' => 'setCountryAbbreviated',
                        );
    
    // }}}
    // {{{ haveMethod()

    private function haveMethod($type) {
        return array_key_exists($type, $this->mappings);
    }
    
    // }}}
    // {{{ nodeToMethod()
    
    private function nodeToMethod($type) {
        return $this->mappings[$type];
    }

    // }}}
}
?>
