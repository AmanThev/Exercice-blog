<?php
namespace App\Form;

use App\Manager\UserDatabase;
use App\Manager\VoteDatabase;

class DeleteAccount extends FormHandler
{
    /**
     * The member must type his password again (a session left open is not enough to delete an account)
     * and tick the box.
     *
     * @param string $passwordHash the hash of the password of the member (in the database)
     */
    public function validateDeletion(string $passwordHash): self
    {
        $password = is_string($this->data['delete_password'] ?? null) ? $this->data['delete_password'] : '';

        $this->errors = [];
        if(!password_verify($password, $passwordHash)){
            $this->errors['delete_password'][] = 'Your password is incorrect.';
        }
        if(($this->data['delete_confirm'] ?? '') !== '1'){
            $this->errors['delete_confirm'][] = 'Tick the box to confirm that you want to delete your account.';
        }
        $this->resultValidator = empty($this->errors);

        return $this;
    }

    /**
     * Delete the account (see UserDatabase::deleteMember), then recount the votes of the posts
     * that had a vote of this member.
     */
    public function deleteAccount(int $idMember): void
    {
        $removedVotes = (new UserDatabase())->deleteMember($idMember);

        $voteDatabase = new VoteDatabase();
        foreach($removedVotes as $vote){
            try{
                $voteDatabase->countAndUpdateVotes($vote['ref'], (int)$vote['ref_id']);
            }catch(\Throwable $e){
                // The account is deleted. A counter that could not be recounted is corrected at the next vote.
            }
        }
    }
}