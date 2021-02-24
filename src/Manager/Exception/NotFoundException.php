<?php
namespace App\Manager\Exception;

class NotFoundException extends \Exception
{
    public function __construct(string $tableName, $value){

        if(is_int($value) === TRUE){
            $this->message = "No $tableName matches this id : $value";
        }
        if(is_string($value) === TRUE){
            $this->message = "No $tableName matches this name : $value";
        }
    }
} 

