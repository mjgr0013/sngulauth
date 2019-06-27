<?php


require 'vendor/autoload.php';

session_start();

$sessionHandler = new \Sngular\Auth\Auth\Handler\SessionHandler();

$sessionHandler->logout();

header('Location: http://localhost:8080/');
exit;