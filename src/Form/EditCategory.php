<?php
namespace App\Form;

class EditCategory extends AddCategory
{
    /**
     * @param int $id id of the category (the one of the url)
     */
    public function updateCategory(int $id): void
    {
        $this->update($this->fields(), 'f_categories', 'id', $id);
    }
}