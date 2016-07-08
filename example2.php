<?php

// Initialize the session
session_start();

// Load the SimpleSAMLphp classes
include_once('/home/ffraz/apps/simplesamlphp/lib/_autoload.php');

// Parameters
$sp              = 'default-sp';
$session_timeout = 1200; // 20 minutes
$session_refresh = 120; // 2 minutes

// Functions
function toTimestamp($data) {
	return gmdate("Y-m-d\TH:i:s\Z", $data);
}

// Constructor
$as = new SimpleSAML_Auth_Simple($sp);

// Check whether the user is authenticated with this authentication source
$valid_saml_session = $as->isAuthenticated();
if (!$valid_saml_session) {
	
	// Check the result of the logout operation
	//$state = SimpleSAML_Auth_State::loadState((string)$_REQUEST['LogoutState'], 'MyLogoutState');
	//$ls = $state['saml:sp:LogoutStatus'];
	//if ($ls['Code'] === 'urn:oasis:names:tc:SAML:2.0:status:Success' && !isset($ls['SubCode'])) {
	//	// Successful logout
	//	session_unset();
	//}
	
	// Make sure that the user is authenticated
	if (isset($_SESSION['was_logged_in']) or $_SESSION['was_logged_in'] === TRUE) {
		$as->requireAuth();
	}
		
	// Retrieve a URL that can be used to start authentication
	$url =  $as->getLoginURL();
	echo '<a style="text-decoration: none; color: purple;" href="' . htmlspecialchars($url) . '">Login</a>';
} else {
	
	// Store something in the session
	$_SESSION['was_logged_in'] = TRUE;
	
	// Retrieve a URL that can be used to trigger logout
	$url =  $as->getLogoutURL();
	echo '<a style="text-decoration: none; color: purple;" href="' . htmlspecialchars($url) . '">Log Out</a';
		
	// Retrieve the specified authentication data for the current session
	$miscs = array(
			'Expire'       => $as->getAuthData('Expire'),
			'REMOTE_ADDR'  => $_SERVER['REMOTE_ADDR'],
			'IdP'          => $as->getAuthData('saml:sp:IdP'),
			'AuthnInstant' => $as->getauthData('AuthnInstant'),
			'AuthnContext' => $as->getAuthData('saml:sp:AuthnContext'),
			'SessionIndex' => $as->getAuthData('saml:sp:SessionIndex'),
	);
	
	// Send a passive authentication request
	$e = $miscs['Expire'];
	$c = time();
	if (($e - $c) <= ($session_timeout - $session_refresh)) {
		$as->login(array(
				'ForceAuthn' => TRUE,
				'isPassive'  => TRUE,
		));
	}
		
	// Retrieve the attributes of the current user
	$attrs = $as->getAttributes();
	
	// Actor.FormattedName
	if (!isset($attrs['Actor.FormattedName'][0])) {
		throw new Exception('Actor.FormattedName attribute missing.');
	}
	$display_name = $attrs['Actor.FormattedName'][0];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<title>SimpleSAMLphp Example SAML SP</title>
<meta charset="utf-8">
</head>

<body style="Arial,Helvetica Neue,Helvetica,sans-serif; font-size: 18;">
<h1 style="font-family: Arial Black,Arial Bold,Gadget,sans-serif; color: blue;">SimpleSAMLphp Example SAML SP</h1>

<?php

if (!$valid_saml_session) {
	
	// Logged out
	echo '<h2>Please log in</h2>';  	
} else {
	
	// Logged in
	echo '<h2>Welcome, ' . $display_name . '</h2>';
	
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
	foreach ($attrs as $attr_name => $attr_values) {
		if (!empty(array_filter($attr_values))) {
			echo '<b>' . $attr_name . ':</b><ul>';
			foreach ($attr_values as $attr_value) {
				echo'<li>' . $attr_value . '</li>';
			}
			echo '</ul>';
		}
	}
}
?>

</body>
</html>