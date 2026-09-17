<?php
class AddonsOnline
{
    private $arrayScript = array();

    function __construct()
    {
    }

    public function script($element = '', $type = '', $timeout = 3.5)
    {
        $script = '';
        $timeout = max($timeout * 1000, 15000);

        if ($element && $type) {
            $script = '<script type="text/javascript">$(function(){'
                . 'var loaded=false,loadAddon=function(){if(loaded)return;loaded=true;$("#' . $element . '").load("api/addons.php?type=' . $type . '")};'
                . '["pointerdown","keydown","touchstart","scroll"].forEach(function(eventName){window.addEventListener(eventName,loadAddon,{once:true,passive:true})});'
                . 'setTimeout(loadAddon,' . $timeout . ');'
                . '});</script>';
            $this->arrayScript[] = $script;
        }
    }

    public function set($element = '', $type = '', $timeout = 3.5)
    {
        $elementAddons = '';

        if ($element && $type) {
            $elementAddons = '<div id="' . $element . '"></div>';
            $this->script($element, $type, $timeout);
        }

        return $elementAddons;
    }

    public function get()
    {
        $textAddons = '';

        if ($this->arrayScript) {
            foreach ($this->arrayScript as $v) {
                $textAddons .= $v;
            }
        }

        return $textAddons;
    }
}
