<?php
/**
Implements an icon to display on the map.
*/
class GIS_OpenLayers_Map_Icon extends GIS_OpenLayers_Map_Element {
    /* friend API */
    // {{{ __construct()

    /**
    Create an icon of the given size.  The first parameter (url) is the full **browser-accessible** path to the image you want
    to use for the icon.  The second (width) and third (height) parameters (optional, but supply both or neight) are the
    desired width and height of the icon, not necessarily the actual dimensions of the image.  Thus if the actual image is
    20x34, you might supply a width of 10 and height of 17 to the image at 50% size.  If you don't supply the dimensions, we
    assume you want the image at 100%.
    */
    public function __construct($url, $width = null, $height = null) {
        // get the information about our icon
        if (is_string($url) && 0 < strlen($url = trim($url)) ) {
            // get the width and height, if needed
            if (is_null($width) || is_null($height)) {
                $info = @getimagesize($url);
                if (false === $info) {
                    if (0 === strpos($url, 'http://') || 0 === strpos($url, 'https://')) {
                        $phpConfig = new PHPConfig();
                        if (false == $phpConfig->get('allow_url_fopen')) {
                            throw new RuntimeException("PHP config (allow_url_fopen) prevents access to remote URL=[$url]");
                        } else {
                            throw new RuntimeException("Unable to determine image size of URL=[$url] (file not found? permission denied? not an image?); supply manually to constructor");
                        }
                    } else {
                        throw new RuntimeException("Unable to determine image size of file=[$url] (file not found?); supply manually to constructor");
                    }
                } else {
                    list ($width, $height) = getimagesize($url);
                }
            }

            // store information
            $this->url    = $url;
            $this->width  = $width;
            $this->height = $height;
        } else {
            throw new InvalidArgumentException('First parameter (url) must be a non-empty string');
        }

        // build our parent
        parent::__construct('icon', 'Icon', array ());
    }

    // }}}
    // {{{ paint()

    /**
    Paint this icon.
    */
    public function paint() {
        // output the icon
        echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
var {$this->jsVariable}size = new OpenLayers.Size({$this->width}, {$this->height});
var {$this->jsVariable}offset = new OpenLayers.Pixel(-({$this->jsVariable}size.w/2), -{$this->jsVariable}size.h);
var {$this->jsVariable} = new OpenLayers.Icon(
    {$this->getAsJSON($this->url)},
    {$this->jsVariable}size,
    {$this->jsVariable}offset
);
OpenLayers.Console.info(
);
//]]>
</script>

EOHTML;
        $this->log(sprintf('Added icon to symbol set: %s (%dx%d)', $this->url, $this->width, $this->height));
    }

    // }}}

    /* protected API */
    // {{{ $url

    /**
    The **browser-accessible** location of the image to use as the icon.  Keep it small silly (KISS)!
    */
    protected $url = null;

    // }}}
    // {{{ $width

    /**
    The width of the image, in pixels.
    */
    protected $width = null;

    // }}}
    // {{{ $height

    /**
    The height of the image, in pixels.
    */
    protected $height = null;

    // }}}
}

?>
