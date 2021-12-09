<?php
namespace App\Model;

use DateTime;

class Comment
{        
    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $index_id;
    /**
     * @var string
     */
    private $pseudo;
    /**
     * @var string
     */
    private $comment;
    /**
     * @var date
     */
    private $comment_date;
    /**
     * @var int|Null
     */
    private $edit;
    /**
     * Only for the reviews
     * 
     * @var int
     */
    private $rating_film;    
    /**
     * @var string
     */
    private $title;    
    /**
     * @var int
     */
    private $best;
    

    public function getId(): int
    {
        return $this->id;
    }

    public function getIndexId(): int
    {
        return $this->index_id;
    }

    public function getPseudo(): string
    {
        return htmlspecialchars($this->pseudo);
    }

    public function getComment(): string
    {
        return nl2br(htmlspecialchars($this->comment));
    }

    public function getCommentFilm(): string
    {
        $comment        = htmlspecialchars($this->comment);
        $spoilerMarker 	= array('[Spoiler]', '[/Spoiler]');
        $replaceSpoiler = array('<span class="spoiler">', '</span>');
        return nl2br(str_ireplace($spoilerMarker, $replaceSpoiler, $comment));
    }

    public function getDate()
    {
        return new DateTime($this->comment_date);
    }
    
    public function getEdit(): ?int
    {
        return $this->edit;
    }

    public function getRatingFilm(): int
    {
        return $this->rating_film;
    }

    public function getTitle(): string
    {
        return htmlspecialchars($this->title);
    }
    
    /**
     * Number of the best Comment
     *
     */
    public function getBest(): int
    {
        return $this->best;
    }
}