<?php
namespace App\Manager;

use \PDO;
use App\Model\Member;
use App\Model\Admin;
use App\SQL\CountSql;

class UserDatabase extends Database
{   
    /**
     * The account that receives what a deleted member wrote (his topics and his messages in the forum).
     * It is recognized by its e-mail, never by its name : the registration form refuses this address
     * (it has no dot), so nobody can create an account with it.
     */
    public const DELETED_MEMBER_NAME  = 'Deleted member';
    public const DELETED_MEMBER_EMAIL = 'deleted-member@invalid';

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
        $members = $pdo->query("SELECT id, name, email FROM members WHERE email <> '" . self::DELETED_MEMBER_EMAIL . "' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

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

    /**
     * What the login of the administrators needs.
     *
     * @return array|null [id, name, password (the hash)], or null if there is no admin with this name
     */
    public function getAdminCredentials(string $name): ?array
    {
        $stmt = $this->connect()->prepare("SELECT id, name, password FROM admins WHERE name = :name");
        $stmt->execute(['name' => $name]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        return $admin === false ? null : $admin;
    }

    /**
     * Set the password of an administrator (see scripts/set-admin-password.php)
     *
     * @return bool false if there is no administrator with this name
     */
    public function setAdminPassword(string $name, string $password): bool
    {
        $stmt = $this->connect()->prepare("UPDATE admins SET password = :password WHERE name = :name");
        $stmt->execute(['password' => password_hash($password, PASSWORD_ARGON2ID), 'name' => $name]);
        return $stmt->rowCount() === 1;
    }

    /**
     * Delete the account of a member, and keep what he wrote :
     *  - his topics and his messages of the forum go to the account "Deleted member"
     *  - his comments (they only keep his pseudo) are signed "Deleted member"
     *  - his votes and his account are deleted
     * Everything is done in one transaction : if a step fails, nothing is changed.
     *
     * @return array the votes that were deleted [['ref' => ..., 'ref_id' => ...], ...] : the counters of
     *               those posts have to be recounted
     */
    public function deleteMember(int $id): array
    {
        $pdo = $this->connect();
        try{
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT name, email FROM members WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $member = $stmt->fetch(PDO::FETCH_ASSOC);
            if($member === false){
                throw new \Exception("There is no member with the id $id");
            }
            if($member['email'] === self::DELETED_MEMBER_EMAIL){
                throw new \Exception("The account \"Deleted member\" cannot be deleted");
            }

            $deletedId = $this->deletedMemberId($pdo);

            $stmt = $pdo->prepare("SELECT DISTINCT ref, ref_id FROM votes WHERE user_id = :id");
            $stmt->execute(['id' => $id]);
            $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach(['f_topics', 'f_messages'] as $table){
                $pdo->prepare("UPDATE $table SET id_members = :deleted WHERE id_members = :id")
                    ->execute(['deleted' => $deletedId, 'id' => $id]);
            }

            // The pseudo of a comment is the name of the member, escaped once or twice depending on the page
            // that wrote it : both forms are looked for.
            foreach(['comments_post', 'comments_film'] as $table){
                $pdo->prepare("UPDATE $table SET pseudo = :deleted WHERE pseudo IN (:raw, :escaped)")
                    ->execute([
                        'deleted' => self::DELETED_MEMBER_NAME,
                        'raw'     => $member['name'],
                        'escaped' => htmlspecialchars($member['name']),
                    ]);
            }

            $pdo->prepare("DELETE FROM votes WHERE user_id = :id")->execute(['id' => $id]);
            $pdo->prepare("DELETE FROM members WHERE id = :id")->execute(['id' => $id]);

            $pdo->commit();
            return $votes;
        }catch(\Throwable $e){
            if($pdo->inTransaction()){
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * The id of the account "Deleted member" : it is created the first time it is needed
     * (with the same three columns as a registration). Nobody can log in with it : "!" is not a valid hash.
     */
    private function deletedMemberId(PDO $pdo): int
    {
        $stmt = $pdo->prepare("SELECT id FROM members WHERE email = :email");
        $stmt->execute(['email' => self::DELETED_MEMBER_EMAIL]);
        $id = $stmt->fetchColumn();
        if($id !== false){
            return (int)$id;
        }

        $pdo->prepare("INSERT INTO members (name, email, password) VALUES (:name, :email, '!')")
            ->execute(['name' => self::DELETED_MEMBER_NAME, 'email' => self::DELETED_MEMBER_EMAIL]);

        return (int)$pdo->lastInsertId();
    }

    public function getAdminsPresentationPage($position): array
    {
        $stmt = $this->connect()->prepare("$this->queryAdmins WHERE position=:position");
        $stmt->execute(['position' => $position]);
        $admins = $stmt->fetchAll(PDO::FETCH_CLASS, Admin::class);
        return $admins;
    }

    /**
     * The real members (the account "Deleted member" is not one)
     */
    public function countMembers(): int
    {
        return CountSql::totalData("$this->queryMembers WHERE email <> '" . self::DELETED_MEMBER_EMAIL . "'");
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