<?php
namespace App\Form;

use App\Manager\ForumDatabase;
use App\Manager\Database;
use App\Manager\UserDatabase;
use App\Model\Forum\Topic;
use DateTime;

class AddTopic extends AddData
{        
    private $category;
    private $subCat;    
    /**
     * @var Topic
     */
    private $topic;

    public function validateTopic(): self
    {
        $data = new Validator($this->data);
        $data->check('required', ['title', 'name', 'subCat', 'subject']);
        $data->check('used', 'title', 'f_topics');
        $data->check('exist', 'name', 'members');
        $data->check('lengthBetween', ['name', 'title'], 2, 20);

        $this->resultValidator  = $data->validateForm();
        $this->errors           = $data->getErrors();
        
        return $this;
    }

    public function createTopic(array $data): void
    {
        $arrayTopic = $this->arrayDataTopic($data);
        $topic      = new Topic();
        Database::hydrate($topic, $arrayTopic);
        
        $id = $this->create([
            'id_sub_categories'     => $topic->getIdSubCat(),
            'id_members'            => $topic->getIdMember(),
            'title'                 => $topic->getTitle(),
            'subject'               => $topic->getSubject(),
            'date_time_creation'    => $topic->getDateTimeCreation()->format('Y-m-d h:i:s')
        ], 'f_topics');
        $topic->setId($id);
        $this->topic = $topic;
    }

    public function getUrlSubCategory(string $subCategory): string
    {
        $subCat     = $this->getSubCategory($subCategory);
        $catName    = $this->getCategory($subCat->getIdCategory())->getName();

        return $catName.'/'.$subCat->getUrlName().'-'.$subCat->getId();
    }

    public function getUrlTopic()
    {   
        $catName    = $this->category->getName();
        $subCatName = $this->subCat->getUrlName();
        $topic      = $this->topic->getTitle().'-'.$this->topic->getId();

        return $catName.'/'.$subCatName.'/'.$topic;
    }

    private function getSubCategory(string $subCategory): Object
    {
        $stmt = new ForumDatabase();
        return $this->subCat = $stmt->getSubCategoryName($subCategory);
    }

    private function getCategory(int $idCat): Object
    {
        $stmt = new ForumDatabase();
        return $this->category = $stmt->getCategoryById($idCat);
    }

    private function getIdName(string $name): int
    {
        $stmt = new UserDatabase();
        return $stmt->getMemberByName($name)->getId();
    }

    private function arrayDataTopic(array $data)
    {
        $idSubCat   = $this->subCat->getId();
        $date       = date('Y-m-d H:i:s');
        $idName     = $this->getIdName($data['name']);

        $dataTopic = [
            'idSubCat'  => $idSubCat,
            'idMember'  => $idName,
            'title'     => $data['title'],
            'subject'   => $data['subject'],
            'dateTimeCreation' => $date
        ];
        return $dataTopic;
    }
}