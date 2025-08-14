<?php
namespace App\Model;


class Member
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
     * @var string varchar (250)
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
     * @var int
     */
    private $nbr_like;

    /**
     * @var int
     */
    private $nbr_comment;

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

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return htmlspecialchars($this->name);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
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
        return nl2br(htmlspecialchars($this->description));
    }

    public function getnbrLike(): int
    {
        return $this->nbr_like;
    }

    public function getnbrComment(): int
    {
        return $this->nbr_comment;
    }

    public function getToken(): varchar
    {
        return $this->token;
    }

    public function getDate()
    {
        return new DateTime($this->reset_at);
    }
}