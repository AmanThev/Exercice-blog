<?php

session_start();
session_destroy();
header('location: ' . CreateUrl::url('home'));
exit();