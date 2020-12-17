<?php
namespace App\Model;

class Vote
{    
    /**
     * @var int
     */
    private $id;    
    /**
     * @var int
     */
    private $ref_id;    
    /**
     * @var string varchar(50)
     */
    private $ref;
    /**
     * @var int
     */
    private $user_id;
    /**
     * @var int
     */
    private $vote;

    public function getId()
    {
        return $this->id;
    }

    public function getRefId()
    {
        return $this->$ref_id;    
    }

    public function getRef()
    {
        return $this->ref;
    } 
    public function getUserId()
    {
        return $this->user_id;
    } 
    
    public function getVote()
    {
        return $this->vote;
    } 
}
