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

    public function bottomProfile(): string
    {
        return <<<HTML
            <section class="setting-profile">
                <div class="single-icon">
                    <div class="icon">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                    </div>
                    <div class="content-icon">
                        <h3>Password</h3>
                        <p>Change your password</p><a href="#">>> Click here</a>
                    </div>			
                </div>
                <div class="single-icon">
                    <div class="icon">
                        <i class="fas fa-file-alt" aria-hidden="true"></i>
                    </div>
                    <div class="content-icon">
                        <h3>Description</h3>
                        <p>Write or change your description</p><a href="#">>> Click here</a>
                    </div>			
                </div>
                <div class="single-icon">
                    <div class="icon">
                        <i class="fas fa-user-times" aria-hidden="true"></i>
                    </div>
                    <div class="content-icon">
                        <h3>Unsubscribe</h3>
                        <p>If you want delete your account</p><a href="#">>> Click here</a>
                    </div>			
                </div>
            </section>
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
    
    /**
     * Verify if is Admin and ADD the position
     *
     * @param  string $admin
     * @param  object $user
     * @return void|html
     */
    private function isAdmin(string $admin, object $user) 
    {
        if($admin === 'admin'){
            return <<<HTML
                <span>{$user->getPosition()}</span>
HTML;
        }
    } 
    
    /**
     *
     * @param  string $description
     * @param  object $user
     * @return html
     */
    private function getDescription(string $description, object $user)
    {
        if($description != NULL){
            return <<<HTML
                <p>{$description}</p>
HTML;
         }
        return '<p>Please, write a description !!!!</p>';
    }
}

