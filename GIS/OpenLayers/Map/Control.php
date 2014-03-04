<?php
/**
Implements the base functionality of an OpenLayers map control.
*/
class GIS_OpenLayers_Map_Control extends GIS_OpenLayers_Map_Element {
    /* friend API */
    // {{{ __construct()

    public function __construct($implementation, array $attributes) {
        // don't let div be passed in, because it'll be quoted and will certainly be wrong.
        if (array_key_exists('div', $attributes)) {
            throw new InvalidArgumentException('Call renderIn() instead of passing "div" as an attribute');
        }

        // pass on down to our parent
        parent::__construct('control', $implementation, $attributes);
    }

    // }}}
    // {{{ renderIn()

    /**
    Set the div into which this control should go.
    */
    public function renderIn($divID) {
        if (is_scalar($divID) && 0 < strlen($divID)) {
            $this->inDivID = $divID;
            $this->attributes->setLiteral('div', sprintf('$("%s")', addcslashes($divID, '"')));
            return $this;
        } else {
            throw new InvalidArgumentException('Must give scalar, non-empty <div> ID to render control in');
        }
    }

    // }}}
    // {{{ activate()

    public function activate() {
        $this->activate = true;
        return $this;
    }

    // }}}
    // {{{ paint()

    /**
    Paint this control.  This a brain-dead simple painter, designed for the standard controls that can be created
    and added to the map with a single object initialization.  If you need any more complicated painting, then you should
    override this method.

    Note: If you're rendering this control in a div, then the control will be created once the document loads.
    */
    public function paint() {
        // get the appropriate dom:loaded wrapper
        $this->getOnLoadWrappers($header, $footer);
        $this->getActivation($activation);

        // output the control
        echo <<<EOHTML
<script type='text/javascript'>
//<![CDATA[
{$header}
var {$this->jsVariable} = new OpenLayers.{$this->implementation}({$this->attributes->getAsJSON()});
{$this->getMap()->getJavascriptVariable()}.addControl({$this->jsVariable});
{$footer}
{$activation}
//]]>
</script>
EOHTML;
    }

    // }}}

    /* protected API */
    // {{{ $inDivID

    /**
    * The div ID into which to render this control.
    */
    protected $inDivID = null;

    // }}}
    // {{{ $activate

    protected $activate = false;

    // }}}
    // {{{ getOnLoadWrappers()

    /**
    * Get the Javascript dom:loaded wrappers to use when we're rendering in a div
    */
    protected function getOnLoadWrappers(&$header, &$footer) {
        $header = '';
        $footer = '';
        if (null !== $this->inDivID) {
            $header = 'OpenLayers.Event.observe(window, "load", function () {';
            $footer = '});';
        }
    }

    // }}}
    // {{{ getActivation()

    /**
    * Get the Javascript necessary for activating/deactivating the control.
    */
    protected function getActivation(&$activation) {
        // if we're activating
        $activation = '';
        if ($this->activate) {
            // activation is always deferred -- the map has to be there
            $activation = "OpenLayers.Event.observe(window, 'load', function () { {$this->jsVariable}.activate(); });";
        }
    }

    // }}}
}

?>
