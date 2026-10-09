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
        if($value === ''){
            return 0;
        }
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
    public static function maxUpload(): int
    {
        $limits = [
            self::returnBytes(ini_get('upload_max_filesize')),
            self::returnBytes(ini_get('post_max_size')),
            self::returnBytes(ini_get('memory_limit')),
        ];

        // 0 or -1 in php.ini means "no limit" : those values must be ignored
        $limits = array_filter($limits, fn($limit) => $limit > 0);

        return $limits ? min($limits) : PHP_INT_MAX;
    }

    /**
     * Check an uploaded picture.
     * Returns the list of errors (empty if everything is fine, or if no file was chosen).
     *
     * @param  array $file one entry of $_FILES
     * @param  int $maxSize in bytes (2 MB by default)
     * @return array
     */
    public static function pictureErrors(array $file, int $maxSize = 2097152): array
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if($error === UPLOAD_ERR_NO_FILE){
            return [];
        }
        if($error !== UPLOAD_ERR_OK){
            return ['The picture could not be uploaded.'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if(!in_array($extension, ['png', 'jpg', 'jpeg', 'gif'], true)){
            return ['This extension is not valid.'];
        }
        if($file['size'] > $maxSize){
            return ['The picture must not exceed ' . ($maxSize / 1048576) . ' MB.'];
        }
        if(@getimagesize($file['tmp_name']) === false){
            return ['This file is not a valid image.'];
        }

        return [];
    }

    /**
     * Move an uploaded picture into img/$folder with a random name,
     * so nothing is ever overwritten. Check it with pictureErrors() first.
     *
     * @param  array $file one entry of $_FILES
     * @param  string $folder "postPicture", "posterFilm"...
     * @return string name of the saved file
     */
    public static function moveUploadedPicture(array $file, string $folder): string
    {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name      = bin2hex(random_bytes(6)) . '.' . $extension;

        if(!move_uploaded_file($file['tmp_name'], IMAGE . $folder . DIRECTORY_SEPARATOR . $name)){
            throw new \Exception("Error, impossible to save the picture");
        }

        return $name;
    }
}