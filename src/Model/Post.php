<?php
namespace App\Model;

use App\Helpers\Text;
use App\URL\CreateUrl;
use DateTime;

class Post
{    
    /**
     * @var int
     */
    private $id;
    /**
     * @var string varchar (250)
     */
    private $admin_id;
    /**
     * @var string varchar (250)
     */
    private $title;
    /**
     * @var string varchar (250)
     */
    private $picture;   
    /**
     * @var string
     */
    private $content;
    /**
     * @var date
     */
    private $date;  
    /**
     * @var int
     */
    private $public;
    /**
     * @var int|Null
     */
    private $count_like;    
    /**
     * @var int|Null
     */
    private $count_dislike;
    /**
     * @var int|Null
     */
    private $edit;    
    /**
     * @var string varchar (5)
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
        return htmlspecialchars($this->title);
    }
    
    public function getUrlTitle(): string
    {
        return CreateUrl::urlTitle($this->title);
    }
    
    public function getPicture()
    {
        return htmlspecialchars($this->picture);
    }
    
    public function getExcerptContent(): string
    {
        return nl2br(htmlspecialchars(Text::excerpt($this->content, 150)));
    }
        
    /**
     * getExcerptLastContent for the blog.php
     *
     * @return string
     */
    public function getExcerptLastContent(): string
    {
        return nl2br(htmlspecialchars(Text::excerpt($this->content, 550)));
    }
    
    public function getContent(): string
    {
        return nl2br(htmlspecialchars($this->content));
    }
    
    public function getDate()
    {
        return new DateTime($this->date);
    }
    
    public function getPublic(): int
    {
        return $this->public;
    }
    
    public function getLike(): int
    {
        return $this->count_like;
    }

    public function getDislike(): int
    {
        return $this->count_dislike;
    }

    public function getEdit(): ?int
    {
        return $this->edit;
    }
    
    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getAuthor(): string
    {
        return htmlspecialchars($this->name);
    }
    
}