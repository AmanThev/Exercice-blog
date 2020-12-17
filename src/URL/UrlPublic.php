<?php

namespace App\URL;


class URLPublic {

    public static function publicPath($script_path) 
    {
        if($script_path[0] != '/') {
          $script_path = "/" . $script_path;
        }
        return PUBLIC_PATH . $script_path;
    }
}