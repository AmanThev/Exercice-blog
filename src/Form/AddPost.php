<?php
namespace App\Form;

use App\Helpers\File;
use App\Manager\UserDatabase;
use App\Model\Post;

class AddPost extends FormHandler
{
    public function validatePost(array $file = []): self
    {
        $this->ensureStrings(['author', 'title', 'content']);

        $data = new Validator($this->dataForValidator());
        $data->check('required', ['author', 'title']);
        $data->check('lengthBetween', 'author', 2, 20);
        $data->check('used', 'title', 'posts');
        $data->check('lengthMin', 'content', 20);
        if($this->data['author'] !== ''){
            $data->check('exist', 'author', 'admins', 'name');
        }
        $data->validateForm();
        $this->errors = $data->getErrors();

        return $this->checkPicture($file);
    }

    /**
     * Create the post (and save its picture, if there is one).
     *
     * @param  array $file the entry $_FILES['picture']
     * @return int id of the new post
     */
    public function createPost(array $file = []): int
    {
        $admin = (new UserDatabase())->getAdminByName($this->data['author']);

        return $this->createWithPicture([
            'title'    => $this->encoded()->getTitle(),
            'admin_id' => $admin->getId(),
            'content'  => $this->encoded()->getContent(),
            'public'   => $this->isPublic() ? 1 : 0,
            'date'     => date('Y-m-d'),
        ], $file);
    }

    /**
     * Check the picture : adds the errors to the ones of the form.
     */
    protected function checkPicture(array $file): self
    {
        $pictureErrors = File::pictureErrors($file);
        if(!empty($pictureErrors)){
            $this->errors['picture'] = $pictureErrors;
        }
        $this->resultValidator = empty($this->errors);

        return $this;
    }

    /**
     * The titles in the database are encoded by the Post model ("Café" is stored "Caf&eacute;"),
     * so the "used" rule must compare the encoded title, otherwise a duplicate is never found
     * as soon as the title has an accent, an & or an apostrophe.
     */
    protected function dataForValidator(): array
    {
        return ['title' => $this->encoded()->getTitle()] + $this->data;
    }

    /**
     * The Post model encodes the title and the content like everywhere else on the site.
     */
    protected function encoded(): Post
    {
        $post = new Post();
        $post->setTitle($this->data['title']);
        $post->setContent($this->data['content']);

        return $post;
    }

    /**
     * The creation form sends "true"/"false", the edit form a checkbox ("1" when checked).
     */
    protected function isPublic(): bool
    {
        $value = $this->data['public'] ?? '';

        return $value === 'true' || $value === '1' || $value === 1;
    }

    /**
     * ['picture' => 'name.jpg'] if a file was sent, otherwise nothing to add.
     */
    protected function savedPicture(array $file): array
    {
        if(empty($file['tmp_name'])){
            return [];
        }

        return ['picture' => File::moveUploadedPicture($file, 'postPicture')];
    }

    /**
     * One INSERT, picture included. If the INSERT fails, the saved file is removed.
     */
    private function createWithPicture(array $fields, array $file): int
    {
        $picture = $this->savedPicture($file);

        try{
            return $this->create($fields + $picture, 'posts');
        }catch(\Throwable $e){
            if($picture){
                @unlink(IMAGE . 'postPicture' . DIRECTORY_SEPARATOR . $picture['picture']);
            }
            throw $e;
        }
    }

    /**
     * Makes sure the fields exist and are strings (a hand-made POST with title[]=x
     * would crash the Validator).
     */
    protected function ensureStrings(array $keys): void
    {
        foreach($keys as $key){
            $this->data[$key] = (isset($this->data[$key]) && is_string($this->data[$key])) ? trim($this->data[$key]) : '';
        }
    }
}