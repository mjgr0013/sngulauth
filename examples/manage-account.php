<?php

require '../vendor/autoload.php';

session_start();

$auth = new \Sngular\Auth\Auth\Handler\SessionHandler();

if (!$auth->hasRole('manage-account')) {
    header('Location: http://localhost:8080/');
    exit;
}
echo nl2br ('- Current user: ' . json_encode($auth->getUser()) . "\n");
echo nl2br('- Secure page');
echo nl2br('Token is valid until: ' . $auth->tokenValidUntil() . "\n");

echo "<p>User has role manage-account</p>";
echo "<p><a href='/' >Back</a></p>";
echo "<p><a href='/logout.phpogout.php' > Logout</a></p>";