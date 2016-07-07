<?php
// Load the SimpleSAMLphp classes
include_once('/home/ffraz/apps/simplesamlphp/lib/_autoload.php');

// Parameters
$title = 'SimpleSAMLphp Example SAML SP';

// Functions
function toTimestamp($data) {
	return gmdate("Y-m-d\TH:i:s\Z", $data);
}

// Constructor
$auth = new SimpleSAML_Auth_Simple('default-sp');

// Check whether the user is authenticated with this authentication source
$valid_saml_session = $auth->isAuthenticated();
if (!$valid_saml_session) {
	// Retrieve a URL that can be used to start authentication
	$url =  $auth->getLoginURL();
	echo '<a style="text-decoration: none;" href="' . htmlspecialchars($url) . '">Login</a>';
	
} else {
	// Retrieve a URL that can be used to trigger logout
	$url =  $auth->getLogoutURL();
	echo '<a style="text-decoration: none;" href="' . htmlspecialchars($url) . '">Log out</a';
	
	// Retrieve the specified authentication data for the current session
	$miscs = array(
			'Expire'       => $auth->getAuthData('Expire'),
			'REMOTE_ADDR'  => $_SERVER['REMOTE_ADDR'],
			'IdP'          => $auth->getAuthData('saml:sp:IdP'),
			'AuthnInstant' => $auth->getauthData('AuthnInstant'),
			'AuthnContext' => $auth->getAuthData('saml:sp:AuthnContext'),
			'SessionIndex' => $auth->getAuthData('saml:sp:SessionIndex'),
	);
	
	// Retrieve the attributes of the current user
	$attrs = $auth->getAttributes();
	
	// Display name
	if (!isset($attrs['Actor.FormattedName'][0])) {
		throw new Exception('displayName attribute missing.');
	}
	$name = $attrs['Actor.FormattedName'][0];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<title><?= $title ?></title>
<meta charset="utf-8">
</head>

<body style="Arial,Helvetica Neue,Helvetica,sans-serif; font-size: 18;">
<h1 style="font-family: Arial Black,Arial Bold,Gadget,sans-serif; color: blue;"><?= $title ?></h1>

<?php
if (!$valid_saml_session) {
	
	// Logged out
	echo '<h2>Please log in</h2>';  	
} else {
	
	// Logged in
	echo '<h2>Welcome, ' . $name . '</h2>';
	
	// Miscellaneous
	echo '<h3 style="font-family: Consolas,monaco,monospace; text-decoration: underline; color: red;">Miscellaneous:</h3>';
	echo '<b>Session Expiration</b>: ' . toTimestamp($miscs['Expire']) . '</br>';
	echo '<b>Client Address</b>: ' . $miscs['REMOTE_ADDR'] . '</br>';
	echo '<b>Identity Provider</b>: ' . $miscs['IdP'] . '</br>';
	echo '<b>Authentication Time</b>: '	. toTimestamp($miscs['AuthnInstant']) . '</br>';
	echo '<b>Authentication Context Class</b>: ' . $miscs['AuthnContext'] . '</br>';
	echo '<b>SessionIndex</b>: ' . $miscs['SessionIndex'] . '</br>';
	
	// Attributes
	echo '<h3 style="font-family: Consolas,monaco,monospace; text-decoration: underline; color: red;">Attributes:</h3>';
	foreach ($attrs as $attrName => $attrValues) {
		if (!empty(array_filter($attrValues))) {
			echo '<b>' . $attrName . ':</b><ul>';
			foreach ($attrValues as $attrValue) {
				echo'<li>' . $attrValue . '</li>';
			}
			echo '</ul>';
		}
	}
}
?>

</body>
</html>