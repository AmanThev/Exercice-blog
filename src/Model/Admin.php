<?php
namespace App\Model;

use App\Helpers\Text;
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

    /**
     * The beginning of the description (cut after a word, then " ..."), for the team page.
     * The text is cut before it is escaped, so an HTML entity is never cut in half.
     */
    public function getExcerptDescription(int $limit = 150): string
    {
        return htmlspecialchars(Text::excerpt(trim((string)$this->description), $limit));
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