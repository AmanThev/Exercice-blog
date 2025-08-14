<?php

use App\URL\CreateUrl;

session_destroy();
header('location: ' . CreateUrl::url('home'));
exit();