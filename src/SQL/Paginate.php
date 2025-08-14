<?php

namespace App\SQL;

use \PDO;
use \Exception;
use App\Connection;
use App\Url\NumericUrl;
use App\SQL\CountSql;
use App\Url\ExplodeUrl;

class Paginate {

    private $query;
    private $pdo;
    private $limit;

    public function __construct(string $query, int $limit = 6)
    {
        $this->query = $query;
        $this->limit = $limit;
    }

    public function getPagination(): string
    {
        $currentPage    = $this->getCurrentPage();
        $totalData      = $this->getCountData();
        $totalPages     = $this->getPages($totalData);
        if($currentPage > $totalPages){
            throw new Exception("This page doesn't exist");
        }
        $offset = $this->limit * ($currentPage - 1);
        return "LIMIT {$this->limit} OFFSET $offset";
    }

    private function getCurrentPage(): int
    {
        return numericUrl::getPositiveInt('page', 1);
    }

    private function getPerPage(): int
    {
        return numericUrl::getPositiveInt('perPage', $this->limit);
    }

    private function getCountData(): int
    {
        return CountSql::totalData($this->query);
    }

    private function getPages(int $totalData): int
    {
        return ceil($totalData / $this->limit);
    }

    public function getPaginationNumber()
    {
        $currentPage    = $this->getCurrentPage();
        $totalData      = $this->getCountData();
        $totalPages     = $this->getPages($totalData);
        
        if($currentPage == 1){
            echo "<li class='disabled'><a href='' aria-label='Previous'>&laquo</a></li>";
        }else{
            $link = "?page=" . ($currentPage - 1);
            echo "<li><a href='$link' aria-label='Previous'>&laquo</a></li>";
        }
        for($i=1; $i<=$totalPages; $i++){
            if($i == $currentPage){
                echo "<li class='active'><a href='?page=$i'>$i</a></li>";
            }else{
                echo "<li><a href='?page=$i'>$i</a></li>";
            }
        }
        if($currentPage == $totalPages){
            echo "<li class='disabled'><a href='' aria-label='Previous'>&raquo;</a></li>";
        }else{
            $link = "?page=" . ($currentPage + 1);
            echo "<li><a href='$link' aria-label='Next'>&raquo;</a></li>";
        }
    }

    public function getPaginationNumberDecade(int $decades)
    {
        $currentPage    = $this->getCurrentPage();
        $totalData      = $this->getCountData();
        $totalPages     = $this->getPages($totalData);
        $linkDecade     = "&decades=$decades";

        if($currentPage == 1){
            echo "<li class='disabled'><a href='' aria-label='Previous'>&laquo</a></li>";
        }else{
            $link = "?page=" . ($currentPage - 1);
            $link .= $linkDecade;
            echo "<li><a href='$link' aria-label='Previous'>&laquo</a></li>";
        }
        for($i=1; $i<=$totalPages; $i++){
            if($i == $currentPage){
                echo "<li class='active'><a href='?page=$i$linkDecade'>$i</a></li>";
            }else{
                echo "<li><a href='?page=$i$linkDecade'>$i</a></li>";
            }
        }
        if($currentPage == $totalPages){
            echo "<li class='disabled'><a href='' aria-label='Previous'>&raquo;</a></li>";
        }else{
            $link = "?page=" . ($currentPage + 1);
            $link .= $linkDecade;
            echo "<li><a href='$link' aria-label='Next'>&raquo;</a></li>";
        }
    }

    public function getPaginationNumberYear(int $decades, int $year)
    {
        $currentPage    = $this->getCurrentPage();
        $totalData      = $this->getCountData();
        $totalPages     = $this->getPages($totalData);
        $linkYear       = "&decades=$decades&year=$year";

        if($currentPage == 1){
            echo "<li class='disabled'><a href='' aria-label='Previous'>&laquo</a></li>";
        }else{
            $link = "?page=" . ($currentPage - 1);
            $link .= $linkYear;
            echo "<li><a href='$link' aria-label='Previous'>&laquo</a></li>";
        }
        for($i=1; $i<=$totalPages; $i++){
            if($i == $currentPage){
                echo "<li class='active'><a href='?page=$i$linkYear'>$i</a></li>";
            }else{
                echo "<li><a href='?page=$i$linkYear'>$i</a></li>";
            }
        }
        if($currentPage == $totalPages){
            echo "<li class='disabled'><a href='' aria-label='Previous'>&raquo;</a></li>";
        }else{
            $link = "?page=" . ($currentPage + 1);
            $link .= $linkYear;
            echo "<li><a href='$link' aria-label='Next'>&raquo;</a></li>";
        }
    }
}