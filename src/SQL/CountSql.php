<?php 

namespace App\SQL;

use App\Manager\Connection;
use \PDO;

class CountSql {

    /**
     * Count row from a table, if param $sql = (ex: WHERE id = ?)
     *
     * @param  string $sql
     * @param  mixed $param
     * @return int
     */
    public static function totalData(string $sql, $param = null): int 
    {
        $pdo = new Connection();
        if($param != null ){
            $countSql = $pdo->connect()->prepare($sql);
            $countSql->execute([$param]);
        }
        if($param === null){
            $countSql = $pdo->connect()->query($sql);
        }
        $countSql = $countSql->fetchAll();
        $countSql = Count($countSql);
        return $countSql;
    }
    
    /**
     * Sum the first column
     *
     * @param  string $sql
     * @param  mixed $param
     * @return int
     */
    public static function totalColumn(string $sql, $param): int
    {
        $pdo = new Connection();
        $stmt = $pdo->connect()->prepare($sql);
        $stmt->execute([$param]);
        $total = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $total = array_sum($total);
        return $total;
    }
}