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
     * title of the subCat
     * @var string varchar(100)
     */
    private $name;    
    /**
     * @var int
     */
    private $id_categories;

    
    /**
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

    public function getName(): string
    {
        return htmlspecialchars($this->name);
    }

    public function setName(string $name): void
    {
        $this->name = htmlentities(trim($name));
    }

    public function getIdCategory(): int
    {
        return $this->id_categories;
    }

    public function setIdCategory(int $idCat): void
    {
        $this->id_categories = $idCat;
    }

    public function getUrlName(): string
    {
        return CreateUrl::urlTitle($this->name);
    }
}