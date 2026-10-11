<?php
namespace App\Manager;

use \PDO;
use App\Model\Member;
use App\Model\Admin;
use App\SQL\CountSql;

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
        return $this->getDataByField($this->queryAdmins, 'name', $name, 'Admin');
    }

    public function getMemberByName(string $name): Member
    {
        return $this->getDataByField(
            $this->queryMembers, 'name', $name, 'Member');
    }

    public function getMemberById(int $id): Member
    {
        return $this->getDataByField($this->queryMembers, 'id', $id, 'Member');
    }

    public function getMemberByEmail(string $email): Member
    {
        return $this->getDataByField($this->queryMembers, 'email', $email, 'Member');
    }

    public function getMemberByToken(string $token): Member
    {
        return $this->getDataByField($this->queryMembers, 'token', $token, 'Member');
    }


    /**
     * The members with what they did, for the dashboard : a few queries for everybody
     * (instead of 3 queries per member). The counts are matched in PHP, so two tables
     * never have to be compared with each other in SQL (different collations would fail).
     *
     * @return array each member is [id, name, email, likes, dislikes, comments]
     *               ("comments" = on the posts + on the reviews)
     */
    public function getMembersWithStats(): array
    {
        $pdo     = $this->connect();
        $members = $pdo->query("SELECT id, name, email FROM members ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

        $likes    = [];
        $dislikes = [];
        $votes    = $pdo->query("SELECT user_id, vote, COUNT(*) AS total FROM votes GROUP BY user_id, vote")->fetchAll(PDO::FETCH_ASSOC);
        foreach($votes as $vote){
            if((int)$vote['vote'] === 1){
                $likes[$vote['user_id']] = (int)$vote['total'];
            }elseif((int)$vote['vote'] === -1){
                $dislikes[$vote['user_id']] = (int)$vote['total'];
            }
        }

        $comments = [];
        foreach(['comments_post', 'comments_film'] as $table){
            $rows = $pdo->query("SELECT pseudo, COUNT(*) AS total FROM $table GROUP BY pseudo")->fetchAll(PDO::FETCH_ASSOC);
            foreach($rows as $row){
                $comments[$row['pseudo']] = ($comments[$row['pseudo']] ?? 0) + (int)$row['total'];
            }
        }

        return array_map(fn($member) => $member + [
            'likes'    => $likes[$member['id']]       ?? 0,
            'dislikes' => $dislikes[$member['id']]    ?? 0,
            'comments' => $comments[$member['name']]  ?? 0,
        ], $members);
    }

    /**
     * The admins with the number of posts and reviews they wrote, for the dashboard.
     *
     * @return array each admin is [id, name, email, position, posts, reviews]
     */
    public function getAdminsWithStats(): array
    {
        $pdo     = $this->connect();
        $admins  = $pdo->query("SELECT id, name, email, position FROM admins ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $posts   = $pdo->query("SELECT admin_id, COUNT(*) FROM posts GROUP BY admin_id")->fetchAll(PDO::FETCH_KEY_PAIR);
        $reviews = $pdo->query("SELECT admin_id, COUNT(*) FROM films GROUP BY admin_id")->fetchAll(PDO::FETCH_KEY_PAIR);

        return array_map(fn($admin) => $admin + [
            'posts'   => (int)($posts[$admin['id']]   ?? 0),
            'reviews' => (int)($reviews[$admin['id']] ?? 0),
        ], $admins);
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
        if(!in_array($tableName, ['members', 'admins'], true)){
            throw new \Exception("Unknown user table: $tableName");
        }
        $stmt = $this->connect()->prepare("SELECT name FROM $tableName WHERE name=:pseudo");
        $stmt->execute(['pseudo' => $pseudo]);
        return $stmt->rowCount();
    }
}