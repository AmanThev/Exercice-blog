<?php
namespace App\Form;

class PasswordReset extends Authentication
{
    public function sendEmail()
    {
        $this->token();
        dd($this->data['email']);
    }

    private function token()
    {
        $chars = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
        
    }
}