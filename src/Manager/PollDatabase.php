<?php
namespace App\Manager;

use \PDO;

/**
 * The poll of the home page. One question, three answers, one answer for each member.
 */
class PollDatabase extends Database
{
    public const QUESTION = 'Do you like 3D ?';

    /** The accepted answers : value sent by the form => text shown to the visitor */
    public const ANSWERS = [
        'yes'       => 'Yes',
        'no'        => 'No',
        'doNotKnow' => "Don't know",
    ];

    /**
     * Number of votes for each answer, e.g. ['yes' => 12, 'no' => 3, 'doNotKnow' => 1].
     */
    public function getResults(): array
    {
        $results = array_fill_keys(array_keys(self::ANSWERS), 0);

        $rows = $this->connect()
            ->query("SELECT answer, COUNT(id) AS total FROM poll_votes GROUP BY answer")
            ->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row){
            if(isset($results[$row['answer']])){
                $results[$row['answer']] = (int)$row['total'];
            }
        }
        return $results;
    }

    /**
     * The answer of a member, or null if he has not voted yet.
     */
    public function getAnswer(int $userId): ?string
    {
        $stmt = $this->connect()->prepare("SELECT answer FROM poll_votes WHERE user_id = :userId");
        $stmt->execute(['userId' => $userId]);
        $answer = $stmt->fetchColumn();

        return $answer === false ? null : $answer;
    }

    /**
     * Save the vote of a member. Returns false if the answer is not one of the three
     * or if the member has already voted (the table accepts only one row for each member).
     */
    public function addVote(int $userId, string $answer): bool
    {
        if(!array_key_exists($answer, self::ANSWERS) || $this->getAnswer($userId) !== null){
            return false;
        }

        try{
            $stmt = $this->connect()->prepare("INSERT INTO poll_votes (user_id, answer) VALUES (:userId, :answer)");
            return $stmt->execute(['userId' => $userId, 'answer' => $answer]);
        }catch(\PDOException $e){
            // Two clicks at the same time : the second one is refused by the unique key.
            return false;
        }
    }
}
