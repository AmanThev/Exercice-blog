<?php
namespace App\Form;

use App\URL\CreateUrl;
use App\Manager\UserDatabase;
use App\Manager\Exception\NotFoundException;

class PasswordReset extends Authentication
{
    public function sendEmail()
    {
        try{
            $this->data['id'] = (new UserDatabase())->getMemberByEmail($this->data['email'])->getId();
        }catch(NotFoundException $e){
            return;
        }

        $token = $this->generateToken();
        $this->updateDB($token);
        $this->sendResetLink($this->data['email'], $token);
    }

    private function generateToken()
    {
        return $token = bin2hex(random_bytes(32));
        
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    private function updateDB(string $token){
        $this->update([
            'token'    => $this->hashToken($token),
            'reset_at' => date('Y-m-d H:i:s')
        ], 'members', 'email', $this->data['email']);
    }

    private function sendResetLink($email, $token) 
    {
        $subject = "Password Reset";
        $resetLink = CreateUrl::absoluteUrl('authentication/reset/' . $token);
        $message = '<html lang="en" style="font-family: sans-serif;">
                        <head>
                            <meta charset="UTF-8">
                        </head>
                        <body>
                            To reset your password click here : <a href="'.$resetLink.'">this page</a>:
                            <br/>Email: '.$email.'
                            <br/><br/>After inserting this information, you will need to choose a password.
                        </body>
                    </html>';
        $headers = "From: me@example.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        return mail($email, $subject, $message, $headers);
    }

    public static function tokenIsValid(string $token): bool
    {
        try{
            $member = (new UserDatabase())->getMemberByToken(hash('sha256', $token));
        }catch(NotFoundException $e){
            return false;
        }

        $now = new \DateTime();
        return ($now->getTimestamp() - $member->getDate()->getTimestamp()) <= 900;
    }


    public function resetPassword(string $token): self
    {
        $this->data['token'] = $token;

        $data = new Validator($this->data);
        $data->check('required', ['token', 'password', 'password2']);
        $data->check('token', 'token');
        $data->check('lengthMin', 'password', 6);
        $data->check('equals', 'password', 'password2');

        $this->resultValidator = $data->validateForm();
        $this->errors          = $data->getErrors();

        if($this->resultValidator){
            $this->update([
            'password' => password_hash($this->data['password'], PASSWORD_ARGON2ID),
            'token'    => null,
            'reset_at' => null
        ], 'members', 'token', $this->hashToken($token));
        }

        return $this;
    }

}
