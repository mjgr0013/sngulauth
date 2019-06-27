<?php
/**
 *  SAML Handler
 */

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Utils;

session_start();

require_once 'vendor/autoload.php';


$spBaseUrl = 'http://localhost:8080/index.php';

$realmCert = 'MIICmzCCAYMCBgFrao+m6jANBgkqhkiG9w0BAQsFADARMQ8wDQYDVQQDDAZtYXN0ZXIwHhcNMTkwNjE4MTIyNzA3WhcNMjkwNjE4MTIyODQ3WjARMQ8wDQYDVQQDDAZtYXN0ZXIwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQCpJGZydXrflVlbdAL2IIs6nsz2H/y7IQ+vXgfO3stb6azFsFNPJFX3GTcfChZNE2r5LeASMiJdr6hcW1ioBw9R28fuVuWbue16U4qFFh8Zz2jd+8spBmVa1hD1PQK1MVIE3PT2Zu3ZLlfIdSLB6PzjVM3gtD4Q/UdUuyxcrBBiATX+7X4e4UKf3ytHgUACa16yi66D/+KHrFDQC9YDYcu4ABiL0b4dXazmcxt80knXjcBhR7URv2aF0zK5J1qsEKxM9th79lg9hW0bqtLl/ICuOAwJYVfzSVzrem1tYicYL66Uup7kzRUZjLavDmV2F5Il6OP5VtKPXLvzlYSbTY7nAgMBAAEwDQYJKoZIhvcNAQELBQADggEBAKYxbZ5/dIVcW0zun2Ed+Kb49QxZNeQr9nl4xOmP7SkKcgSSKoC7Bj7QvS2wcNsJ2T2lPFxm7z3xGC22acIT2DFGjVO7OiNL35v5AZleKizKgNuA+/imJIzHSPtOCozoupWAY16ceBHcVrtxCV9zFjfH8J6/0fUw5jG03jt+MJQaqvJlD2GoKRus0EkTCgRzU+S2b2b3RVSyGtorCSB3bw04IquLwWr9zymoW9OGXbEaB9lPd2Q1EehP3qLo2dEAvAUW62WfoyLNArHehNbwNfNMHW1xah85+pAeI1OnmaRljEXi8CSx5gle5j5jhs5usVZnbTF8WNGyxH9+UX75VB4=';
$realmPubKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqSRmcnV635VZW3QC9iCLOp7M9h/8uyEPr14Hzt7LW+msxbBTTyRV9xk3HwoWTRNq+S3gEjIiXa+oXFtYqAcPUdvH7lblm7ntelOKhRYfGc9o3fvLKQZlWtYQ9T0CtTFSBNz09mbt2S5XyHUiwej841TN4LQ+EP1HVLssXKwQYgE1/u1+HuFCn98rR4FAAmtesouug//ih6xQ0AvWA2HLuAAYi9G+HV2s5nMbfNJJ143AYUe1Eb9mhdMyuSdarBCsTPbYe/ZYPYVtG6rS5fyArjgMCWFX80lc63ptbWInGC+ulLqe5M0VGYy2rw5ldheSJejj+VbSj1y785WEm02O5wIDAQAB';

