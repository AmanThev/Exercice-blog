<?php 
namespace App\Helpers;

class File
{
    
    /**
     * convert to Bytes
     *
     * @param  string $value
     * @return int
     */
    private static function returnBytes(string $value) :int
    {
        $value = trim($value);
        $letter = strtolower($value[strlen($value)-1]);
        $value = (int)($value);
        switch($letter) {
            case 'g':
            $value *= 1024;
            case 'm':
            $value *= 1024;
            case 'k':
            $value *= 1024;
        }

        return $value;
    }

        
    /**
     * get the maximum authorized size of the php.ini (upload) 
     *
     * @return void
     */
    public static function maxUpload(){
        $max_upload     = self::returnBytes(ini_get('upload_max_filesize'));
        $max_post       = self::returnBytes(ini_get('post_max_size'));
        $memory_limit   = self::returnBytes(ini_get('memory_limit'));

        return min($max_upload, $max_post, $memory_limit);
    }
}