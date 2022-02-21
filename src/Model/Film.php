<?php
namespace App\Model;

use App\Helpers\Text;
use App\URL\CreateUrl;
use DateTime;

class Film
{    
    /**
     * @var int
     */
    private $id;    
    /**
     * @var string varchar(250)
     */
    private $admin_id;    
    /**
     * @var string varchar(250)
     */
    private $title;    
    /**
     * @var string varchar(250)
     */
    private $poster;    
    /**
     * @var date year
     */
    private $date;    
    /**
     * @var string varchar(250)
     */
    private $director;    
    /**
     * @var string varchar(250)
     */
    private $production;    
    /**
     * @var string varchar(250)
     */
    private $writer;
    /**
     * The first tree actors
     * 
     * @var string varchar(250)
     */
    private $cast;    
    /**
     * @var string varchar(250)
     */
    private $genre;    
    /**
     * @var string
     */
    private $synopsis;    
    /**
     * @var string
     */
    private $review;    
    /**
     * @var int
     */
    private $score;       
    /**
     * $slug = films
     *
     * @var string varchar(5)
     */
    private $slug;      
    /**
     * @var string
     */
    private $name;  


    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getIdAdmin(): int
    {
        return $this->admin_id;
    }

    public function setIdAdmin(int $idAdmin): void
    {
        $this->admin_id = $idAdmin;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = htmlentities(trim($title));
    }

    public function getUrlTitle()
    {
        return CreateUrl::urlTitle($this->title);
    }

    public function getUrlTitleCheck(){
        return CreateUrl::urlTitleCheck($this->title);
    }
    
    public function getPoster(): string
    {
        return htmlspecialchars($this->poster);
    }

    public function setPoster($poster): void
    {
        $this->poster = $poster;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setDate(int $date): void
    {
        $this->date = trim($date); 
    }    

    public function getDirector(): string
    {
        return $this->director;
    }

    public function setDirector(string $director): void
    {
        $this->director = htmlspecialchars(trim($director));
    }

    public function getProduction(): string
    {
        return $this->production;
    }

    public function setProduction(string $production): void
    {
        $this->production = htmlspecialchars(trim($production));
    }

    public function getWriter(): string
    {
        return $this->writer;
    }

    public function setWriter(string $writer): void
    {
        $this->writer = htmlspecialchars(trim($writer));
    }

    public function getCast(): array
    {
        $actors 		= $this->cast;
        $array_actors 	= explode(',', $actors);
        return $array_actors;
    }

    public function setCast(string $cast): void
    {
        $this->cast = htmlspecialchars(trim($cast));
    }

    public function getGenre(): string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): void
    {
        $this->genre = htmlspecialchars(trim($genre));
    }

    public function getExcerptContent(): string
    {
        return nl2br(Text::excerpt($this->synopsis, 60));
    }

    public function getExcerptSynopsis(): string
    {
        return nl2br(Text::excerpt($this->synopsis, 235));
    }

    public function getSynopsis(): string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): void
    {
        $this->synopsis = htmlspecialchars(trim($synopsis));
    }
    
    public function getReview(): string 
    {
        $review         = $this->review;
        $spoilerMarker 	= array('[Spoiler]', '[/Spoiler]');
        $replaceSpoiler = array('<span class="spoiler">', '</span>');
        $review 		= str_ireplace($spoilerMarker, $replaceSpoiler, $review);
        return $review;
    }
    
    public function setReview(string $review): void
    {
        $this->review = htmlspecialchars(trim($review));
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function getAuthor(): string
    {
        return htmlspecialchars($this->name);
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    } 
}