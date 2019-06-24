<?php

require 'vendor/autoload.php';

session_start();

$authManager = new Sngular\Keycloak\Manager\Manager\AuthManager();

$provider = $authManager->openIdProtocol();

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();

    echo "<p><a href='{$authUrl}' >Login</a></p>";

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state, make sure HTTP sessions are enabled.');
} else {
    // Try to get an access token (using the authorization coe grant)
    try {
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
    } catch (Exception $e) {
        exit('Failed to get access token: ' . $e->getMessage());
    }

    // Optional: Now you have a token you can look up a users profile data
    try {
        $user = $provider->getResourceOwner($token);

    } catch (Exception $e) {
        exit('Failed to get resource owner: ' . $e->getMessage());
    }

    // Use this to interact with an API on the users behalf
    dump($token);
    dump($user);

    echo "<p><a href='/' >Back</a></p>";
}