$clientPrivKey = 'MIIEowIBAAKCAQEAiy3Hy9zF5GSnskvZyuWrIE3Yg7Xr81+GatD6p8HUJDAuzaUXzYI4pF0YQFXL0PUsbOhygqRxb1cEM6i3ATdFxi4ye825oHDxAdaXniUFUQfz1RoZTT3G2tYg++e/HVBzwrig49h4JrP9zKI64fyIenaKxTSGplPf9aLojxSNfShaEVS00xaQlPjtcVVxhPLojm37bizEsje6cWRclFFV9ZeK/WOtRjL/1Ol0N90WVGCbg11gUykKRJZ6LqfmOJUVRdMhMRZzrA4uh0170q1MZTfGw3X171ye1g1RzvfCUWlcs2A+waAlYNu9mu7otC1RysgscSsM+km5K/HfL4rNEwIDAQABAoIBAFTvpF2iJtaiIsEpjwlGre9x8m82x37nzgVD8aQNIuTOztFLLkKJdP3BnnosocyswubX2IInzt4u0W6hSMWiMJ+oM8DgJKdoJXyEtFSbrSntW32yhrxftgp44Po6TKScI3ky6WUDBxg/geSvIJYnYjayPy/oRUAeMbAbMieXWF2HKtfJuF2hTPteKHVVPTKQrEkPKZ8CpGKRB7Hhp2OikCydkX2GXcPWlJnQJ3S7j3VDvRDK4UGtAAhgiGd5E8jccGiwwqZh6Y/owcIhmMS7A+AoYPrVjzO369MXMnkPjcIkC4Dq04SYoqjer8RGFv4TTY3uDsKR62rbR9d7Fg0jeAECgYEAyJJT3sF/VDmCooiZ7Snk/GeDr8V0avBJD5uo6h2NYJtPkMhPi91VeM8iV8ojgOnDU5BvW0luWNmVQJDDLYn84/SPSX82n+lTD8HhaVtE1gwavzWFEbyahczIeIE/qBFcnpZ4nMg63pPRrW0NxE0ZFdsLbmLYBXu1Tyt0UaeCVAECgYEAsaQmYDvwyraTml2Rd33HkNpLdjQ+OisTWUA4WB3Lbeq/tcaaC9MnohHgecdSv0dgcHgRFt+cQLDqx2NXnTdxMn6OVThRzNlUgteNqGPpoGZ2vgOpOlG7Gso6Mb3mPz+vqhqwIo6ivEPjHDnMdaJXl5TmTGLzjHJoSFDndqdKkRMCgYEAnSsk86oW9831Ex4N2G161VrcMzF8T4wpBEUK8SHlNi2eBKsiwkvXfUp68+YfOhZ0DBhjWlpJUPB3Z1fIyfeWJp7uZT59dIOmpfNcim2NnFlx4CG1O7faMjzNXriRREcOl+r7aofwsZeNt+N75TYxERJLe92Sv4E4C2jWxmGH3AECgYB7H7s5mFIdLs6/f3sDxzo7PBJxq2Q2DQUsJ0bmTEBjvwGBjFXZPFGME7fysCS6T06YpZ+yquoyLG2OJVzkqzjaNr2Qc6i+wyPATIby381eT6adrQvuUBfPSbtsHqMn04x96mGKkjJZKvSO68C7B/qOJbY525vTDLka5niH1Qvp4QKBgA5uGwnrDXA2VGwg4gzmwT/GtYGx+QfNGy3vVeU9U/rGPqMUOyMVNket3EaVdt4eHcEw+2K4B1C+IZffeX8FnBuu2p+REsJYpwvm/k/qsxBTqQV75lEEpD6IJJBY5QgI3WTjacVuOmbpKky3UQD5fJwK26nuHZy5zYmFqRPp0S7X';
$clientCert = 'MIICqTCCAZECBgFrjzvVQTANBgkqhkiG9w0BAQsFADAYMRYwFAYDVQQDDA10ZXN0LWNsaWVudC0zMB4XDTE5MDYyNTE1MjEzMVoXDTI5MDYyNTE1MjMxMVowGDEWMBQGA1UEAwwNdGVzdC1jbGllbnQtMzCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAIstx8vcxeRkp7JL2crlqyBN2IO16/NfhmrQ+qfB1CQwLs2lF82COKRdGEBVy9D1LGzocoKkcW9XBDOotwE3RcYuMnvNuaBw8QHWl54lBVEH89UaGU09xtrWIPvnvx1Qc8K4oOPYeCaz/cyiOuH8iHp2isU0hqZT3/Wi6I8UjX0oWhFUtNMWkJT47XFVcYTy6I5t+24sxLI3unFkXJRRVfWXiv1jrUYy/9TpdDfdFlRgm4NdYFMpCkSWei6n5jiVFUXTITEWc6wOLodNe9KtTGU3xsN19e9cntYNUc73wlFpXLNgPsGgJWDbvZru6LQtUcrILHErDPpJuSvx3y+KzRMCAwEAATANBgkqhkiG9w0BAQsFAAOCAQEAYpXBDRzH4HP7n+veMNbpX438daegnS/i5YwdGh3HG4THO6ITJ1oRXL3rUU3YRqBwPsnhvQdxUNKWNlam9VM5s14OQFff6VCP9t3pYmobOWRGmgB84vxtnb4sOianTtHlxJozDfLmMhEwtMul6WrGnQcJVa2xfiikk0tc60TC98kXDZo/KNR4GMisVhU9Pbm2xRdZ0abrVvTn4zBT72bGZn0sJDXLaFfDaZ9f/vrUSmKd0LNZvQIHdhdYvV4eO37kVYSFASS/P57Y+0EKvOj59tF5ReybnKCX9uPm67t2/ROOBoOzOeb7WC8Sy+FZHo6fPLDGDvIVy2I72TyGU7Q44g==';

$settingsInfo = array (
    'debug' => true,
    'strict' => true,
    'sp' => array (
        'entityId' => 'test-client-3',
        'assertionConsumerService' => array (
            'url' => $spBaseUrl.'?acs',
        ),
        'singleLogoutService' => array (
            'url' => $spBaseUrl.'?sls',
        ),
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
        'privateKey' => $clientPrivKey,
        'x509cert' => $realmCert
    ),
    'idp' => array (
        'entityId' => 'http://localhost:8181/auth/realms/master',
        'singleSignOnService' => array (
            'url' => 'http://localhost:8181/auth/realms/master/protocol/saml',
        ),
        'singleLogoutService' => array (
            'url' => 'http://localhost:8181/auth/realms/master/protocol/saml',
        ),
        'x509cert' => $realmCert,
    ),
    'security' => array (
        'authnRequestsSigned'   => true,
        'logoutRequestSigned'   => true,
        'logoutResponseSigned'  => true,
        'wantAssertionsSigned' => true
   /*     'logoutResponseSigned' => true,*/
/*        'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',*/
    /*    'wantAssertionsSigned' => true,
        'wantMessagesSigned' => true,
        'wantNameId' => true,*/
    ),
);

