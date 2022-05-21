<?php
namespace App\Model\Forum;

use App\Helpers\Text;
use App\URL\CreateUrl;
use DateTime;

class Message
{    
    /**
     * @var int
     */
    private $id;    
    /**
     * @var string
     */
    private $message;    
    /**
     * @var date
     */
    private $date_time_message;

    private $name;
    
    /**
     * @var int
     */
    private $id_sub_categories;

    /**
     * @var int
     */
    private $id_topic;

    /**
     * @var int
     */
    private $id_members;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getMessage(): ?string
    {
        return htmlspecialchars($this->message);
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getDateTimeMessage()
    {
        return new DateTime($this->date_time_message);
    }

    public function setDateTimeMessage(string $date): void
    {
        $this->date_time_message = $date;
    }

    public function getName(): ?string
    {
        return htmlspecialchars($this->name);
    }

    public function getIdSubCat(): int
    {
        return $this->id_sub_categories;
    }

    public function setIdSubCat(int $idSubCat): void
    {
        $this->id_sub_categories = $idSubCat;
    }

    public function getIdTopic(): int
    {
        return $this->id_topic;
    }

    public function setIdTopic(int $idTopic): void
    {
        $this->id_topic = $idTopic;
    }

    public function getIdMember(): int
    {
        return $this->id_members;
    }

    public function setIdMember(int $idMember): void
    {
        $this->id_members = $idMember;
    }
}