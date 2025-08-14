<?php
namespace App\Manager;

use \PDO;
use App\Model\Post;
use App\SQL\CountSql;
use App\SQL\Paginate;
use App\URL\CreateUrl;
use App\Manager\Exception\NotFoundException;

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
     * @var array
     */
    private $status = [
        "public"    => '1',
        "private"   => '0',
        "all"       => "'0' OR '1'"
    ];


    public function getPosts($display)
    {
        $status = $this->status[$display];
        $sql = "SELECT * FROM admins a
            RIGHT JOIN posts 
            ON a.id = admin_id 
            WHERE public=$status 
            ORDER BY date DESC";
        return $this->getAllData($sql, "POST");
    }

    /**
     * get all the public posts with limit from the table post
     *
     * @return array
     */
    public function getPostsPublic(): array
    {
        $sql = $this->queryPublic;
        $pagination = new Paginate($sql);
        $pagination = $pagination->getPagination();
        $sql .= "ORDER BY date DESC $pagination";
        return $this->getAllData($sql, 'Post');
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
        $sql = $this->queryPublic;
        $sql .= "ORDER BY date DESC LIMIT 1";
        return $this->getData($sql, 'Post');
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
        $sql  = $this->queryAllPost;
        $sql .= " p LEFT JOIN admins a ON admin_id = a.id";
        return $this->getDataByField($sql, 'p.id', $id, "Post");
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
        $sql = "SELECT * FROM admins a INNER JOIN posts ON admin_id = a.id";
        return $this->getAllDataByField($sql, 'a.id', $idAdmin, "Post");
    }

    public function totalPosts(): int
    {
        return CountSql::totalData($this->queryAllPost);
    }

    public function bestVote(): array
    {
        $sql = "SELECT title, count_like FROM posts WHERE count_like = (SELECT MAX(count_like) FROM posts)";
        return $this->getAllData($sql, "Post");
    }

    public function postWritten(int $idName)
    {
        return CountSql::totalData("$this->queryAllPost WHERE admin_id= ?", $idName);
    }

    public function createPost(Post $post): void
    {
        $stmt = $this->connect()->prepare("INSERT INTO posts SET 
            title = :title,
            admin_id = :admin_id, 
            content = :content, 
            public = :public, 
            date = NOW()"
        );
        $addPost = $stmt->execute([
            'title'     => $post->getTitle(),
            'admin_id'  => $post->getIdAdmin(),
            'content'   => $post->getContent(),
            'public'    => $post->getPublic()
        ]);
        if($addPost === false){
            throw new \Exception("Error, impossible to add the post");
        }
        $post->setId($this->pdo->lastInsertId());
    }

    public function findPost(string $keyword): array
    {
        $sql = $this->queryAllPost;
        $sql .= " WHERE public='1' AND MATCH(title, content)
                AGAINST (:keyword IN NATURAL LANGUAGE MODE)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['keyword' => $keyword]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, POST::class);
    }
}