<?php
namespace App\Manager;

use App\Model\Forum\Category;
use App\Model\Forum\SubCategory;
use App\Model\Forum\Topic;
use App\Model\Forum\Message;
use App\SQL\CountSql;
use \PDO;
use \Exception;


class ForumDatabase extends Database
{    
    /**
     * @var string
     */
    private $queryCat = "SELECT * FROM f_categories";    
    /**
     * @var string
     */
    private $querySubCat = "SELECT * FROM f_sub_categories";    
    /**
     * @var string
     */
    private $queryTopic = "SELECT * FROM f_topics";    
    /**
     * @var string
     */
    private $queryMessage = "SELECT * FROM f_messages";
    

    public function getCategories(): array
    {
        $stmt = $this->connect()->query($this->queryCat);
        $cat = $stmt->fetchAll(PDO::FETCH_CLASS, Category::class);
        return $cat;
    }
    
    public function getCategory(int $id, string $name): Category
    {
        $stmt = $this->connect()->prepare("$this->queryCat WHERE id=:id AND name=:name");
        $stmt->execute(['id' => $id, 'name' => $name]);
        if($stmt->rowCount() == 1){
            $stmt->setFetchMode(PDO::FETCH_CLASS,Category::class);
            $cat = $stmt->fetch();
            return $cat;
        }
        throw new NoFoundException("No category matches with this Id: $id or this name: $name");
    }
    
    public function getCategoryName(string $name): Category
    {
        $stmt = $this->connect()->prepare("$this->queryCat WHERE name=:name");
        $stmt->execute(['name' => $name]);
        if($stmt->rowCount() == 1){
            $stmt->setFetchMode(PDO::FETCH_CLASS,Category::class);
            $cat = $stmt->fetch();
            return $cat;
        }
        throw new NoFoundException("No category matches with this name : $name");
    }

    public function getSubCategories(int $idCat): array
    {
        $stmt = $this->connect()->prepare("$this->querySubCat WHERE id_categories=:idCat");
        $stmt->execute(['idCat' => $idCat]);
        $subCat = $stmt->fetchAll(PDO::FETCH_CLASS, SubCategory::class);
        return $subCat;
    }

    public function getSubCategory(int $id, string $name): SubCategory
    {
        $stmt = $this->connect()->prepare("$this->querySubCat WHERE id=:id AND name=:name");
        $stmt->execute(['id' => $id, 'name' => $name]);
        if($stmt->rowCount() == 1){
            $stmt->setFetchMode(PDO::FETCH_CLASS,SubCategory::class);
            $subCat = $stmt->fetch();
            return $subCat;
        }
        throw new NoFoundException("No sub-category matches with this id : $id or this name : $name");
    }

    public function getSubCategoryName(string $name): SubCategory
    {
        $stmt = $this->connect()->prepare("$this->querySubCat WHERE name=:name");
        $stmt->execute(['name' => $name]);
        if($stmt->rowCount() == 1){
            $stmt->setFetchMode(PDO::FETCH_CLASS,SubCategory::class);
            $subCat = $stmt->fetch();
            return $subCat;
        }
        throw new NoFoundException("No sub-category matches with this name : $name");
    }
    
    /**
     * getLastSubCategories for the forum/index
     *
     * @param  int $idCat
     * @return array last 5 categories
     */
    public function getLastSubCategories(int $idCat): array
    {
        $stmt = $this->connect()->prepare("$this->querySubCat WHERE id_categories=:idCat LIMIT 5");
        $stmt->execute(['idCat' => $idCat]);
        $cat = $stmt->fetchAll(PDO::FETCH_CLASS, SubCategory::class);
        return $cat;
    }
    
    public function getTopics(int $idSubCat): array
    {
        $stmt = $this->connect()->prepare("$this->queryTopic WHERE id_sub_categories=:idSubCat ORDER BY date_time_creation DESC");
        $stmt->execute(['idSubCat' => $idSubCat]);
        $topic = $stmt->fetchAll(PDO::FETCH_CLASS, Topic::class);
        return $topic;
    }

    public function getTopic(int $id, string $title): Topic
    {
        // $stmt = $this->connect()->prepare("
        //     $this->queryTopic t
        //     LEFT JOIN members m
        //     ON id_members = m.id
        //     WHERE t.id=:id AND t.title=:title
        //     ORDER BY date_time_creation DESC");
        // $stmt->execute(['id' => $id, 'title' => $title]);
        // if($stmt->rowCount() == 1){
        //     $stmt->setFetchMode(PDO::FETCH_CLASS,Topic::class);
        //     $topic = $stmt->fetch();
        //     return $topic;
        // }
        // throw new NoFoundException("No topic matches with this id = $id or this title : $title");

        $stmt = $this->connect()->prepare("
            $this->queryTopic t
            LEFT JOIN members m
            ON id_members = m.id
            WHERE t.id=:id
            ORDER BY date_time_creation DESC");
        $stmt->execute(['id' => $id]);
        if($stmt->rowCount() == 1){
            $stmt->setFetchMode(PDO::FETCH_CLASS,Topic::class);
            $topic = $stmt->fetch();
            return $topic;
        }
        throw new NoFoundException("No topic matches with this id = $id");
    }

    public function getLastMessageIndex(int $idSubCat): Message
    {
        $stmt = $this->connect()->prepare("
            $this->queryMessage 
            LEFT JOIN members m
            ON id_members = m.id
            WHERE id_sub_categories=:idSubCat
            LIMIT 1");
        $stmt->execute(['idSubCat' => $idSubCat]);
        $stmt->setFetchMode(PDO::FETCH_CLASS,Message::class);
        $lastMsg = $stmt->fetch();
        return $lastMsg;
    }

    public function getLastMessage(int $idTopic): Message
    {
        $stmt = $this->connect()->prepare("
            $this->queryMessage 
            LEFT JOIN members m
            ON id_members = m.id
            WHERE id_topic=:idTopic
            LIMIT 1");
        $stmt->execute(['idTopic' => $idTopic]);
        $stmt->setFetchMode(PDO::FETCH_CLASS,Message::class);
        $lastMsg = $stmt->fetch();
        return $lastMsg;
    }

    public function getMessage(int $idTopic): array
    {
        $stmt = $this->connect()->prepare("
            $this->queryMessage
            LEFT JOIN members m
            ON id_members = m.id 
            WHERE id_topic=:idTopic 
            ORDER BY date_time_message ASC");
        $stmt->execute(['idTopic' => $idTopic]);
        $msg = $stmt->fetchAll(PDO::FETCH_CLASS,Message::class);
        return $msg;
    }

    public function countAllCategories(): int
    {
        return CountSql::totalData($this->queryCat);        
    }

    public function countAllSubCategories(): int
    {
        return CountSql::totalData($this->querySubCat);        
    }

    public function countTopics(int $idSubCat): int
    {
        return CountSql::totalData("$this->queryTopic WHERE id_sub_categories = ?", $idSubCat);        
    }

    public function countAllTopics(): int
    {
        return CountSql::totalData($this->queryTopic);        
    }

    public function countMessages(int $idTopic): int
    {
        return CountSql::totalData("$this->queryMessage WHERE id_topic = ?", $idTopic);
    }

    public function countAllMessages(): int
    {
        return CountSql::totalData($this->queryMessage);
    }
    
    /**
     * Count messages of all the topic from the subCat
     */
    public function countMessagesWithSubCat(int $idSubCat): int
    {
        return CountSql::totalData("$this->queryMessage WHERE id_sub_categories = ?", $idSubCat);
    }
}
