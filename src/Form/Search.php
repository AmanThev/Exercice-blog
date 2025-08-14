<?php
namespace App\Form;

use App\Manager\Database;
use App\Manager\PostDatabase;
use App\Manager\FilmDatabase;

class Search extends FormHandler
{
    public function validateSearch()
    {
        $data = new Validator($this->data);
        $data->check('lengthMin', "search", 3);

        $this->resultValidator = $data->validateForm();
        $this->errors = $data->getErrors();
        return $this;
    }

    public function findResult()
    {
        switch($this->data['submit']){
            case 'film':
                return (new FilmDatabase())->findFilm($this->data['search']);
                break;
            case 'post':
                return (new PostDatabase())->findPost($this->data['search']);
                break;
            case 'all':
                return [
                    'films' => (new FilmDatabase())->findFilm($this->data['search']),
                    'posts' => (new PostDatabase())->findPost($this->data['search'])
                ];
            default:
                return "No found";
        }
    }
}