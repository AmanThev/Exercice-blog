<?php
namespace App\Manager;

use \PDO;
use App\Model\Vote;
use App\SQL\CountSql;
use \Exception;

class VoteDatabase extends Database
{
    private $queryVote = "SELECT * FROM votes";

    /**
     * @var array Tables autorisées à recevoir un vote (whitelist de
     * sécurité : $ref vient de l'URL, on ne veut jamais l'interpoler
     * directement dans une requête SQL sans le vérifier avant).
     */
    private $allowedRefs = ['posts', 'reviews'];

    public function voteUser(string $ref,int $refId,int $userId)
    {
        $stmt = $this->connect()->prepare("$this->queryVote WHERE ref=:ref AND ref_id=:refId AND user_id=:userId");
        $stmt->execute(['ref' => $ref, 'refId' => $refId, 'userId' =>$userId]);
        $stmt->setFetchMode(PDO::FETCH_CLASS,Vote::class);
        $vote = $stmt->fetch();
        return $vote;
    }

    public function userLike(int $idName)
    {
        return CountSql::totalData("$this->queryVote WHERE user_id= ? AND vote = 1", $idName);
    }

    public function userDislike(int $idName)
    {
        return CountSql::totalData("$this->queryVote WHERE user_id= ? AND vote = -1", $idName);
    }

    public function insertVote(string $ref, int $refId, int $userId, int $vote): void
    {
        $stmt = $this->connect()->prepare(
            "INSERT INTO votes SET ref = :ref, ref_id = :refId, user_id = :userId, vote = :vote"
        );
        $inserted = $stmt->execute([
            'ref'    => $ref,
            'refId'  => $refId,
            'userId' => $userId,
            'vote'   => $vote
        ]);
        if($inserted === false){
            throw new Exception("Error, impossible to add the vote");
        }
    }

    public function updateVote(string $ref, int $refId, int $userId, int $vote): void
    {
        $stmt = $this->connect()->prepare(
            "UPDATE votes SET vote = :vote WHERE ref = :ref AND ref_id = :refId AND user_id = :userId"
        );
        $stmt->execute([
            'vote'   => $vote,
            'ref'    => $ref,
            'refId'  => $refId,
            'userId' => $userId
        ]);
    }

    public function deleteVote(string $ref, int $refId, int $userId): void
    {
        $stmt = $this->connect()->prepare(
            "DELETE FROM votes WHERE ref = :ref AND ref_id = :refId AND user_id = :userId"
        );
        $stmt->execute([
            'ref'    => $ref,
            'refId'  => $refId,
            'userId' => $userId
        ]);
    }

    /**
     * Recompte les votes d'un post/review et met à jour ses colonnes
     * count_like / count_dislike en conséquence.
     */
    public function countAndUpdateVotes(string $ref, int $refId): void
    {
        if(!in_array($ref, $this->allowedRefs, true)){
            throw new Exception("Invalid ref table: $ref");
        }

        $stmt = $this->connect()->prepare(
            "SELECT vote, COUNT(id) as total FROM votes WHERE ref = :ref AND ref_id = :refId GROUP BY vote"
        );
        $stmt->execute(['ref' => $ref, 'refId' => $refId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $counts = ['1' => 0, '-1' => 0];
        foreach($rows as $row){
            $counts[(string)$row['vote']] = (int)$row['total'];
        }

        $stmt = $this->connect()->prepare(
            "UPDATE $ref SET count_like = :like, count_dislike = :dislike WHERE id = :refId"
        );
        $stmt->execute([
            'like'    => $counts['1'],
            'dislike' => $counts['-1'],
            'refId'   => $refId
        ]);
    }
}