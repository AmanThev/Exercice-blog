<?php
namespace App\Form;

class EditSubCategory extends AddSubCategory
{
    /**
     * @param int $id id of the sub-category (the one of the url)
     */
    public function updateSubCategory(int $id): void
    {
        $this->update($this->fields(), 'f_sub_categories', 'id', $id);
    }
}