<?php
namespace App\Form;

use App\Manager\Database;
use App\Model\Member;

class AddUser extends FormHandler
{
    public function validateUser(): self
    {
        $data = new Validator($this->data);
        $data->check('required', ['name', 'email', 'password', 'password2']);
        $data->check('lengthMin', 'name', 3);
        $data->check('used', 'name', 'members', 'name');
        $data->check('used', 'email', 'members', 'email');
        $data->check('email', 'email');
        $data->check('equals', 'password', 'password2');

        $this->resultValidator  = $data->validateForm();
        $this->errors           = $data->getErrors();

        return $this;
    }

    public function addMembers(): void
    {
        $arrayMember = [
            'name'      => $this->data['name'],
            'email'     => $this->data['email'],
            'password'  => $this->data['password']
        ];

        $member = new Member();
        Database::hydrate($member, $arrayMember);

        $id = $this->create([
            'name'      => $member->getName(),
            'email'     => $member->getEmail(),
            'password'  => $member->getPassword()
        ], 'members');

        $member->setId($id);
    }
}