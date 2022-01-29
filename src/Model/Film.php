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

    public function getIdAdmin(): int
    {
        return $this->admin_id;
    }

    public function getTitle(): string
    {
        return trim(htmlspecialchars($this->title));
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

    public function getDate()
    {
        return $this->date;
    }
    
    public function getDirector(): string
    {
        return htmlspecialchars($this->director);
    }

    public function getProduction(): string
    {
        return htmlspecialchars($this->production);
    }

    public function getWriter(): string
    {
        return htmlspecialchars($this->writer);
    }

    public function getCast(): array
    {
        $actors 		= htmlspecialchars($this->cast);
        $array_actors 	= explode(',', $actors);
        return $array_actors;
    }

    public function getGenre(): string
    {
        return htmlspecialchars($this->genre);
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
        return nl2br(htmlspecialchars($this->synopsis));
    }
    
    public function getReview(): string 
    {
        $review         = htmlspecialchars($this->review);
        $spoilerMarker 	= array('[Spoiler]', '[/Spoiler]');
        $replaceSpoiler = array('<span class="spoiler">', '</span>');
        $review 		= str_ireplace($spoilerMarker, $replaceSpoiler, $review);
        return $review;
    }

    public function getAuthor(): string
    {
        return htmlspecialchars($this->name);
    }
}