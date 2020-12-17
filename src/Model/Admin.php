<?php
namespace App\Model;


class Admin
{      
    /**
     * @var int
     */
    private $id;    
    /**
     * @var string varchar (250)
     */
    private $name;    
    /**
     * @var string varchar(250)
     */
    private $email;    
    /**
     * @var string
     */
    private $description;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return htmlspecialchars($this->name);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDescription(): string
    {
        return htmlspecialchars($this->description);
    }

}