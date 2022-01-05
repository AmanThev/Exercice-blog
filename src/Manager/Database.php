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

class Database extends Connection
{  
    public function exist(string $title, string $tableName) :bool
    {
        $stmt = $this->connect()->prepare("SELECT title FROM $tableName WHERE title = :title");
        $stmt->execute(['title' => $title]);
        if($stmt->rowCount() == 1){
            return false;
        }
        return true;
    }
}