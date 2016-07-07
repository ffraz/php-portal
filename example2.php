<?php
// Load the SimpleSAMLphp classes
include_once('/home/ffraz/apps/simplesamlphp/lib/_autoload.php');

// Parameters
$title = 'My First Service Provider in PHP';

// Functions
function toTimestamp($data) {
	return gmdate("Y-m-d\TH:i:s\Z", $data);
}

function print_miscs($miscs) {
	echo '<p><u>Miscellaneous</u></p>';
	echo '<b>Session Expiration</b>: ' . toTimestamp($miscs['Expire']) . '</br>';
	echo '<b>Client Address</b>: ' . $miscs['REMOTE_ADDR'] . '</br>';
	echo '<b>Identity Provider</b>: ' . $miscs['IdP'] . '</br>';
	echo '<b>Authentication Time</b>: ' . toTimestamp($miscs['AuthnInstant']) . '</br>';
	echo '<b>Authentication Context Class</b>: ' . $miscs['AuthnContext'] . '</br>';
	echo '<b>SessionIndex</b>: ' . $miscs['SessionIndex'] . '</br>';
}

function print_attrs($attrs) {
	echo '<p><u>Attributes:</u></p>';
	foreach ($attrs as $attrName => $attrValues) {
		if (!empty(array_filter($attrValues))) {
			echo '<b>' . htmlentities($attrName) . '</b><ul>';
			foreach ($attrValues as $attrValue) {
				echo '<li>' . htmlentities($attrValue) . '</li>';
			}
			echo '</ul>';
		}
	}
}

// Constructor
$auth = new SimpleSAML_Auth_Simple('default-sp');

// Check whether the user is authenticated with this authentication source
$valid_saml_session = $auth->isAuthenticated();
if (!$valid_saml_session) {
	// Retrieve a URL that can be used to start authentication
	$url = $auth->getLoginURL();
	echo '<a href="' . htmlspecialchars($url) . '">Login</a>';
	
} else {
	// Retrieve a URL that can be used to trigger logout
	$url = $auth->getLogoutURL();
	echo '<a href="' . htmlspecialchars($url) . '">Logout</a>';
	
	// Retrieve the specified authentication data for the current session
	$miscs = array(
		'REMOTE_ADDR'          => $_SERVER['REMOTE_ADDR'],
		'SessionIndex'         => $auth->getAuthData('saml:sp:SessionIndex'),
		'AuthnContext'         => $auth->getAuthData('saml:sp:AuthnContext'),
		'IdP'                  => $auth->getAuthData('saml:sp:IdP'),
		'Expire'               => $auth->getAuthData('Expire'),
		'AuthnInstant'         => $auth->getauthData('AuthnInstant'),
	);
	
	// Retrieve the attributes of the current user
	$attrs = $auth->getAttributes();
	// Show displayName
	if (!isset($attrs['Actor.FormattedName'][0])) {
		throw new Exception('displayName attribute missing.');
	}
	$name = $attrs['Actor.FormattedName'][0];	
}

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?= $title ?></title>
  </head>
  <body>
    <h1><?= $title ?></h1>
    
    <?php
	if (!$valid_saml_session) {
    	echo '<h2>Please log in</h2>';
    } else { 
    	echo '<h2>Hello ' . $name . '</h2>';
		
    	print_miscs($miscs);
	    print_attrs($attrs);
    }
    ?>
  </body>
</html>