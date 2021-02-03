<?php
namespace App\Manager;

use App\Model\Post;
use App\SQL\CountSql;
use App\SQL\Paginate;
use App\URL\CreateUrl;
use \PDO;
use \Exception;


class PostDatabase extends Database
{    
    
    /**
     * @var string
     */
    private $queryPublic = "SELECT * FROM posts WHERE public='1'";
    /**
     * @var string
     */
    private $queryAllPost = "SELECT * FROM posts";


    /**
     * get all the public posts with limit from the table post
     *
     * @return array
     */
    public function getPosts(): array
    {
        $sql = $this->queryPublic;
        $pagination = new Paginate($sql);
        $pagination = $pagination->getPagination();
        $sql .= "ORDER BY date DESC $pagination";
        $stmt = $this->connect()->query($sql);
        $posts = $stmt->fetchAll(PDO::FETCH_CLASS, Post::class);
        return $posts;
    }

    public function getAllPosts(): array
    {
        $stmt = $this->connect()->query("
            SELECT * FROM admins a
            RIGHT JOIN posts
            ON a.id = admin_id 
            ORDER BY date DESC");
        $posts = $stmt->fetchAll(PDO::FETCH_CLASS, Post::class);
        return $posts;
    }

    public function getPostsHome(): array
    {
        $sql = $this->queryPublic;
        $pagination = new Paginate($sql, 3);
        $pagination = $pagination->getPagination();
        $sql .= "ORDER BY date DESC $pagination";
        $stmt = $this->connect()->query($sql);
        $posts = $stmt->fetchAll(PDO::FETCH_CLASS, Post::class);
        return $posts;
    }
    
    public function getLastPost(): Post
    {
        $stmt = $this->connect()->query("$this->queryPublic ORDER BY date DESC LIMIT 1");
        $stmt->setFetchMode(PDO::FETCH_CLASS,Post::class);
        $lastpost = $stmt->fetch();
        return $lastpost;
    }

    public function postPaginationNumber(string $status): ?int
    {
        if($status === 'public'){
            $pagination = new Paginate($this->queryPublic);
            $pagination = $pagination->getPaginationNumber();
            return $pagination; 
        }
    }

    public function getPostById(int $id): Post
    {
        $stmt = $this->connect()->prepare("
            $this->queryAllPost p
            LEFT JOIN admins a
            ON admin_id = a.id
            WHERE p.id=:id");
        $stmt->execute(['id' => $id]);
        if($stmt->rowCount() == 1){
            $stmt->setFetchMode(PDO::FETCH_CLASS,Post::class);
            $post = $stmt->fetch();
            return $post;
        }
        throw new Exception('No post matches this id');
    }

    public function getPostByCommentId(int $idPost): Post
    {
        $stmt = $this->connect()->prepare("
            $this->queryAllPost p
            INNER JOIN comments_post
            ON p.id = index_id
            WHERE index_id=:idPost");
        $stmt->execute(['idPost' => $idPost]);
        $stmt->setFetchMode(PDO::FETCH_CLASS,Post::class);
        $post = $stmt->fetch();
        return $post;
    }

    public function getPostByAdminId(int $idAdmin)
    {
        $stmt = $this->connect()->prepare("
            SELECT * FROM admins a
            INNER JOIN posts
            ON admin_id = a.id
            WHERE a.id=:idAdmin");
        $stmt->execute(['idAdmin' => $idAdmin]);
        $posts = $stmt->fetchAll(PDO::FETCH_CLASS, Post::class);
        return $posts;
    }

    public function totalPosts(): int
    {
        return CountSql::totalData($this->queryAllPost);
    }

    public function bestVote(): array
    {
        $stmt = $this->connect()->query("SELECT title, count_like FROM posts WHERE count_like = (SELECT MAX(count_like) FROM posts)");
        $post = $stmt->fetchAll(PDO::FETCH_CLASS, Post::class);
        return $post;
    }

    public function postWritten(int $idName)
    {
        return CountSql::totalData("$this->queryAllPost WHERE admin_id= ?", $idName);
    }
}