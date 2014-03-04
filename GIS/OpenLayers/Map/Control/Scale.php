<?php
/**
This class implements a scale display, like 1:24000.
*/
class GIS_OpenLayers_Map_Control_Scale extends GIS_OpenLayers_Map_Control {
    /* friend API */
    // {{{ __construct()

    public function __construct($attributes) {
        parent::__construct('Control.Scale', $attributes);
    }

    // }}}
    // {{{ paint()

    public function paint() {
        // if we're given the div in which to render
        $div = 'null';
        if ($this->inDivID) {
            $div = "'{$this->inDivID}'"; // This control expects a string, not a Javascript object
        }

        // get the appropriate dom:loaded wrapper
        $this->getOnLoadWrappers($header, $footer);

        // output the HTML for the control
        echo <<<EOHTML
<script type="text/javascript">
//<![CDATA[
{$header}
var {$this->jsVariable} = new OpenLayers.Control.Scale({$div}, {$this->attributes->getAsJSON()});
{$this->getMap()->getJavascriptVariable()}.addControl({$this->jsVariable});
{$footer}
//]]>
</script>

EOHTML;
    }

    // }}}
}

?>
