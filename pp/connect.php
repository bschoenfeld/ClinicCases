<?php
session_start();
require('../lib/php/auth/session_check.php');
require('../db.php');
require_once('../vendor/autoload.php');

try
{
    $provider = new \League\OAuth2\Client\Provider\GenericProvider([
		'clientId'                => PP_CLIENT_ID,
		'clientSecret'            => PP_CLIENT_SECRET,
		'redirectUri'             => 'https://evictionhelpline.org/helplinecms/pp/connect.php',
		'urlAuthorize'            => 'https://app.practicepanther.com/OAuth/Authorize',
		'urlAccessToken'          => 'https://app.practicepanther.com/OAuth/Token',
		'urlResourceOwnerDetails' => 'https://app.practicepanther.com/'
    ]);

	if (!isset($_GET['code'])) {
		// Fetch the authorization URL from the provider; this returns the
		// urlAuthorize option and generates and applies any necessary parameters
		// (e.g. state).
		$authorizationUrl = $provider->getAuthorizationUrl();
	
		// Get the state generated for you and store it to the session.
		$_SESSION['oauth2state'] = $provider->getState();
	
		// Redirect the user to the authorization URL.
		header('Location: ' . $authorizationUrl);
		exit;
	
	// Check given state against previously stored one to mitigate CSRF attack
	} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
	
		if (isset($_SESSION['oauth2state'])) {
			unset($_SESSION['oauth2state']);
		}
		
		exit('Invalid state');
	
	} else {
        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
		]);

		$epoch = $accessToken->getExpires();
		$dt = new DateTime("@$epoch");

		$q = $dbh->prepare("INSERT INTO pp_tokens (id, accessToken, refreshToken, expires) VALUES (NULL, :accessToken, :refreshToken, :expires);");
		$data = array(
			'accessToken' => $accessToken->getToken(),
			'refreshToken' => $accessToken->getRefreshToken(),
			'expires' => $dt->format('Y-m-d H:i:s')
		);
        $q->execute($data);
        
        // Redirect the user to the authorization URL.
		header('Location: manage.php');
		exit;
	}
} catch(Exception $e) {
	echo 'Caught exception: ',  $e->getMessage();
}
