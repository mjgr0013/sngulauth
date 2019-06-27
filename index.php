<?php

session_start();

require 'vendor/autoload.php';

require_once 'src/Provider/Keycloak/Resources/config/filename.php';

use Sngular\Auth\Provider\Keycloak\Protocol\Connect;
use Sngular\Auth\Auth\Handler\SessionHandler;

$auth = new Connect($config);
$sessionHandler = new SessionHandler();

$authUrl = $auth->getAuthorizationUrl();
echo "<p><a href='{$authUrl}' >Login</a></p>";


if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $_SESSION['oauth2state'] = $auth->getState();

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state, make sure HTTP sessions are enabled.');
} else {
    // Try to get an access token (using the authorization coe grant)
    try {

        $token = $auth->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);


    } catch (Exception $e) {
        exit('Failed to get access token: ' . $e->getMessage());
    }

    // Optional: Now you have a token you can look up a users profile data
    try {
        $user = $auth->getResourceOwner($token);

    } catch (Exception $e) {
        exit('Failed to get resource owner: ' . $e->getMessage());
    }

    $userData = $auth->decryptResponse($token->getToken());

    SessionHandler::persistSessionData($token, $userData);

    header('Location: http://localhost:8080/auth_resource.php');
    exit;
}