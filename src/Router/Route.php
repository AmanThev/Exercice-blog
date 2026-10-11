<?php

namespace App\Router;

use App\Security\AdminGuard;
use App\URL\CreateUrl;

class Route{
        
    /**
     * @var string
     */
    private $path;    
    /**
     * @var mixed
     */
    private $target;    
    /**
     * @var array Array of all matches 
     */
    private $matches = [];
    /**
     * @var array Array of all params
     */
    private $params = [];


    public function __construct($path, $target)
    {
        $this->path = trim($path, '/');
        $this->target = $target;
    }

    /**
     * Compare url and path
     *
     * @param  mixed $url
     * @return bool
     */
    public function match($url)
    { 
        $url = trim($url, '/'); 
        $path = preg_replace('#:([\w]+)#', '([^/]+)', $this->path);                    
        $regex = "#^$path$#i";
        if(!preg_match($regex, $url, $matches)){
            return false;
        }
            array_shift($matches);
            $this->matches = $matches;
            return true;
    }
    
    /**
     * call the target
     *
     * @return void
     */
    public function call()
    {  
        $view = $this->target;
        $newPath = explode('/', $this->path);

        // The dashboard and its ajax calls are for the administrators only.
        // (The check is made on the first part of the route itself, not on what is typed in the url.)
        if(in_array($newPath[0], ['dashboard', 'ajax'], true) && !AdminGuard::isAdmin()){
            $this->denyAccess($newPath[0]);
        }

        switch ($newPath[0]) {
            case 'dashboard':
                ob_start();
                require $view . '.php';
                $content = ob_get_clean();
                require VIEWS . 'layouts/dashboard.php';
                return $this;
                break;
            case 'authentication':
                ob_start();
                require $view . '.php';
                $content = ob_get_clean();
                require VIEWS . 'layouts/auth.php';
                return $this;
                break;
            // case 'user':
            //     ob_start();
            //     require $view . '.php';
            //     $content = ob_get_clean();
            //     require VIEWS . 'layouts/profile.php';
            //     return $this;
            //     break;
            case 'ajax':
                require $view . '.php';
                return $this;
                break;
            case 'actions':
                require $view . '.php';
                return $this;
                break;
            default:
                ob_start();
                require $view . '.php';
                $content = ob_get_clean();
                require VIEWS . 'layouts/default.php';
                return $this;
        }
    }

    /**
     * Nobody is signed in as an administrator : the page is not shown.
     *
     * @param string $area "dashboard" (pages) or "ajax" (calls made by the forms of the dashboard)
     */
    private function denyAccess(string $area): void
    {
        if($area === 'ajax'){
            // Same answer as the other errors of the ajax forms : the form shows the message
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'error' => ['form' => ['Your administrator session has expired, please log in again.']]]);
            exit;
        }

        // After the login, we come back to the page that was asked (only for a simple page : a POST cannot be replayed)
        $params = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' ? ['redirect' => $_SERVER['REQUEST_URI']] : [];
        header('Location: ' . CreateUrl::url('authentication/admin', $params));
        exit;
    }
}