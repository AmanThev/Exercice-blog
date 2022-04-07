<?php
namespace App\Manager;

use \PDO;
use App\Model\Comment;
use App\Model\Film;
use App\Model\Forum\Category;
use App\Model\Forum\SubCategory;
use App\Model\Forum\Topic;
use App\Model\Forum\Message;
use App\Model\Post;
use App\Model\Member;
use App\Model\Admin;
use App\Model\Vote;
use App\Manager\Exception\NotFoundException;
use App\URL\UrlPublic;

class Database extends Connection
{  
    public function exist(string $field, string $key, string $tableName) :bool
    {
        $stmt = $this->connect()->prepare("SELECT $field FROM $tableName WHERE $field = :keyValue");
        $stmt->execute(['keyValue' => $key]);
        if($stmt->rowCount() == 1){
            return false;
        }
        return true;
    }

    public static function hydrate($object, array $data): void
    {
        foreach($data as $key => $value){
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $object->$method($data[$key]);
        }
    }

    public static function hydrateFile($object, array $data): void
    {
        foreach($data as $key => $value){
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $object->$method($data[$key]['name']);
        }
    }

    public function uploadFile(string $tabName, array $data, string $folder, string $columnName = 'picture'): void
    {
        foreach($data as $key => $value){
            $name = basename($data[$key]['name']);
            $title = $data[$key]['title'];
            $upload_dir = IMAGE .$folder. DIRECTORY_SEPARATOR .$name;
            $tmp_name = $_FILES[$key]['tmp_name'];
            $stmt = $this->connect()->prepare("UPDATE $tabName SET $columnName = :pictureName WHERE title = :title");
            $uploadFile = $stmt->execute([
                'pictureName' => $name,
                'title' => $title
            ]);
            if($uploadFile === false){
                throw new \Exception("Error, impossible to upload the picture");
            }
            move_uploaded_file($tmp_name, $upload_dir);
        }
    }
}