<?php
namespace App\Manager;

use \PDO;
use App\Model\Post;
use App\SQL\CountSql;
use App\SQL\Paginate;

class PostDatabase extends Database
{
    /**
     * Number of posts per page (blog and home) : defined once, used everywhere
     */
    private const PER_PAGE_BLOG = 8;
    private const PER_PAGE_HOME = 3;

    /**
     * @var string
     */
    private $queryPublic = "SELECT * FROM posts WHERE public='1'";
    /**
     * @var string
     */
    private $queryAllPost = "SELECT * FROM posts";
    /**
     * Post + name of its author.
     * "p.*" keeps the id of the post : with a plain "SELECT *" the id of the admin
     * overwrote it (both tables have an "id" column).
     *
     * @var string
     */
    private $queryWithAuthor = "SELECT p.*, a.name FROM posts p LEFT JOIN admins a ON p.admin_id = a.id";

    /**
     * @var array
     */
    private $status = [
        "public"    => 1,
        "private"   => 0
    ];


    /**
     * @param  string $display "public" or "private" ; anything else = all the posts
     */
    public function getPosts(string $display = 'all'): array
    {
        $sql = $this->queryWithAuthor;
        if(isset($this->status[$display])){
            $sql .= " WHERE p.public = " . $this->status[$display];
        }
        return $this->getAllData("$sql ORDER BY p.date DESC", 'Post');
    }

    public function getAllPosts(): array
    {
        return $this->getPosts('all');
    }

    /**
     * get the public posts of one page (the page comes from the url, see Paginate)
     */
    private function getPublicPage(int $perPage): array
    {
        $pagination = (new Paginate($this->queryPublic, $perPage))->getPagination();
        return $this->getAllData("$this->queryPublic ORDER BY date DESC $pagination", 'Post');
    }

    public function getPostsPublic(): array
    {
        return $this->getPublicPage(self::PER_PAGE_BLOG);
    }

    public function getPostsHome(): array
    {
        return $this->getPublicPage(self::PER_PAGE_HOME);
    }

    public function getLastPost(): Post
    {
        return $this->getData("$this->queryPublic ORDER BY date DESC LIMIT 1", 'Post');
    }

    public function postPaginationNumber(string $status): ?int
    {
        if($status === 'public'){
            return (new Paginate($this->queryPublic, self::PER_PAGE_BLOG))->getPaginationNumber();
        }
        return null;
    }

    public function getPostById(int $id): Post
    {
        return $this->getDataByField($this->queryWithAuthor, 'p.id', $id, 'Post');
    }

    /**
     * The "index_id" of a comment is the id of its post
     */
    public function getPostByCommentId(int $idPost): Post
    {
        return $this->getPostById($idPost);
    }

    public function getPostByAdminId(int $idAdmin): array
    {
        return $this->getAllDataByField($this->queryWithAuthor, 'p.admin_id', $idAdmin, 'Post');
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

    public function deletePost(int $id): void
    {
        $pdo = $this->connect();
        try{
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM comments_post WHERE index_id = :id")->execute(['id' => $id]);
            $pdo->prepare("DELETE FROM votes WHERE ref = 'posts' AND ref_id = :id")->execute(['id' => $id]);
            $pdo->prepare("DELETE FROM posts WHERE id = :id")->execute(['id' => $id]);
            $pdo->commit();
        }catch(\Throwable $e){
            if($pdo->inTransaction()){
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public function findPost(string $keyword): array
    {
        $sql = $this->queryAllPost;
        $sql .= " WHERE public='1' AND MATCH(title, content)
                AGAINST (:keyword IN NATURAL LANGUAGE MODE)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['keyword' => $keyword]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Post::class);
    }
}