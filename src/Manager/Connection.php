<?php
namespace App\Manager;

use \PDO;
use \PDOException;

class Connection
{   
    private $host;
    private $username;
    private $password;
    private $dbname;
    public $pdo;

    public function __construct(string $dbname = 'blogCinema', string $host = 'localhost', string $username = 'root', string $password = 'root')
    {
        $this->dbname   = $dbname;
        $this->host     = $host;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * getPDOConnexion
     *
     * @return connexion to Connection
     */
    public function connect()
    {
        try{
            $dsn = 'mysql:host='.$this->host.';dbname='.$this->dbname;
            $pdo = new PDO ($dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->pdo = $pdo;
        }catch (PDOException $e){
            die("helo");
            echo "Connection failed: ".$e->getMessage();
        }   
    }
}