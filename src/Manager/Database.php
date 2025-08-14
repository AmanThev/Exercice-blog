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
use App\Manager\Exception\DatabaseException;
use App\URL\UrlPublic;

class Database extends Connection
{  
    public function getAllData(string $sql, string $model, $folder = null): array
    {
        if($sql != null){
            if($folder != null){
                $stmt = $this->connect()->query($sql);
                return $stmt->fetchAll(PDO::FETCH_CLASS, "App\\Model\\$folder\\$model");
            }
            $stmt = $this->connect()->query($sql);
            return $stmt->fetchAll(PDO::FETCH_CLASS, "App\\Model\\$model");
        }
        throw new DatabaseException('Sql does not work');
    }

    public function getData(string $sql, string $model): object 
    {
        if($sql != null){
            $stmt = $this->connect()->query($sql);
            $stmt->setFetchMode(PDO::FETCH_CLASS, "App\\Model\\$model");
            return $stmt->fetch();
        }
        throw new DatabaseException('Sql does not work');
    }

    // public function getDataById(string $sql, int $id, string $model, ?string $folder = null): object 
    // {
    //     $stmt = $this->connect()->prepare($sql);
    //     $stmt->execute(['id' => $id]);
    //     if($stmt->rowCount() == 1){
    //         if($folder != null){
    //             $stmt->setFetchMode(PDO::FETCH_CLASS, "App\\Model\\$folder\\$model");
    //             return $stmt->fetch();
    //         }
    //         $stmt->setFetchMode(PDO::FETCH_CLASS, "App\\Model\\$model");
    //         return $stmt->fetch();
    //     }
    //     throw new NotFoundException($model, $fieldValue);
    // }

    public function getDataByField(
        string $sqlBase, 
        string $fieldName, 
        string|int $fieldValue, 
        string $model, 
        ?string $folder = null
    ): object 
    {
        $sql = "$sqlBase WHERE $fieldName = :value";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['value' => $fieldValue]);
        if($stmt->rowCount() == 1){
            if($folder != null){
                $stmt->setFetchMode(PDO::FETCH_CLASS, "App\\Model\\$folder\\$model");
                return $stmt->fetch();
            }
            $stmt->setFetchMode(PDO::FETCH_CLASS, "App\\Model\\$model");
            return $stmt->fetch();
        }
        throw new NotFoundException($model, $fieldValue);
    }

    public function getAllDataByField(
        string $sqlBase, 
        string $fieldName, 
        string|int $fieldValue, 
        string $model, 
        ?string $folder = null
    ): array 
    {
        $sql = "$sqlBase WHERE $fieldName = :value";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['value' => $fieldValue]);
        if($folder != null){
            return $stmt->fetchAll(PDO::FETCH_CLASS, "App\\Model\\$folder\\$model");
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS, "App\\Model\\$model");
    }

    // public function getAllDataById(string $sql, int $id, string $model): array
    // {
    //     $stmt = $this->connect()->prepare($sql);
    //     $stmt->execute(['id' => $id]);
    //     return $stmt->fetchAll(PDO::FETCH_CLASS, "App\\Model\\$model");
    // }

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