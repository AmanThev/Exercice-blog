<?php
namespace App\Form;

use App\Manager\CommentDatabase;
use App\Manager\Database;
use App\Model\Comment;
use App\URL\CreateUrl;
use DateTime;

class AddComment extends AddData
{
    public function validateComment(?string $user = null): self
    {
        $data = new Validator($this->data);
        $data->check('required', ['pseudo', 'comment']);
        $data->check('lengthBetween', 'pseudo', 2, 20);
        if($user){
            $data->check('exist', 'pseudo', $user, 'name');
        }
        else{
            $data->check('used', 'pseudo', 'members', 'name');
            $data->check('used', 'pseudo', 'admins', 'name');
        }

        $this->resultValidatorn = $data->validateForm();
        $this->errors           = $data->getErrors();

        return $this;
    }

    public function createComment(int $idPost): void
    {
        $arrayComment = [
            'indexId'    => $idPost,
            'pseudo'    => $this->data['pseudo'],
            'comment'   => $this->data['comment'],
            'date'      => date('Y-m-d')
        ];

        $comment = new Comment();
        Database::hydrate($comment, $arrayComment);

        $id = $this->create([
            'index_id'      => $comment->getIndexId(),
            'pseudo'        => $comment->getPseudo(),
            'comment'       => $comment->getComment(),
            'comment_date'  => $comment->getDate()
        ]);

        $comment->setId($id);
    }
}