<?php
namespace App\Manager;

use \PDO;
use App\Model\Comment;
use App\SQL\CountSql;

class CommentDatabase extends Database
{
    /**
     * The comment tables, and the table of what they are about.
     * A table name is only put in a query after being checked against this list.
     */
    private const TABLES = [
        'comments_post' => 'posts',
        'comments_film' => 'films',
    ];

    /**
     * @return string the table the comments are about ('posts' or 'films')
     */
    private function targetTable(string $tabName): string
    {
        if(!isset(self::TABLES[$tabName])){
            throw new \Exception("Unknown comment table: $tabName");
        }
        return self::TABLES[$tabName];
    }

    public function getCommentById(string $tabName, int $id): array
    {
        $sql = "SElECT * FROM $tabName";
        return $this->getAllDataByField($sql, 'index_id', $id, "Comment");
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
        $sql = "SELECT * FROM $tabName";
        return $this->getAllData($sql, "Comment");
    }

    public function getLastComment(string $tabName): Comment
    {
        $sql = "SELECT * FROM $tabName ORDER BY comment_date DESC Limit 1";
        return $this->getData($sql, "Comment");
    }

    public function mostComments(string $table): array
    {
        $this->targetTable($table);
        $stmt = $this->connect()->query("SELECT index_id, count(index_id) AS best FROM $table GROUP BY index_id");
        $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$counts){
            return [];
        }
        // the highest number first, then every post / film that reaches it
        $max = max(array_column($counts, 'best'));
        return array_values(array_filter($counts, fn($row) => $row['best'] == $max));
    }
    
    /**
     * count the number of comments
     */
    public function countCommentByIndexId(int $indexId, string $table): Comment
    {
        $stmt = $this->connect()->prepare("SELECT index_id, count(index_id) AS best FROM $table WHERE index_id=:indexId GROUP BY index_id");
        $stmt->execute(['indexId' => $indexId]);
        $stmt->setFetchMode(PDO::FETCH_CLASS,Comment::class);
        $number = $stmt->fetch();
        return $number;
    }
    
    /**
     * Get the total of the vote for each film & Sum all the vote for each film
     */
    public function bestRating(): array
    {
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

    /**
     * Every comment of a table, the most recent first, with the title of the post / film
     * it is about (null if that post / film does not exist any more).
     * Each comment is an array with all the columns + "target_title".
     *
     * @param string $tabName "comments_post" or "comments_film"
     */
    public function getCommentsWithTitle(string $tabName): array
    {
        $target = $this->targetTable($tabName);
        $stmt = $this->connect()->query("
            SELECT c.*, t.title AS target_title
            FROM $tabName c
            LEFT JOIN $target t ON c.index_id = t.id
            ORDER BY c.comment_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $tabName "comments_post" or "comments_film"
     */
    public function deleteComment(string $tabName, int $id): void
    {
        $this->targetTable($tabName);
        $stmt = $this->connect()->prepare("DELETE FROM $tabName WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function findTitle(int $idTable, string $tableComment, string $table): Comment
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