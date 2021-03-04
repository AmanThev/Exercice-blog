<?php

namespace App\URL;


class CreateURL {

    public static function url(string $name, ?array $params = [])
    {
        if($name[0] != '/') {
            $name = "/" . $name;
        }

        if($params){
            $slug = $params['slug'];
            $id = $params['id'];
            return WWW_ROOT. $name . '/' . $slug . '-' . $id;
        }
        return WWW_ROOT. $name;
    }

    public static function urlDashboardAction(string $name, int $id, ?string $action = NULL)
    {
        if($action){
            return WWW_ROOT. '/' . $name . '/' . $id . '/' . $action;
        }

       return WWW_ROOT. '/' . $name . '/' . $id;


    }

    public static function urlSlugOnly(string $name, string $slug)
    {
        return WWW_ROOT. '/' . $name . '/' . $slug;
    }

    public static function urlTitle($title)
    {
        $title = ucwords($title);
        $urlTitle = preg_replace("#[']+#", '_', $title);
        $urlTitle = preg_replace("#[ :&\-']+#", '', $urlTitle);
        return $urlTitle;
    }

    public static function urlTitleTopic($title)
    {
        $title = ucwords($title);
        $urlTitle = preg_replace("#[']+#", '_', $title);
        $urlTitle = preg_replace("#[ :&\?\-']+#", '', $urlTitle);
        return $urlTitle;
    }

    public static function urlTitleCheck($title)
    {
        $title = ucwords($title);
        $urlTitle = preg_replace("#[']+#", '_', $title);
        $urlTitle = preg_replace("#[ :&\?\-']+#", '', $urlTitle);
        $urlTitle = preg_split('/(?=[A-Z])/', $urlTitle, -1, PREG_SPLIT_NO_EMPTY);
        $urlTitle = implode(' ', $urlTitle);
        return $urlTitle;
    }
}
