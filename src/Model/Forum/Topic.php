<?php
namespace App\Model\Forum;

use App\Helpers\Text;
use App\URL\CreateUrl;
use DateTime;

class Topic
{    
    /**
     * @var int
     */
    private $id;    
    /**
     * @var int
     */
    private $id_sub_categories;
    /**
     * @var int
     */
    private $id_members;
    /**
     * @var string varchar(100)
     */
    private $title;    
    /**
     * @var string
     */
    private $subject;    
    /**
     * @var dateTime
     */
    private $date_time_creation;       
    /**
     * @var int|null
     */
    private $resolved;     
    /**
     * @var string
     */
    private $name;    
    
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    public function getIdSubCat(): ?int
    {
        return $this->id_sub_categories;
    }

    public function setIdSubCat(int $id): void
    {
        $this->id_sub_categories = $id;
    }

    public function getIdMember(): ?int
    {
        return $this->id_members;
    }

    public function setIdMember(int $id): void
    {
        $this->id_members = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return htmlspecialchars($this->title);
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
    
    public function getUrlTitle(): string
    {
        return CreateUrl::urlTitleTopic($this->title);
    }
    
    /**
     * @return string
     */
    public function getSubject(): ?string
    {
        return htmlspecialchars($this->subject);
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getDateTimeCreation()
    {
        return new Datetime($this->date_time_creation);
    }

    public function setDateTimeCreation(string $date): void
    {
        $this->date_time_creation = $date;
    }   

    public function getResolved(): ?int
    {
        return $this->resolved;
    }

    public function setResolved(int $resolved): void
    {
        $this->resolved = $resolved;
    }

    public function getName(): string
    {
        return htmlspecialchars($this->name);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}