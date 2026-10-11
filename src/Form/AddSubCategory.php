<?php
namespace App\Form;

use App\Manager\ForumDatabase;
use App\Manager\Exception\NotFoundException;

class AddSubCategory extends ForumForm
{
    /**
     * @param string|null $currentName when a sub-category is edited : its name in the database (it may stay the same)
     */
    public function validateSubCategory(?string $currentName = null): self
    {
        $this->ensureStrings(['name', 'id_categories']);

        $data = new Validator($this->data);
        $this->checkName($data, 'f_sub_categories', $currentName);
        $data->validateForm();
        $this->errors = $data->getErrors();

        $this->checkNameShape($currentName);
        if(!$this->categoryExists($this->data['id_categories'])){
            $this->errors['id_categories'][] = 'Choose a category.';
        }
        $this->resultValidator = empty($this->errors);

        return $this;
    }

    /**
     * @return int id of the new sub-category
     */
    public function createSubCategory(): int
    {
        return $this->create($this->fields(), 'f_sub_categories');
    }

    protected function fields(): array
    {
        return [
            'name'          => $this->data['name'],
            'id_categories' => (int)$this->data['id_categories'],
        ];
    }

    /**
     * The category chosen in the form must exist (the list can be changed by hand in the browser).
     */
    private function categoryExists(string $id): bool
    {
        if(!ctype_digit($id)){
            return false;
        }
        try{
            (new ForumDatabase())->getCategoryById((int)$id);
            return true;
        }catch(NotFoundException $e){
            return false;
        }
    }
}