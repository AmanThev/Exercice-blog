<?php
namespace App\Manager;

use \PDO;
use App\Model\Film;
use App\SQL\CountSql;
use App\SQL\Paginate;
use App\Manager\Exception\NotFoundException;

class FilmDatabase extends Database
{

    private $query = "SELECT * FROM films ";
    private $queryRating = "SELECT rating_film FROM comments_film";
    
    /**
     * get All reviews with a limit
     *
     * @return array
     */
    public function getFilms(): array
    {
        $sql = $this->query;
        $pagination = new Paginate($sql);
        $pagination = $pagination->getPagination();
        $sql .= "ORDER BY date DESC $pagination";
        $stmt = $this->connect()->query($sql);
        $films = $stmt->fetchAll(PDO::FETCH_CLASS, Film::class);
        
        return $films;
    }

    public function getAllFilms(): array
    {
        $stmt = $this->connect()->query("
            SELECT * FROM admins a 
            RIGHT JOIN films
            ON a.id = admin_id
            ORDER BY date DESC");
        $films = $stmt->fetchAll(PDO::FETCH_CLASS, Film::class);
        return $films;
    }

    public function getFilmsHome(): array
    {
        $sql = $this->query;
        $pagination = new Paginate($sql, 3);
        $pagination = $pagination->getPagination();
        $sql .= "ORDER BY id DESC $pagination";
        $stmt = $this->connect()->query($sql);
        $films = $stmt->fetchAll(PDO::FETCH_CLASS, Film::class);
        return $films;
    }

    public function getLastFilm(): Film
    {
        $stmt = $this->connect()->query("$this->query ORDER BY id DESC Limit 1");
        $stmt->setFetchMode(PDO::FETCH_CLASS,Film::class);
        $lastFilm = $stmt->fetch();
        return $lastFilm;
    }

    public function getLastFilms(): array
    {
        $stmt = $this->connect()->query("$this->query ORDER BY id DESC Limit 5");
        $lastFilms = $stmt->fetchAll(PDO::FETCH_CLASS, Film::class);
        return $lastFilms;
    }

    public function getFilmById(int $id): Film
    {
        $stmt = $this->connect()->prepare("
            $this->query f
            LEFT JOIN admins a
            ON admin_id = a.id
            WHERE f.id=:id");
        $stmt->execute(['id' => $id]);
        if($stmt->rowCount() == 1){
            $stmt->setFetchMode(PDO::FETCH_CLASS,Film::class);
            $film = $stmt->fetch();
            return $film;
        }
        throw new NotFoundException('Film', $id);
    }
    
    public function getFilmByCommentId(int $idFilm)
    {
        $stmt = $this->connect()->prepare("
            $this->query f
            INNER JOIN comments_film
            ON f.id = index_id
            WHERE index_id=:idFilm");
        $stmt->execute(['idFilm' => $idFilm]);
        $stmt->setFetchMode(PDO::FETCH_CLASS,Film::class);
        $film = $stmt->fetch();
        return $film;
    }

    public function getFilmByAdminId(int $idAdmin)
    {
        $stmt = $this->connect()->prepare("
            SELECT * FROM admins a
            INNER JOIN films
            ON admin_id = a.id
            WHERE a.id=:idAdmin");
        $stmt->execute(['idAdmin' => $idAdmin]);
        $films = $stmt->fetchAll(PDO::FETCH_CLASS, Film::class);
        return $films;
    }

    public function filmPaginationNumber(): ?int
    {
        $pagination = new Paginate($this->query);
        $pagination = $pagination->getPaginationNumber();
        return $pagination; 
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
    
        $stmt = $this->connect()->prepare("SELECT score FROM Films WHERE id = :id");
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

    public function createFilm(Film $film): void
    {
        $stmt = $this->connect()->prepare("INSERT INTO films SET title = :title, admin_id = :admin_id, date = :date, director = :director, production = :production, writer = :writer, cast = :cast, genre = :genre, synopsis = :synopsis, review = :review, score = :score");
        $addFilm = $stmt->execute([
            'title' => $film->getTitle(),
            'admin_id' => $film->getIdAdmin(),
            'date' => $film->getDate(),
            'director' => $film->getDirector(),
            'production' => $film->getProduction(),
            'cast' => $film->getCast(),
            'genre' => $film->getGenre(),
            'synopsis' => $film->getSynopsis(),
            'review' => $film->getReview(),
            'score' => $film->getScore()
        ]);
        if($addFilm === false){
            throw new \Exception("Error, impossible to add the film");
        }
        $film->setId($this->pdo->lastInsertId());
    }
}