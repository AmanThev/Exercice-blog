<?php
namespace App\Form;

use \PDO;
use App\Manager\Connection;
use DateTime;


class AddData
{
    protected $data;
    protected $errors;    
    /**
     * @var bool
     */
    protected $resultValidator;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function returnErrors()
    {
        return $this->errors;
    }

    public function resultValidator()
    {
        return $this->resultValidator;
    }

    public function arrayKeyExist($key, array $valueError)
    {
        if(array_key_exists($key, $valueError)){
            foreach($valueError[$key] as $error){
                echo '<p class="error"><i class="fas fa-exclamation-circle"></i> '.$error.'</p>';
            }
        }
    }

    Public function create(array $data, $tabName): int
    {
        foreach($data as $key => $value){
            $keyFields[] = "$key = :$key";
        }

        $sqlFields = implode(', ', $keyFields);
        $stmt = new Connection();
        $sql = $stmt->connect()->prepare("INSERT INTO $tabName SET " . $sqlFields);
        $addObject = $sql->execute($data);
        if($addObject === false){
            throw new \Exception("Error, impossible to add id to {$tabName}");
        }
        return $stmt->pdo->lastInsertId();
    }
}