<?php
namespace App\Form;

class AddCategory extends ForumForm
{
    /**
     * @param string|null $currentName when a category is edited : its name in the database (it may stay the same)
     */
    public function validateCategory(?string $currentName = null): self
    {
        $this->ensureStrings(['name', 'introduction']);

        $data = new Validator($this->data);
        $this->checkName($data, 'f_categories', $currentName);
        $data->check('required', ['introduction']);
        $data->check('lengthBetween', 'introduction', 10, 1000);
        $data->validateForm();
        $this->errors = $data->getErrors();

        $this->checkNameShape($currentName);
        $this->resultValidator = empty($this->errors);

        return $this;
    }

    /**
     * @return int id of the new category
     */
    public function createCategory(): int
    {
        return $this->create($this->fields(), 'f_categories');
    }

    /**
     * The columns of the table. The text is saved as it was typed : the getters of the Category model
     * escape it when it is displayed (an encoded text would be encoded twice).
     */
    protected function fields(): array
    {
        return [
            'name'         => $this->data['name'],
            'introduction' => $this->data['introduction'],
        ];
    }
}