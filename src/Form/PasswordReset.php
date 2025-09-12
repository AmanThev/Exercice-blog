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


// function sendResetLink($email, $token) {
//     $subject = "Password Reset";
//     $resetLink = "https://votresite.com/reset-password.php?token=" . urlencode($token);
//     $message = "Cliquez sur ce lien pour réinitialiser votre mot de passe : $resetLink";
//     $headers = "From: support@votresite.com\r\n";

//     return mail($email, $subject, $message, $headers);
// }