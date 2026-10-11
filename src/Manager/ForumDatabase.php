<?php
namespace App\Manager;

use \PDO;
use App\Model\Forum\Category;
use App\Model\Forum\SubCategory;
use App\Model\Forum\Topic;
use App\Model\Forum\Message;
use App\SQL\CountSql;
use App\Manager\Exception\NotFoundException;


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
        return $this->getAllData($this->queryCat, "Category", "Forum");
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
        throw new \Exception("No category matches with this Id: $id or this name: $name");
    }
    
    public function getCategoryName(string $name): Category
    {
        return $this->getDataByField($this->queryCat, 'name', $name, 'Category', 'Forum');
    }

    public function getCategoryById(int $id): Category
    {
        return $this->getDataByField($this->queryCat, 'id', $id, "Category", "Forum");
    }

    public function getSubCategories(int $idCat): array
    {
        return $this->getAllDataByField($this->querySubCat, 'id_categories', $idCat, 'SubCategory', 'Forum');
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
        throw new \Exception("No sub-category matches with this id : $id or this name : $name");
    }

    public function getSubCategoryName(string $name): SubCategory
    {
        return $this->getDataByField($this->querySubCat, 'name', $name, 'SubCategory', 'Forum');
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
        throw new NotFoundException('Topic', $id);
    }

    public function getLastMessageIndex(int $idSubCat): Message
    {
        $stmt = $this->connect()->prepare("
            $this->queryMessage 
            LEFT JOIN members m
            ON id_members = m.id
            WHERE id_sub_categories=:idSubCat
            ORDER BY date_time_message DESC
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
            ORDER BY date_time_message DESC
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

    /**
     * count all the Topics of the sub-Cat
     *
     * @param  int $idSubCat
     * @return int
     */
    public function countTopics(int $idSubCat): int
    {
        return CountSql::totalData("$this->queryTopic WHERE id_sub_categories = ?", $idSubCat);        
    }

    public function countAllTopics(): int
    {
        return CountSql::totalData($this->queryTopic);        
    }
    
    /**
     * Count Topic non resolved
     *
     * @return int
     */
    public function countOpenTopics(): int
    {
        return CountSql::totalData("$this->queryTopic WHERE resolved is NULL");
        
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
     * Number of messages of every topic, in one query
     *
     * @return array [id of the topic => number of messages] (a topic without message is not in the list)
     */
    public function countMessagesByTopic(): array
    {
        $stmt = $this->connect()->query("SELECT id_topic, COUNT(*) AS total FROM f_messages GROUP BY id_topic");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Count messages of all the topic from the subCat
     */
    public function countMessagesWithSubCat(int $idSubCat): int
    {
        return CountSql::totalData("$this->queryMessage WHERE id_sub_categories = ?", $idSubCat);
    }

    public function closeTopic(int $idTopic): void
    {
        $stmt = $this->connect()->prepare("UPDATE f_topics SET resolved = '1' WHERE id = :id");
        $updated = $stmt->execute(['id' => $idTopic]);
        if($updated === false){
            throw new \Exception("Impossible to update the table f_topics");
        }
    }

    public function reopenTopic(int $idTopic): void
    {
        $stmt = $this->connect()->prepare("UPDATE f_topics SET resolved = NULL WHERE id = :id");
        $updated = $stmt->execute(['id' => $idTopic]);
        if($updated === false){
            throw new \Exception("Impossible to update the table f_topics");
        }
    }

    /**
     * Delete a topic with its messages
     */
    public function deleteTopic(int $id): void
    {
        $this->runInTransaction([
            "DELETE FROM f_messages WHERE id_topic = :id",
            "DELETE FROM f_topics WHERE id = :id",
        ], $id);
    }

    /**
     * Delete a sub-category with its topics and their messages
     */
    public function deleteSubCategory(int $id): void
    {
        $this->runInTransaction([
            "DELETE FROM f_messages WHERE id_topic IN (SELECT id FROM f_topics WHERE id_sub_categories = :id)",
            "DELETE FROM f_topics WHERE id_sub_categories = :id",
            "DELETE FROM f_sub_categories WHERE id = :id",
        ], $id);
    }

    /**
     * Delete a category with its sub-categories, their topics and their messages
     */
    public function deleteCategory(int $id): void
    {
        $this->runInTransaction([
            "DELETE FROM f_messages WHERE id_topic IN (
                SELECT t.id FROM f_topics t
                INNER JOIN f_sub_categories s ON t.id_sub_categories = s.id
                WHERE s.id_categories = :id)",
            "DELETE FROM f_topics WHERE id_sub_categories IN (SELECT id FROM f_sub_categories WHERE id_categories = :id)",
            "DELETE FROM f_sub_categories WHERE id_categories = :id",
            "DELETE FROM f_categories WHERE id = :id",
        ], $id);
    }

    /**
     * Run the queries one after the other (each one has exactly one ":id").
     * If one fails, none of them is kept.
     */
    private function runInTransaction(array $queries, int $id): void
    {
        $pdo = $this->connect();
        try{
            $pdo->beginTransaction();
            foreach($queries as $sql){
                $pdo->prepare($sql)->execute(['id' => $id]);
            }
            $pdo->commit();
        }catch(\Throwable $e){
            if($pdo->inTransaction()){
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}