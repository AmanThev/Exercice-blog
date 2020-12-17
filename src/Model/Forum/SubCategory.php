<?php
namespace App\Model\Forum;

use App\Helpers\Text;
use App\URL\CreateUrl;
use DateTime;

class SubCategory
{    
    /**
     * @var int
     */
    private $id;
    /**
     * @var string varchar(100)
     */
    private $name;

    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return htmlspecialchars($this->name);
    }

    public function getUrlName(): string
    {
        return CreateUrl::urlTitle($this->name);
    }
}