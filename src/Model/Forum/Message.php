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

    
    public function getId(): int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return htmlspecialchars($this->message);
    }

    public function getDateTimeMessage()
    {
        return new DateTime($this->date_time_message);
    }

    public function getName(): string
    {
        return htmlspecialchars($this->name);
    }
}