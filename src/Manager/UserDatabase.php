<?php
namespace App\Manager;

use \PDO;
use App\Model\Member;
use App\Model\Admin;
use App\SQL\CountSql;
use App\Manager\Exception\NotFoundException;


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

    public function getAdminById(int $id): Admin
    {
        return $this->getDataByField($this->queryAdmins, 'id', $id, "Admin");
    }

    public function getAdminByName(string $name): Admin
    {
        $stmt = $this->connect()->prepare("$this->queryAdmins WHERE name=:name");
        $stmt->execute(['name' => $name]);
        if($stmt->rowCount() == 1){
            $stmt->setFetchMode(PDO::FETCH_CLASS,Admin::class);
            $admin = $stmt->fetch();
            return $admin;
        }
        throw new NotFoundException('Admin', $name);
    }

    public function getMemberByName(string $name): Member
    {
        return $this->getDataByField(
            $this->queryMembers, 'name', $name, 'Member');
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

    public function statutUser(string $pseudo, string $tableName): int
    {
        $stmt = $this->connect()->prepare("SELECT name FROM $tableName WHERE name=:pseudo");
        $stmt->execute(['pseudo' => $pseudo]);
        return $stmt->rowCount();
    }

}