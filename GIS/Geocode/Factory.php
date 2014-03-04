<?php
/**
* Creates connections to various geocoding providers, such as Google and Yahoo.
* @pattern AbstractFactory
*/
abstract class GIS_Geocode_Factory {
    public static function getMockGeocoder() {
        static $driver = null;
        if (null === $driver) {
            $driver = new GIS_Geocode_Driver_Mock();
        }
        return $driver;
    }

    public static function getGoogleGeocoder() {
        static $driver = null;
        if (null === $driver) {
            $driver = new GIS_Geocode_Driver_Google();
        }
        return $driver;
    }

    /**
    * @param $options array The frontend and backend options, as would be found in a config file.
    */
    public static function getCachingGeocoder(Zend_Config $options) {
        static $driver = null;
        if (null === $driver) {
            $cache = Zend_Cache::factory(
                $options->frontend,
                $options->backend,
                $options->frontendOptions->toArray(),
                $options->backendOptions->toArray()
            );
            $driver = new GIS_Geocode_Driver_Caching($cache);
        }
        return $driver;
    }

    /**
    * @param $appid string The application ID required by the Yahoo geocoding service.
    * @see http://developer.yahoo.com/faq/index.html#appid
    */
    public static function getYahooGeocoder(Zend_Config $options) {
        static $driver = null;
        if (is_string($options->appid) && 0 < strlen($options->appid)) {
            if (null === $driver) {
                $driver = new GIS_Geocode_Driver_Yahoo($options->appid);
            }
            return $driver;
        } else {
            throw new InvalidArgumentException('$appid must be non-empty string');
        }
    }
}

?>
