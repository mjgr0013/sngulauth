<?php

require '../vendor/autoload.php';

session_start();

$auth = new \Sngular\Auth\Auth\Handler\SessionHandler();

if (!$auth->isAuthenticated()) {
    header('Location: http://localhost:8080/');
    exit;
}

echo nl2br ('- Current user: ' . json_encode($auth->getUser()) . "\n");
echo nl2br('- Secure page' . "\n");
echo nl2br('Token is valid until: ' . $auth->tokenValidUntil());


echo "<p><a href='/' >Back</a></p>";
echo "<p><a href='logout.php' > Logout</a></p>";
echo "<p><a href='manage-account.php' > Manage Account Section</a></p>";