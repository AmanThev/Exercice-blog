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
     * @param string $currentHash le hash du mot de passe actuel (en base)
     */
    public function validatePassword(string $currentHash): self
    {
        $this->ensureStrings(['current_password', 'password', 'password2']);

        $data = new Validator($this->data);
        $data->check('required', ['current_password', 'password', 'password2']);
        $data->check('lengthMin', 'password', 6);
        $data->check('equals', 'password2', 'password');

        $this->resultValidator = $data->validateForm();
        $this->errors          = $data->getErrors();

        // Vérification de l'ancien mot de passe, par le hash du membre connecté
        // (et non par son nom, qui n'est pas fiable pour retrouver le compte)
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
     * Garantit que les champs existent et sont bien des chaînes
     * (un POST truqué avec password[]=x ferait planter le Validator).
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