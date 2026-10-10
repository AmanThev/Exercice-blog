<?php
namespace App\Form;

use App\Helpers\File;

/**
 * What the forms with a picture (posts, films) have in common.
 *
 * The class using this trait must define the constants :
 *   TABLE  the table where the row is saved        ('posts', 'films')
 *   FOLDER the folder of the pictures, in img/      ('postPicture', 'posterFilm')
 *   COLUMN the column (and the form field) of the picture ('picture', 'poster')
 */
trait UploadsPicture
{
    /**
     * Makes sure the fields exist and are strings (a hand-made POST with title[]=x
     * would crash the Validator).
     */
    protected function ensureStrings(array $keys): void
    {
        foreach($keys as $key){
            $this->data[$key] = (isset($this->data[$key]) && is_string($this->data[$key])) ? trim($this->data[$key]) : '';
        }
    }

    /**
     * Check the picture : adds its errors to the ones of the form.
     */
    protected function checkPicture(array $file): self
    {
        $pictureErrors = File::pictureErrors($file);
        if(!empty($pictureErrors)){
            $this->errors[static::COLUMN] = $pictureErrors;
        }
        $this->resultValidator = empty($this->errors);

        return $this;
    }

    /**
     * ['picture' => 'name.jpg'] if a file was sent, otherwise nothing to add.
     */
    protected function savedPicture(array $file): array
    {
        if(empty($file['tmp_name'])){
            return [];
        }

        return [static::COLUMN => File::moveUploadedPicture($file, static::FOLDER)];
    }

    /**
     * One INSERT, picture included. If the INSERT fails, the saved file is removed.
     */
    protected function createWithPicture(array $fields, array $file): int
    {
        $picture = $this->savedPicture($file);

        try{
            return $this->create($fields + $picture, static::TABLE);
        }catch(\Throwable $e){
            if($picture){
                @unlink(IMAGE . static::FOLDER . DIRECTORY_SEPARATOR . reset($picture));
            }
            throw $e;
        }
    }
}