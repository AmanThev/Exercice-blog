<?php
namespace App\Model;

use App\Helpers\Text;
use App\URL\CreateUrl;
use DateTime;

#[\AllowDynamicProperties]
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
    
        
    /**
     * Id post
     *
     * @return int
     */
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
    
    public function getUrlTitle(): string
    {
        return CreateUrl::urlTitle($this->title);
    }

    public function getUrlTitleCheck()
    {
        return CreateUrl::urlTitleCheck($this->title);
    }
    
    public function getPicture()
    {
        return htmlentities($this->picture);
    }

    public function setPicture($picture): void
    {
        $this->picture = $picture;
    }
    
    public function getExcerptContent(): string
    {
        return nl2br(htmlspecialchars(trim(Text::excerpt($this->content, 150))));
    }
        
    /**
     * getExcerptLastContent for the blog.php
     *
     * @return string
     */
    public function getExcerptLastContent(): string
    {
        return nl2br(htmlspecialchars(trim(Text::excerpt($this->content, 550))));
    }
    
    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = htmlspecialchars(trim($content));
    }
    
    public function getDate()
    {
        return new DateTime($this->date);
    }
    
    public function getPublic(): int
    {
        return $this->public;
    }

    public function setPublic(int $public): void
    {
        if($public === 1 || $public === 0){
            $this->public = $public;
        }
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
        return $this->name;
    }

    public function setAuthor($author): void
    {
        $this->name = htmlentities($author);
    }
    
}