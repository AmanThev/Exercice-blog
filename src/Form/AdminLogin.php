<?php
namespace App\Form;

use App\Manager\UserDatabase;

class AdminLogin extends FormHandler
{
    /**
     * A valid hash of a password nobody knows. When the name does not exist, the password is
     * verified against it anyway : the answer takes the same time, so it does not tell whether
     * the name exists. (It can never give access : see $isReal below.)
     */
    private const FAKE_HASH = '$argon2id$v=19$m=65536,t=4,p=1$QXJYQnJZS3liVEUvampjdw$/TiloHOA6JmHd/vNaJbcpeR4SOsfpBHwjNTEyZ0v1EI';

    /**
     * @var array|null [id, name, password] of the administrator who just logged in
     */
    private ?array $admin = null;

    public function validateLogin(): self
    {
        $name     = is_string($this->data['name'] ?? null)     ? trim($this->data['name']) : '';
        $password = is_string($this->data['password'] ?? null) ? $this->data['password']  : '';

        $admin  = $name !== '' ? (new UserDatabase())->getAdminCredentials($name) : null;
        $hash   = (string)($admin['password'] ?? '');
        $isReal = $admin !== null && $hash !== '' && $password !== '';

        // One password_verify in every case
        $verified = password_verify($password, $isReal ? $hash : self::FAKE_HASH);

        $this->admin           = ($isReal && $verified) ? $admin : null;
        $this->resultValidator = $this->admin !== null;
        // The same message whether the name or the password is wrong
        $this->errors          = $this->resultValidator ? [] : ['login' => ['Invalid username or password.']];

        return $this;
    }

    public function admin(): ?array
    {
        return $this->admin;
    }
}