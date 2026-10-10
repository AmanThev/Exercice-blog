<?php
namespace App\Form;

use App\Manager\UserDatabase;
use App\Model\Post;

class AddPost extends FormHandler
{
    use UploadsPicture;

    protected const TABLE  = 'posts';
    protected const FOLDER = 'postPicture';
    protected const COLUMN = 'picture';

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
}