<?php

use App\URL\CreateUrl;
use App\Security\Csrf;
use App\Security\AdminGuard;

// This route only exists in POST, and needs the token : a link on another site cannot log an administrator out
if(Csrf::validate($_POST['csrf_token'] ?? null)){
    AdminGuard::logout();
    header('Location: ' . CreateUrl::url('home'));
}else{
    header('Location: ' . CreateUrl::url('dashboard'));
}
exit;