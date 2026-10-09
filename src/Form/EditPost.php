<?php
namespace App\Form;

class EditPost extends AddPost
{
    /**
     * @param string $currentTitle the title in the database : it is allowed to stay the same
     * @param array  $file the entry $_FILES['picture']
     */
    public function validateEdit(string $currentTitle, array $file = []): self
    {
        $this->ensureStrings(['title', 'content']);

        $data = new Validator($this->dataForValidator());
        $data->check('required', ['title']);
        $data->check('lengthMin', 'content', 20);
        if(mb_strtolower($this->data['title']) !== mb_strtolower(html_entity_decode($currentTitle, ENT_QUOTES))){
            $data->check('used', 'title', 'posts');
        }
        $data->validateForm();
        $this->errors = $data->getErrors();

        return $this->checkPicture($file);
    }

    /**
     * @param int   $id id of the post (the one of the url)
     * @param array $file the entry $_FILES['picture'] : without a new file, the picture is kept
     */
    public function updatePost(int $id, array $file = []): void
    {
        $this->update([
            'title'   => $this->encoded()->getTitle(),
            'content' => $this->encoded()->getContent(),
            'public'  => $this->isPublic() ? 1 : 0,
            'edit'    => 1,
        ] + $this->savedPicture($file), 'posts', 'id', $id);
    }
}