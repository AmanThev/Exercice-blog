<?php
namespace App\HTML;


use App\Manager\UserDatabase;

class Profile
{

    private $statut;


    public function __construct(string $statut)
    {
        $this->statut = $statut;
    }

    public function topProfile(string $name): string
    {
        $user = $this->sqlResult($this->statut, $name);
        $publicPath = PUBLIC_PATH;
        return <<<HTML
            <div class="top-profile $this->statut">
                <aside class="photo-profile">
                    <img src="{$publicPath}/img/photoProfile/default.jpg">
                </aside>

                <section class="intro-profile">
                    <h2>{$user->getName()}
                        {$this->isAdmin($this->statut, $user)}
                    </h2>
                    {$this->getDescription($user->getDescription(), $user)}
                </section>
            </div>
HTML;
    }
    
    /**
     * get data for Admin or the Member
     *
     * @param  string $tableName
     * @param  string $name
     * @return Admin|Member
     */
    private function sqlResult(string $tableName, string $name)
    {
        $sql = new UserDatabase();
        if($tableName === "admin"){
            return $sql->getAdminByName($name);
        }elseif($tableName === "members"){
            return $sql->getMemberByName($name);
        }else{
            throw new Exception("Invalid Users");
        }
    }

    private function isAdmin(string $admin, $user)
    {
        if($admin === 'admin'){
            return <<<HTML
                <span>{$user->getPosition()}</span>
HTML;
        }
    } 

    private function getDescription(string $description, $user)
    {
        if($description != NULL){
            return <<<HTML
                <p>{$description}</p>
HTML;
         }
        return '<p>Please, write a description !!!!</p>';
    }
}
