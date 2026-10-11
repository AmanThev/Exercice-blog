<?php
namespace App\Model;

use DateTime;

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
    private $password; 
    /**
     * @var string
     */
    private $description;
    /**
     * @var string varchar (5)
     */
    private $position;
    /**
     * @var varchar
     */
    private $token;

    /**
     * @var date
     */
    private $reset_at; 

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return htmlspecialchars((string)$this->name);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_ARGON2ID);
    }

    public function getDescription(): string
    {
        return htmlspecialchars((string)$this->description);
    }

    public function getPosition(): string
    {
        return htmlspecialchars((string)$this->position);
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Date of the last password reset request (null if there never was one)
     */
    public function getDate(): ?DateTime
    {
        return $this->reset_at !== null ? new DateTime($this->reset_at) : null;
    }

}