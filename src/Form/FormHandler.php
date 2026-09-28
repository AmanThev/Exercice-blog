<?php
namespace App\Form;

use \PDO;
use App\Manager\Connection;
use DateTime;


class FormHandler
{
    protected array $data;
    protected $errors;    
    /**
     * @var bool
     */
    protected bool $resultValidator;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function returnErrors()
    {
        return $this->errors;
    }

    public function resultValidator(): bool
    {
        return $this->resultValidator;
    }

    public function arrayKeyExist($key, array $valueError): void
    {
        if(array_key_exists($key, $valueError)){
            foreach($valueError[$key] as $error){
                echo '<p class="error"><i class="fas fa-exclamation-circle"></i> '.$error.'</p>';
            }
        }
    }

    public function getField(string $key, mixed $default = ''): mixed
    {
        return $this->data[$key] ?? $default;
    }

    private function buildSetClause(array $data): string
    {
        $fields = [];
        foreach($data as $key => $value){
            $fields[] = "$key = :$key";
        }
        return implode(', ', $fields);
    }

    public function create(array $data, $tabName): int
    {
        $sqlFields = $this->buildSetClause($data);

        $stmt = new Connection();
        $sql = $stmt->connect()->prepare("INSERT INTO $tabName SET " . $sqlFields);
        $addObject = $sql->execute($data);
        if($addObject === false){
            throw new \Exception("Error, impossible to add id to {$tabName}");
        }
        return $stmt->pdo->lastInsertId();
    }

    public function update(array $data, $tabName, string $whereColumn, $whereValue): bool
    {
        $sqlFields = $this->buildSetClause($data);

        $stmt = new Connection();
        $sql = $stmt->connect()->prepare("UPDATE $tabName SET " . $sqlFields . " WHERE $whereColumn = :whereValue");

        $data['whereValue'] = $whereValue;

        $updateObject = $sql->execute($data);
        if($updateObject === false){
            throw new \Exception("Error, impossible to update {$tabName}");
        }
        return true;
    }
}
