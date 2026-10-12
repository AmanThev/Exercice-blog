<?php
namespace App\Form;

class EditProfile extends FormHandler
{
    public function validateDescription(): self
    {
        $this->ensureStrings(['description']);

        $data = new Validator($this->data);
        $data->check('lengthMax', 'description', 500);

        $this->resultValidator = $data->validateForm();
        $this->errors          = $data->getErrors();

        return $this;
    }

    public function updateDescription(int $idMember): void
    {
        $this->update([
            'description' => trim($this->data['description'])
        ], 'members', 'id', $idMember);
    }

    /**
     * @param string $currentHash the hash of the current password (in the database)
     */
    public function validatePassword(string $currentHash): self
    {
        $this->ensureStrings(['current_password', 'password', 'password2']);

        $data = new Validator($this->data);
        $data->check('required', ['current_password', 'password', 'password2']);
        $data->check('lengthMin', 'password', 8);
        $data->check('equals', 'password2', 'password');

        $this->resultValidator = $data->validateForm();
        $this->errors          = $data->getErrors();

        // Check of the old password, using the hash of the logged-in member
        // (and not by his name, which is not reliable to find the account)
        if(empty($this->errors['current_password']) && !password_verify($this->data['current_password'], $currentHash)){
            $this->errors['current_password'][] = 'Your current password is incorrect';
            $this->resultValidator = false;
        }

        return $this;
    }

    public function updatePassword(int $idMember): void
    {
        $this->update([
            'password' => password_hash($this->data['password'], PASSWORD_ARGON2ID)
        ], 'members', 'id', $idMember);
    }

    /**
     * Makes sure the fields exist and are strings
     * (a forged POST with password[]=x would crash the Validator).
     */
    private function ensureStrings(array $keys): void
    {
        foreach($keys as $key){
            if(!isset($this->data[$key]) || !is_string($this->data[$key])){
                $this->data[$key] = '';
            }
        }
    }
}