<?php
namespace App\Form;

class EditFilm extends AddFilm
{
    /**
     * @param string $currentTitle the title in the database : it is allowed to stay the same
     * @param array  $file the entry $_FILES['poster']
     */
    public function validateEdit(string $currentTitle, array $file = []): self
    {
        $this->ensureStrings(array_merge(['score'], self::TEXT_FIELDS));

        $data = new Validator($this->dataForValidator());
        if(mb_strtolower($this->data['title']) !== mb_strtolower(html_entity_decode($currentTitle, ENT_QUOTES))){
            $data->check('used', 'title', 'films');
        }
        $this->checkFilmFields($data);
        $data->validateForm();
        $this->errors = $data->getErrors();

        return $this->checkPicture($file);
    }

    /**
     * @param int   $id id of the film (the one of the url)
     * @param array $file the entry $_FILES['poster'] : without a new file, the poster is kept
     */
    public function updateFilm(int $id, array $file = []): void
    {
        $this->update($this->filmFields() + $this->savedPicture($file), static::TABLE, 'id', $id);
    }
}