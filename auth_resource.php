<?php

require 'vendor/autoload.php';

session_start();

$auth = new \Sngular\Auth\Auth\Handler\SessionHandler();

if (!$auth->isAuthenticated()) {
    header('Location: http://localhost:8080/');
    exit;
}

echo nl2br ('- Current user: ' . json_encode($auth->getUser()) . "\n");
echo nl2br('- Secure page');
echo nl2br('Toke is valid until: ' . $auth->tokenValidUntil());


echo "<p><a href='/' >Back</a></p>";
echo "<p><a href='/logout.php' > Logout</a></p>";