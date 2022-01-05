<?php

namespace App;

use App\SQL\Paginate;
use App\Manager\Connection;
use App\Model\Film;
use App\SQL\CountSql;
use \Exception;
use \PDO;

class DateReviews
{
    public static function listYears($year)
    {
        $yearsThousand = ($year % 10000 - $year % 100) / 100;
        $yearTens = ($year % 100);
        $result = [];
    
        switch ($yearTens){
            case 00: 
                $lastDecade = [0, 99, 98, 97, 96, 95, 94, 93, 92, 91];
                foreach ($lastDecade as $lastNumber){
                    if($lastNumber == 0){
                        $result[] = $yearsThousand.'0'.$lastDecade[0];
                    }else{
                        $result[] = ($yearsThousand - 1).$lastNumber;
                    }
                }
                return $result;
            break;
            case 10:
                for ($i = $yearTens; $i >= 1; $i--){
                    if($i == 10){
                        $result[] = $yearsThousand.$i;
                    }else{
                        $result[] = $yearsThousand.'0'.$i;
                    }
                }
                return $result;
            break;
            case 20:
                for ($i = $yearTens; $i >= 11; $i--){
                    $result[] = $yearsThousand.$i;
                }
                return $result;
            break;
            case 30:
                for ($i = $yearTens; $i >= 21; $i--){
                    $result[] = $yearsThousand.$i;
                }
                return $result;
            break;
            case 40:
                for ($i = $yearTens; $i >= 31; $i--){
                    $result[] = $yearsThousand.$i;
                }
                return $result;
            break;
            case 50:
                $result = [];
                for ($i = $yearTens; $i >= 41; $i--){
                    $result[] = $yearsThousand.$i;
                }
                return $result;
            break;
            case 60:
                for ($i = $yearTens; $i >= 51; $i--){
                    $result[] = $yearsThousand.$i;
                }
                return $result;
            break;
            case 70:
                for ($i = $yearTens; $i >= 61; $i--){
                    $result[] = $yearsThousand.$i;
                }
                return $result;
            break;
            case 80:
                for ($i = $yearTens; $i >= 71; $i--){
                    $result[] = $yearsThousand.$i;
                }
                return $result;
            break;
            case 90:
                for ($i = $yearTens; $i >= 81; $i--){
                    $result[] = $yearsThousand.$i;
                }
                return $result;
            break;
        }
    }

    public static function getListYears($decade)
    {
        return self::listYears($decade);
    }

    public static function listDecades()
    {
        return $decades = [
            '2020' => '2020-2011',
            '2010' => '2010-2001',
            '2000' => '2000-1991'  
        ];
    }
    
    /**
     * get the start and the end of the decade
     *
     * @param  int $params
     * @return array
     */
    private function endsDecade($params): array
    {
        $listYears = self::listYears($params);
        $startDecade = $listYears[9];
        $endDecade = $listYears[0];

        return [$startDecade, $endDecade];
    }

    public static function getFilmByDecade($decades)
    {
        [$startDecade, $endDecade] = self::endsDecade($decades);
        $pdo = new Connection();
        $sql = "SELECT * FROM films WHERE date BETWEEN {$startDecade} AND {$endDecade}";
        $pagination = new Paginate($sql, 6);
        $pagination = $pagination->getPagination();
        $sql .= " ORDER BY date DESC $pagination";

        $stmt = $pdo->connect()->query($sql);
        $films = $stmt->fetchAll(PDO::FETCH_CLASS, Film::class);
        return $films;
    }

    public static function getFilmByYear($year)
    {
        $pdo = new Connection();
        $sql = "SELECT * FROM films WHERE date = $year";
        $filmExist = CountSql::totalData($sql);
        if($filmExist){
            $pagination = new Paginate($sql, 6);
            $pagination = $pagination->getPagination();
            $sql .= " ORDER BY date DESC $pagination";

            $stmt = $pdo->connect()->query($sql);
            $films = $stmt->fetchAll(PDO::FETCH_CLASS, Film::class);
            return $films;
        }
       throw new Exception("No film at this year : $year"); 
    }

    public static function filmPaginationNumberDecade($decades)
    {
        [$startDecade, $endDecade] = self::endsDecade($decades);
        $sql = "SELECT * FROM films WHERE date BETWEEN {$startDecade} AND {$endDecade}";
        $pagination = new Paginate($sql);
        $pagination = $pagination->getPaginationNumberDecade($decades);
        return $pagination; 
    }

    public static function filmPaginationNumberYear($decades, $year)
    {
        $sql = "SELECT * FROM films WHERE date = $year";
        $pagination = new Paginate($sql);
        $pagination = $pagination->getPaginationNumberYear($decades, $year);
        return $pagination; 
    }
    
    /**
     * Check if film exist at this year
     *
     * @param  int $year
     * @return void
     */
    public static function filmsExists($year)
    {
        return CountSql::totalData("SELECT * FROM films WHERE date = $year");
    }

    
}