$auth = new Auth($settingsInfo);

if (isset($_GET['sso'])) {
    //$auth->login();

    # If AuthNRequest ID need to be saved in order to later validate it, do instead
    $ssoBuiltUrl = $auth->login(null, array(), false, false, true);
    $_SESSION['AuthNRequestID'] = $auth->getLastRequestID();
    header('Pragma: no-cache');
    header('Cache-Control: no-cache, must-revalidate');
    header('Location: ' . $ssoBuiltUrl);
    # exit();

} else if (isset($_GET['sso2'])) {
    $returnTo = $spBaseUrl.'/demo1/attrs.php';
    $auth->login($returnTo);
} else if (isset($_GET['slo'])) {
    $returnTo = null;
    $parameters = array();
    $nameId = null;
    $sessionIndex = null;
    $nameIdFormat = null;

    if (isset($_SESSION['samlNameId'])) {
        $nameId = $_SESSION['samlNameId'];
    }
    if (isset($_SESSION['samlNameIdFormat'])) {
        $nameIdFormat = $_SESSION['samlNameIdFormat'];
    }
    if (isset($_SESSION['samlNameIdNameQualifier'])) {
        $nameIdNameQualifier = $_SESSION['samlNameIdNameQualifier'];
    }
    if (isset($_SESSION['samlNameIdSPNameQualifier'])) {
        $nameIdSPNameQualifier = $_SESSION['samlNameIdSPNameQualifier'];
    }
    if (isset($_SESSION['samlSessionIndex'])) {
        $sessionIndex = $_SESSION['samlSessionIndex'];
    }

    //$auth->logout($returnTo, $parameters, $nameId, $sessionIndex, false, $nameIdFormat, $nameIdNameQualifier, $nameIdSPNameQualifier);

    # If LogoutRequest ID need to be saved in order to later validate it, do instead
    $sloBuiltUrl = $auth->logout(null, $parameters, $nameId, $sessionIndex, true);
    $_SESSION['LogoutRequestID'] = $auth->getLastRequestID();
    header('Pragma: no-cache');
    header('Cache-Control: no-cache, must-revalidate');
    header('Location: ' . $sloBuiltUrl);
    # exit();

} else if (isset($_GET['acs'])) {
    if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
        $requestID = $_SESSION['AuthNRequestID'];
    } else {
        $requestID = null;
    }

    $auth->processResponse($requestID);

    $errors = $auth->getErrors();

    if (!empty($errors)) {
        echo '<p>',implode(', ', $errors),'</p>';
    }

    if (!$auth->isAuthenticated()) {
        echo "<p>Not authenticated</p>";
        exit();
    }

    $_SESSION['samlUserdata'] = $auth->getAttributes();
    $_SESSION['samlNameId'] = $auth->getNameId();
    $_SESSION['samlNameIdFormat'] = $auth->getNameIdFormat();
    $_SESSION['samlNameIdNameQualifier'] = $auth->getNameIdNameQualifier();
    $_SESSION['samlNameIdSPNameQualifier'] = $auth->getNameIdSPNameQualifier();
    $_SESSION['samlSessionIndex'] = $auth->getSessionIndex();
    unset($_SESSION['AuthNRequestID']);
    if (isset($_POST['RelayState']) && Utils::getSelfURL() != $_POST['RelayState']) {
        $auth->redirectTo($_POST['RelayState']);
    }
} else if (isset($_GET['sls'])) {
    if (isset($_SESSION) && isset($_SESSION['LogoutRequestID'])) {
        $requestID = $_SESSION['LogoutRequestID'];
    } else {
        $requestID = null;
    }

    $auth->processSLO(false, $requestID, true);
    $errors = $auth->getErrors();
    if (empty($errors)) {
        echo '<p>Sucessfully logged out</p>';
    } else {
        echo '<p>', implode(', ', $errors), '</p>';
    }
}

if (isset($_SESSION['samlUserdata'])) {
    if (!empty($_SESSION['samlUserdata'])) {
        $attributes = $_SESSION['samlUserdata'];
        echo 'You have the following attributes:<br>';
        echo '<table><thead><th>Name</th><th>Values</th></thead><tbody>';
        foreach ($attributes as $attributeName => $attributeValues) {
            echo '<tr><td>' . htmlentities($attributeName) . '</td><td><ul>';
            foreach ($attributeValues as $attributeValue) {
                echo '<li>' . htmlentities($attributeValue) . '</li>';
            }
            echo '</ul></td></tr>';
        }
        echo '</tbody></table>';
    } else {
        echo "<p>You don't have any attribute</p>";
    }

    echo '<p><a href="?slo" >Logout</a></p>';
} else {
    echo '<p><a href="?sso2" >Login and access to attrs.php page</a></p>';
}

echo '<p><a href="?sso" >Login</a></p>';