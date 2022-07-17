<?php
namespace App\Form;

use App\Manager\CommentDatabase;
use App\Manager\Database;
use App\Model\Comment;
use App\URL\CreateUrl;
use DateTime;

class AddComment extends AddData
{    
    /**
     * @param  string|NULL $user === admins || members
     * @return self
     */
    public function validateComment(?string $user = null): self
    {
        $data = new Validator($this->data);
        $data->check('required', ['pseudo', 'comment']);
        $data->check('lengthBetween', 'pseudo', 2, 20);
        if(empty($_POST['pseudo']) === false){
            if($user){
                $data->check('exist', 'pseudo', $user, 'name');
            }else{
                $data->check('used', 'pseudo', 'members', 'name');
                $data->check('used', 'pseudo', 'admins', 'name');
            }
        }
        
        $this->resultValidator  =   $data->validateForm();
        $this->errors           =   $data->getErrors();
        return $this;
    }
    
    public function validateRating(): self
    {
        $data = new Validator($this->data);
        $data->check('numberBetween', 'rating-film', 0, 5);
        
        $previousResult = $this->resultValidator;
        $newResult =   $data->validateForm();
        if($previousResult === false || $newResult === false){
            $this->resultValidator = false;
        }
        $this->errors += $data->getErrors();
        return $this;
    }

    public function createCommentPost(int $idPost): void
    {
        $arrayComment = [
            'indexId'   => $idPost,
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
            'comment_date'  => $comment->getDate()->format('Y-m-d')
        ], 'comments_post');

        $comment->setId($id);
    }

    public function createCommentFilm(int $idFilm): void
    {
        $arrayComment = [
            'indexId'   => $idFilm,
            'pseudo'    => $this->data['pseudo'],
            'comment'   => $this->data['comment'],
            'date'      => date('Y-m-d'),
            'ratingFilm'    =>$this->data['rating-film']
        ];

        $comment = new Comment();
        Database::hydrate($comment, $arrayComment);

        $id = $this->create([
            'index_id'      => $comment->getIndexId(),
            'pseudo'        => $comment->getPseudo(),
            'comment'       => $comment->getComment(),
            'comment_date'  => $comment->getDate()->format('Y-m-d'),
            'rating_film'   => $comment->getRatingFilm()
        ], 'comments_film');

        $comment->setId($id);
    }
}