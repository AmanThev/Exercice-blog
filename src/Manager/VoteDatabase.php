<?php
namespace App\Manager;

use \PDO;
use App\Model\Vote;
use App\SQL\CountSql;
use \Exception;

class VoteDatabase extends Database
{
    private $queryVote = "SELECT * FROM votes";

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
    
    // function refIdExist($refId){
    //     require('manager.php');
    //     $req = $db->prepare("SELECT * FROM posts WHERE id = ?");
    //     $req->execute(array($refId));
    //     $exist = $req->rowCount();
    //     return $exist;
    // }
    
    // function userHasVoted($ref, $refId, $userId){
    //     require('manager.php');
    //     $req = $db->prepare("SELECT ref, ref_id, user_id FROM votes WHERE ref = ? AND ref_id = ? AND user_id = ?");
    //       $req->execute(array($ref, $refId, $userId));
    //       $exist = $req->rowCount();
    //       return $exist;
    
    //       $req->closeCursor();
    // }
    
    // function changeVote($vote, $refId, $userId){
    //     require('manager.php');
    //     $req = $db->prepare("UPDATE votes SET vote = ? WHERE ref_id = ? AND user_id=?");
    //     $req->execute(array($vote, $refId, $userId));
        
    //     $req->closeCursor();
    // }
    
    // function insertVote($ref, $refId, $userId, $vote){
    //     require('manager.php');
    //     $req = $db->prepare("INSERT INTO votes SET ref=?, ref_id=?, user_id=?, vote=?");
    //     $req->execute(array($ref, $refId, $userId, $vote));
    
    //     $req->closeCursor();
    // }
    
    // function countVote($ref, $refId){
    //     require('manager.php');
    //     $req = $db->prepare("SELECT COUNT(id) as count, vote FROM votes WHERE ref = ? AND ref_id = ? GROUP BY vote");
    //     $req->execute(array($ref, $refId));
    //     $votes = $req->fetchAll();
    //     $counts = [
    //       '-1' => 0,
    //       '1' => 0
    //     ];
    //     foreach ($votes as $vote){
    //       $counts[$vote['vote']] = $vote['count'];
    //     }
    //     $req1 = $db->query("UPDATE $ref SET count_like = {$counts[1]}, count_dislike = {$counts[-1]} WHERE id = $refId");
    
    //     $req->closeCursor();    
    //     $req1->closeCursor();
    // }
}