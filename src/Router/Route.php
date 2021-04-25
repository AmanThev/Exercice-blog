<?php

namespace App\Router;

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
            case 'user':
                ob_start();
                require $view . '.php';
                $content = ob_get_clean();
                require VIEWS . 'layouts/profile.php';
                return $this;
                break;
            case 'ajax':
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

        // if($newPath[0] === 'dashboard'){
        //     ob_start();
        //     require $view . '.php';
        //     $content = ob_get_clean();
        //     require VIEWS . 'layouts/dashboard.php';
        //     return $this;
        // }
        // if($newPath[0] === 'login'){
        //     ob_start();
        //     require $view . '.php';
        //     $content = ob_get_clean();
        //     require VIEWS . 'layouts/auth.php';
        //     return $this;
        // }
        // ob_start();
        // require $view . '.php';
        // $content = ob_get_clean();
        // require VIEWS . 'layouts/default.php';
        // return $this;
    }
}