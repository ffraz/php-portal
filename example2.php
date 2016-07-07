<?php
// Load the SimpleSAMLphp classes
include_once('/home/ffraz/apps/simplesamlphp/lib/_autoload.php');

$title = 'My First Service Provider in PHP';

// Constructor
$auth = new SimpleSAML_Auth_Simple('default-sp');

// Check whether the user is authenticated with this authentication source
$valid_saml_session = $auth->isAuthenticated();
if (isset($valid_saml_session)) {
	// Retrieve a URL that can be used to trigger logout
	$url = $auth->getLogoutURL();
	print('<a href="' . htmlspecialchars($url) . '">Logout</a>');
	
	// Retrieve the attributes of the current user
	$attrs = $auth->getAttributes();
	// Show displayName
	if (!isset($attrs['Actor.FormattedName'][0])) {
		throw new Exception('displayName attribute missing.');
	}
	$name = $attrs['Actor.FormattedName'][0];
	
	#TODO: ziskanie dat zo session
	
} else {
	// Retrieve a URL that can be used to start authentication
	$url = $auth->getLoginURL();
	print('<a href="' . htmlspecialchars($url) . '">Login</a>');
}

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?= $title ?></title>
  </head>
  <body>
    <h1><?= $title ?></h1>
  
  </body>
</html>