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
     * id_sub_categories on the table f_sub_categories
     *
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
    
    public function getIdSubCat(): ?int
    {
        return $this->id_sub_categories;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return htmlspecialchars($this->title);
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

    public function getDateTimeCreation()
    {
        return new DateTime($this->date_time_creation);
    }
    
    public function getResolved(): ?int
    {
        return $this->resolved;
    }

    public function getName(): string
    {
        return htmlspecialchars($this->name);
    }
}