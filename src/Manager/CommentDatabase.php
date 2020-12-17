<?php
namespace App\Manager;

use App\Model\Comment;
use App\SQL\CountSql;
use \PDO;
use \Exception;


class CommentDatabase extends Database
{
    public function getCommentById(string $tabName, int $id): array
    {
        $stmt = $this->connect()->prepare("SELECT * FROM $tabName WHERE index_id=:id");
        $stmt->execute(['id' => $id]);
        $comment = $stmt->fetchAll(PDO::FETCH_CLASS, Comment::class);
        return $comment;
    }

    public function totalComment(string $tabNameComment, int $id): int
    {
        $sql = "SELECT * FROM $tabNameComment WHERE index_id= ?";
        return CountSql::totalData($sql, $id);
    }

    public function totalAllComment(string $tabNameComment): int
    {
        return CountSql::totalData("SELECT * FROM $tabNameComment");
    }

    public function getComments(string $tabName): array
    {
        $stmt = $this->connect()->query("SELECT * FROM $tabName");
        $comment = $stmt->fetchAll(PDO::FETCH_CLASS, Comment::class);
        return $comment;
    }

    public function getLastComment(string $tabName): Comment
    {
        $stmt = $this->connect()->query("SELECT * FROM $tabName ORDER BY comment_date DESC Limit 1");
        $stmt->setFetchMode(PDO::FETCH_CLASS,Comment::class);
        $lastComment = $stmt->fetch();
        return $lastComment;
    }

    public function isMember(string $pseudo): int
    {
        $stmt = $this->connect()->prepare("SELECT name FROM members WHERE name=:pseudo");
        $stmt->execute(['pseudo' => $pseudo]);
        return $stmt->rowCount();
    }

    public function isAdmin(string $pseudo): int
    {
        $stmt = $this->connect()->prepare("SELECT name FROM admins WHERE name=:pseudo");
        $stmt->execute(['pseudo' => $pseudo]);
        return $stmt->rowCount();
    }

    public function mostComments($table): array
    {
        $stmt = $this->connect()->query("SELECT index_id, count(index_id) AS best FROM $table GROUP BY index_id");
        $mostCom = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // $mostCom =  max(array_column($mostCom, 'best'));
        $max = 0;
        $result = [];
        foreach( $mostCom as $k => $v ){
            $max = max(array($max, $v['best']));
            if($v['best'] === $max){
                $result [] = $v;
            }
        }
        return $result;
    }
    
    /**
     * count the number of comments
     */
    public function countCommentByIndexId(int $indexId, $table): Comment
    {
        $stmt = $this->connect()->prepare("SELECT index_id, count(index_id) AS best FROM $table WHERE index_id=:indexId GROUP BY index_id");
        $stmt->execute(['indexId' => $indexId]);
        $stmt->setFetchMode(PDO::FETCH_CLASS,Comment::class);
        $number = $stmt->fetch();
        return $number;
    }
    
    /**
     * return the best rating
     */
    public function bestRating(): array
    {
        //Get the total of the vote for each film & Sum all the vote for each film
        $stmt = $this->connect()->query("SELECT index_id, round(SUM(rating_film)/ COUNT(rating_film), 2) AS rating FROM comments_film GROUP BY index_id");
        $bestRating = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $max = 0;
        $result = [];
        foreach( $bestRating as $k => $v){
            $max = max(array_column($bestRating, 'rating'));
            if($v['rating'] === $max){
                $result[] = $v;
            }
        }
        return $result;
    }

    public function userComment(string $name): int
    {
        return CountSql::totalData("SELECT * FROM comments_post WHERE pseudo = ?", $name);
    }

    public function findTitle($idTable, $tableComment, $table): Comment
    {
        $stmt = $this->connect()->prepare("
            SELECT title FROM $tableComment tc
            INNER JOIN $table t
            ON  tc.index_id = t.id
            WHERE index_id=:idTable");
        $stmt->execute(['idTable' => $idTable]);
        $stmt->setFetchMode(PDO::FETCH_CLASS,Comment::class);
        $title = $stmt->fetch();
        return $title;
    }
}