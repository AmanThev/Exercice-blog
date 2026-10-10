<?php
namespace App\Manager;

use \PDO;
use App\Model\Film;
use App\SQL\CountSql;
use App\SQL\Paginate;

#[\AllowDynamicProperties]
class FilmDatabase extends Database
{
    /**
     * Number of reviews per page (reviews page and home) : defined once, used everywhere
     */
    private const PER_PAGE_REVIEWS = 6;
    private const PER_PAGE_HOME    = 3;

    private $query = "SELECT * FROM films ";
    private $queryRating = "SELECT rating_film FROM comments_film";
    /**
     * Film + name of its author.
     * "f.*" keeps the id of the film : with a plain "SELECT *" the id of the admin
     * overwrote it (both tables have an "id" column).
     *
     * @var string
     */
    private $queryWithAuthor = "SELECT f.*, a.name FROM films f LEFT JOIN admins a ON f.admin_id = a.id";

    /**
     * get All reviews with a limit
     *
     * @return array
     */
    public function getFilms(): array
    {
        $pagination = (new Paginate($this->query, self::PER_PAGE_REVIEWS))->getPagination();
        return $this->getAllData("$this->query ORDER BY date DESC $pagination", "Film");
    }

    public function getAllFilms(): array
    {
        return $this->getAllData("$this->queryWithAuthor ORDER BY f.date DESC", "Film");
    }

    public function getFilmsHome(): array
    {
        $pagination = (new Paginate($this->query, self::PER_PAGE_HOME))->getPagination();
        return $this->getAllData("$this->query ORDER BY id DESC $pagination", "Film");
    }

    public function getLastFilm(): Film
    {
        return $this->getData("$this->query ORDER BY id DESC LIMIT 1", "Film");
    }

    public function getLastFilms(): array
    {
        return $this->getAllData("$this->query ORDER BY id DESC LIMIT 5", "Film");
    }

    public function getFilmById(int $id): Film
    {
        return $this->getDataByField($this->queryWithAuthor, 'f.id', $id, "Film");
    }

    /**
     * The "index_id" of a comment is the id of its film
     */
    public function getFilmByCommentId(int $idFilm): Film
    {
        return $this->getFilmById($idFilm);
    }

    public function getFilmByAdminId(int $idAdmin): array
    {
        return $this->getAllDataByField($this->queryWithAuthor, 'f.admin_id', $idAdmin, "Film");
    }

    public function filmPaginationNumber(): ?int
    {
        return (new Paginate($this->query, self::PER_PAGE_REVIEWS))->getPaginationNumber();
    }

    public function totalVote(int $id): int
    {
        $sql = "$this->queryRating WHERE index_id = ?";
        return CountSql::totalData($sql, $id) + 1 ; // +1 = admin vote
    }
    
    public function totalRating(int $id): float
    {
        $sql = "$this->queryRating WHERE index_id = ?";
        $totalVoteUser = CountSql::totalColumn($sql, $id);
    
        $stmt = $this->connect()->prepare("SELECT score FROM films WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $totalVoteAdmin = (int)$stmt->fetchColumn(0);

        $resultRating 	= round(($totalVoteUser + $totalVoteAdmin) / $this->totalVote($id), 1);
        
        return $resultRating;
    }

    public function totalFilms(): int
    {
        return CountSql::totalData($this->query);
    }

    public function reviewWritten(int $idName)
    {
        return CountSql::totalData("$this->query WHERE admin_id = ?", $idName);
    }

    /**
     * Delete a film with its comments.
     * The poster file is NOT deleted : it could be shared by several rows.
     */
    public function deleteFilm(int $id): void
    {
        $pdo = $this->connect();
        try{
            $pdo->beginTransaction();
            $pdo->prepare("DELETE FROM comments_film WHERE index_id = :id")->execute(['id' => $id]);
            $pdo->prepare("DELETE FROM films WHERE id = :id")->execute(['id' => $id]);
            $pdo->commit();
        }catch(\Throwable $e){
            if($pdo->inTransaction()){
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public function findFilm(string $keyword): array
    {
        $sql = $this->query;
        $sql .= " WHERE MATCH(title, director, production, writer, cast, synopsis, genre, review)
                AGAINST (:keyword IN NATURAL LANGUAGE MODE)";
        $stmt = $this->connect()->prepare($sql);
        $stmt->execute(['keyword' => $keyword]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, Film::class);
    }
}