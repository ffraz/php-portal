<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>My First Service Provider in PHP</title>
</head>
<body>

<?php
$lib = "/home/ffraz/apps/simplesamlphp/lib";
$sp = "default-sp";  // Name of SP defined in config/authsources.php

include_once("{$lib}/_autoload.php");
$as = new SimpleSAML_Auth_Simple($sp);

if (!$as->isAuthenticated()) {
	// Show login link
	$url = $as->getLoginURL();
	print('<a href="' . htmlspecialchars($url) . '">Login</a>');
	
	// Do not show displayName
	$name = FALSE;
	
	// Do not show Miscellaneous
	$show_misc = FALSE;
	
	// Do not show Attributes
	$show_attrs = FALSE;
}
else {
	// Send a passive authentication request.
	//if() {
	//	$as->login(array(
	//		'forceAuth' => TRUE,
	//		'isPassive' => TRUE,
	//	));
	//}
	
	// Show logout link
	$url = $as->getLogoutURL();
	print('<a href="' . htmlspecialchars($url) . '">Logout</a>');
	
	// Show displayName
	$attrs = $as->getAttributes();
	if (!isset($attrs['Actor.FormattedName'][0])) {
		throw new Exception('displayName attribute missing.');
	}
	$name = $attrs['Actor.FormattedName'][0];
	
	// Show Miscellaneous
	$show_misc = TRUE;
	
	// Show Attributes
	$show_attrs = TRUE;
}

?>

<h1>My First SP</h1>
<h2>

<?php 
if (!$name) {
	print('Please log in');
} else {
	print('Hello ' . htmlspecialchars($name));
}
?>

</h2>

<?php 
if ($show_misc) {
	print('<p>');
	print('<u>Miscellaneous</u></br>');
	print('<b>Session Expiration: </b>'
		. htmlspecialchars(gmdate("Y-m-d\TH:i:s\Z", $as->getAuthData('Expire'))) . '</br>');
	print('<b>Client Address: </b>'
		. htmlspecialchars($_SERVER['REMOTE_ADDR']) . '</br>');
	print('<b>Identity Provider: </b>'
		. htmlspecialchars($as->getAuthData('saml:sp:IdP')) . '</br>');
	print('<b>Authentication Time: </b>'
		. htmlspecialchars(gmdate("Y-m-d\TH:i:s\Z", $as->getAuthData('AuthnInstant'))) . '</br>');
	print('<b>Authentication Context Class: </b>'
		. htmlspecialchars($as->getAuthData('saml:sp:AuthnContext')) . '</br>');
	print('<b>SessionIndex: </b>'
			. htmlspecialchars($as->getAuthData('saml:sp:SessionIndex')) . '</br>');
	print('</p>');
}

if ($show_attrs) {
	print('<p>');
	print('<u>Attributes</u></br>');
	
	foreach ($attrs as $key => $value) {
		if ($value[0] != NULL) {
			if ($key == 'Roles') {
				print('<b>Roles: </b></br>');
				foreach ($attrs[$key] as $value) {
					print($value . '</br>');
				}
			} else {
				print('<b>' . $key . ': </b>' . $value[0] . '</br>');
			}
		}
	}
	print('</p>');
}
?>

</body>
</html>