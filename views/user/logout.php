<?php

use App\URL\CreateUrl;

session_destroy();

$redirect = $_GET['redirect'] ?? null;

if($redirect && str_starts_with($redirect, WWW_ROOT)){
    header('Location: ' . $redirect);
}else{
    header('Location: ' . CreateUrl::url('home'));
}
exit();
