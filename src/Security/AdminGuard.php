<?php
namespace App\Security;

/**
 * Who is the signed-in administrator ?
 * The members have their own session keys ("id" and "name") : being a member gives no access
 * to the dashboard, and being an administrator does not make you a member.
 */
class AdminGuard
{
    public static function isAdmin(): bool
    {
        return !empty($_SESSION['admin_id']);
    }

    public static function id(): ?int
    {
        return self::isAdmin() ? (int)$_SESSION['admin_id'] : null;
    }

    public static function name(): string
    {
        return (isset($_SESSION['admin_name']) && is_string($_SESSION['admin_name'])) ? $_SESSION['admin_name'] : '';
    }

    public static function login(int $id, string $name): void
    {
        // A new session id : an id that somebody knew before the login is useless afterwards
        session_regenerate_id(true);
        unset($_SESSION['csrf_token']);
        $_SESSION['admin_id']   = $id;
        $_SESSION['admin_name'] = $name;
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['csrf_token']);
        session_regenerate_id(true);
    }
}