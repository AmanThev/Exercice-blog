<?php
namespace App\Form;

class Authentication extends FormHandler
{
    public function validateAuth(): self
    {
        $data = new Validator($this->data);
        $data->check('required', ['name', 'password']);
        $data->check('exist', 'name', 'members');

        $this->resultValidator  = $data->validateForm();
        $this->errors           = $data->getErrors();

        return $this;
    }

    public function validEmail(): self
    {
        $data = new Validator($this->data);
        $data->check('required', ['email']);
        $data->check('email', 'email');
        $data->check('exist', 'email', 'members');

        $this->resultValidator = $data->validateForm();
        $this->errors          = $data->getErrors();

        return $this;
    }

    public function checkPassword()
    {
        $data = new Validator($this->data);
        $data->check('password', 'password', $this->data['name'],'members');
        $this->resultValidator  = $data->validateForm();
        $this->errors           = $data->getErrors();

        return $this;
    }
}