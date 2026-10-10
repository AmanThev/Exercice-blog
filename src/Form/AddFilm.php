<?php
namespace App\Form;

use App\Manager\UserDatabase;
use App\Model\Film;

class AddFilm extends FormHandler
{
    use UploadsPicture;

    protected const TABLE  = 'films';
    protected const FOLDER = 'posterFilm';
    protected const COLUMN = 'poster';

    /**
     * The text fields of the form (the author, the score and the poster are handled apart)
     */
    protected const TEXT_FIELDS = ['title', 'date', 'director', 'writer', 'cast', 'production', 'genre', 'synopsis', 'review'];

    public function validateFilm(array $file = []): self
    {
        $this->ensureStrings(array_merge(['author', 'score'], self::TEXT_FIELDS));

        $data = new Validator($this->dataForValidator());
        $data->check('required', ['author']);
        $data->check('lengthBetween', 'author', 2, 20);
        $data->check('used', 'title', 'films');
        if($this->data['author'] !== ''){
            $data->check('exist', 'author', 'admins', 'name');
        }
        $this->checkFilmFields($data);
        $data->validateForm();
        $this->errors = $data->getErrors();

        return $this->checkPicture($file);
    }

    /**
     * Create the film (and save its poster, if there is one).
     *
     * @param  array $file the entry $_FILES['poster']
     * @return int id of the new film
     */
    public function createFilm(array $file = []): int
    {
        $admin = (new UserDatabase())->getAdminByName($this->data['author']);

        return $this->createWithPicture(['admin_id' => $admin->getId()] + $this->filmFields(), $file);
    }

    /**
     * The rules shared by the creation and the edition.
     */
    protected function checkFilmFields(Validator $data): void
    {
        // the score can be 0, so it is not "required" (0 is empty for the Validator)
        $data->check('required', self::TEXT_FIELDS);
        $data->check('lengthMax', ['title', 'director', 'writer', 'cast', 'production', 'genre'], 250);
        $data->check('lengthMax', 'synopsis', 250);
        $data->check('numberBetween', 'score', 0, 5);
        $data->check('year', 'date');
    }

    /**
     * The columns of the film, as the Film model saves them (encoded).
     */
    protected function filmFields(): array
    {
        $film = $this->encoded();

        return [
            'title'      => $film->getTitle(),
            'date'       => $film->getDate(),
            'director'   => $film->getDirector(),
            'writer'     => $film->getWriter(),
            'cast'       => $film->getCast(),
            'production' => $film->getProduction(),
            'genre'      => $film->getGenre(),
            'synopsis'   => $film->getSynopsis(),
            'review'     => $film->getRawReview(),   // the [Spoiler] markers are kept, they are turned into tags when displayed
            'score'      => $film->getScore(),
        ];
    }

    /**
     * The titles in the database are encoded by the Film model ("Amélie" is stored "Am&eacute;lie"),
     * so the "used" rule must compare the encoded title, otherwise a duplicate is never found
     * as soon as the title has an accent, an & or an apostrophe.
     */
    protected function dataForValidator(): array
    {
        return ['title' => $this->encoded()->getTitle()] + $this->data;
    }

    /**
     * The Film model encodes the fields like everywhere else on the site.
     */
    protected function encoded(): Film
    {
        $film = new Film();
        $film->setTitle($this->data['title']);
        $film->setDate((int)$this->data['date']);
        $film->setDirector($this->data['director']);
        $film->setWriter($this->data['writer']);
        $film->setCast($this->data['cast']);
        $film->setProduction($this->data['production']);
        $film->setGenre($this->data['genre']);
        $film->setSynopsis($this->data['synopsis']);
        $film->setReview($this->data['review']);
        $film->setScore((int)$this->data['score']);

        return $film;
    }
}