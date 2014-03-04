<?php
/**
* Implements a connection to the Yahoo geocoding service.
*/
class GIS_Geocode_Driver_Yahoo implements GIS_Geocode_Driver_Interface {
    /* public API */
    // {{{ __construct()

    public function __construct($appid) {
        $this->appid = $appid;
    }

    // }}}
    // {{{ geocode($address)

    public function geocode($address) {
        if (is_string($address) && 0 < strlen($address)) {
            return new GIS_Geocode_Iterator_Yahoo($this->askYahooService($address));
        } else {
            throw new InvalidArgumentException('Address must be a non-empty string');
        }
    }

    // }}}

    /* protected API */
    // {{{ $appid

    protected $appid = null;

    // }}}
    // {{{ askYahooService()

    /**
    * The command that actually communicates with Yahoo and gets the response.
    */
    protected function askYahooService($address) {
        $url = 'http://where.yahooapis.com/geocode?' . http_build_query(array (
                'q'=>$address,
                'flags'=>'G', // This is necessary to get the response in Global field format (level1, level2, etc)
                'appid'=>$this->appid,
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
        $yahooReturn = curl_exec($ch);
        if (false === $yahooReturn) {
            throw new RuntimeException('curl_exec() said ' . curl_error($ch));
        }

        // close it down
        curl_close($ch);

        // process the response
        $response = new SimpleXMLElement($yahooReturn);

        // status
        $error = $response->xpath('/ResultSet/Error');
        if ((int)$error[0] !== 0) {
            $status = $response->xpath('/ResultSet/ErrorMessage');
            throw new RuntimeException('Error from Yahoo:' . (string)$status[0]);
        }

        return $response;
    }

    // }}}
}
?>
