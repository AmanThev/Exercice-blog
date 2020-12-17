<?php

namespace App;


class Colors
{
    public static function getColor($name, $colors) {
        if(isset($colors[$name])){
            return $colors[$name];
        }
    }
}