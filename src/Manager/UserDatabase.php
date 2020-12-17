<?php
namespace App\Manager;

use App\Model\Member;
use App\Model\Admin;
use App\SQL\CountSql;
use \PDO;
use \Exception;


class UserDatabase extends Database
{   

    private $queryMembers = "SELECT * FROM members";
    private $queryAdmins = "SELECT * FROM admins";

    public function getMembers(): array
    {
        $stmt = $this->connect()->query("$this->queryMembers ORDER BY name");
        $members = $stmt->fetchAll(PDO::FETCH_CLASS, Member::class);
        return $members;
    }

    public function getAdmins(): array
    {
        $stmt = $this->connect()->query($this->queryAdmins);
        $admins = $stmt->fetchAll(PDO::FETCH_CLASS, Admin::class);
        return $admins;
    }

    public function getAdminById($id)
    {
        $stmt = $this->connect()->prepare("SELECT name FROM admins WHERE id=:id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS,Admin::class);
        $admin = $stmt->fetch();
        return $admin;
    }

    public function getAdminsPresentationPage($position): array
    {
        $stmt = $this->connect()->prepare("$this->queryAdmins WHERE position=:position");
        $stmt->execute(['position' => $position]);
        $admins = $stmt->fetchAll(PDO::FETCH_CLASS, Admin::class);
        return $admins;
    }

    public function countMembers(): int
    {
        return CountSql::totalData($this->queryMembers);
    }

    public function countAdmins(): int
    {
        return CountSql::totalData($this->queryAdmins);
    }
}