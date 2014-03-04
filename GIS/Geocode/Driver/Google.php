<?php
/**
* Implements a connection to the Google geocoding service.
*/
class GIS_Geocode_Driver_Google implements GIS_Geocode_Driver_Interface {
    /* public API */
    // {{{ setSensor()

    /**
    * The sensor is a boolean value that tells us whether this request is coming
    * from a GPS device (a position sensor) or a user.  It is required by the Google API
    *  and defaults to false.  You can probably ignore this method.
    *
    * @param $sensor boolean Whether the geocoding is requested by a sensor, or not
    */
    public function setSensor($sensor) {
        if (is_scalar($sensor)) {
            $this->sensor = (bool)$sensor;
            return $this;
        } else {
            throw new InvalidArgumentException('$sensor');
        }
    }

    // }}}
    // {{{ getSensor()

    public function getSensor() {
        return $this->sensor;
    }

    // }}}
    // {{{ geocode()

    public function geocode($address) {
        if (is_string($address) && 0 < strlen($address)) {
            return new GIS_Geocode_Iterator_Google($this->askGoogleService($address));
        } else {
            throw new InvalidArgumentException('Address must be a non-empty string');
        }
    }

    // }}}

    /* protected API */
    // {{{ $sensor

    protected $sensor = false;

    // }}}
    // {{{ askGoogleService()

    /**
    * Use a given address to call to Google's geocoding service.  Check the status of the return
    * for success or the error message.  On success, return a SimpleXMLElement containing the
    * returned data.  On failure, throw an exception with the error message.
    *
    * @param $address string The address to geocode.
    * @return SimpleXMLElement The XML from the Google response in a traversable format.
    */
    protected function askGoogleService($address) {
        $url = 'http://maps.google.com/maps/api/geocode/xml?' . http_build_query(array (
                    'address' => $address,
                    'sensor'  => $this->sensor ? 'true' : 'false',
                ));

        // define our cURL options
        $options = array (
                       CURLOPT_URL            => $url,
                       CURLOPT_FAILONERROR    => true,
                       CURLOPT_RETURNTRANSFER => true,
                       CURLOPT_HEADER         => 0,
                       CURLOPT_TIMEOUT        => 5,
                   );

        // initialize cURL
        $ch = curl_init();
        if (false === $ch) {
            throw new RuntimeException('curl_init() failed');
            break;
        }

        // set our options
        $ok = curl_setopt_array($ch, $options);
        if (false === $ok) {
            throw new RuntimeException('curl_setopt_array() said ' . curl_error($ch));
        }

        // make the call
        $googleReturn = curl_exec($ch);
        if (false === $googleReturn) {
            throw new RuntimeException('curl_exec() said ' . curl_error($ch));
        }

        // close it down
        curl_close($ch);

        // process the response
        $response = new SimpleXMLElement($googleReturn);

        // Get the status of the response.  SimpleXMLElement:xpath($path) returns an array
        // of SimpleXMLElements that are children of the node that lies at the end of the path
        // given, where the path given is relative to the node that xpath() is called from.

        // In this case, it gets an array of SimpleXMLElements that hold the statuses that
        // google returned.  There should be only one status in that array and if we succeeded
        // it should be 'OK'.  Otherwise it will contain the error message.
        $nodes = $response->xpath('/GeocodeResponse/status');
        if (is_array($nodes) && 1 == count($nodes)) {
            $status = (string)array_pop($nodes);
            switch ($status) {
            case 'OK':
                break;

            case 'OVER_QUERY_LIMIT':
                throw new GIS_Geocode_Exception_Google_OverQueryLimit();

            case 'ZERO_RESULTS':
                throw new GIS_Geocode_Exception_NoResults();

            default:
                throw new GIS_Geocode_Exception_Google($status);
            }
        } else {
            throw new RuntimeException('Google response not understood');
        }

        // Returns a SimpleXMLElement populated with the XML returned from Google.
        return $response;
    }

    // }}}
}

?>
