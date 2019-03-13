<?php

namespace Utility;

class Sessions
{
    static public function getSession($name = '')
    {
        if ('' == $name) return null;
        $prefix = (new \Yaf\Config\Ini(APPLICATION_PATH. DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'session.ini'))->prefix;
        return false == empty($prefix) ? $_SESSION[(string)$prefix . $name] : $_SESSION[$name];
    } 

}

