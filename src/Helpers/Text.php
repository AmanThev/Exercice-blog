<?php

namespace App\Helpers;

class Text
{
    
    /**
     * excerpt the first part of a text
     *
     * @param  string $content
     * @param  int $limit
     * @return content
     */
    public static function excerpt(string $content, int $limit = 60)
    {
        if(mb_strlen($content) <= $limit){
            return $content;
        }
        $lastSpace = mb_strpos($content, ' ', $limit);
        // No space after the limit (long last word, or line breaks only) : cut at the limit
        if($lastSpace === false){
            $lastSpace = $limit;
        }
        return mb_substr($content, 0, $lastSpace) . ' ...';
    }
}