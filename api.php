<?php
// Error settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'Shortlink.php';

//$Shortlink = new Shortlink('https://samdjames.uk');

if ( !isSet($_GET['action']) )
	die("{\n\t Success: false,\n\t Message: 'No endpoint specified' \n}"); 


switch ($_GET['action']) {
	case 'create':
		// Missing URL parameter
		if ( !isSet($_GET['url']) )
			die ("{\"Success\": false, \"Message\": 'This endpoint requires an URL parameter'}");

		// Not a valid URL
		if (false)
			die ("{\"Success\": false, \"Message\": 'Invalid URL specified'}");

		// Create Shortlink
		$Shortlink = new Shortlink($_GET['url']);
		$SLJSONString = $Shortlink->retreiveJSONState();

		die ($SLJSONString);
		break;
	
	case 'duplicate':
		if ( !isSet($_GET['url']) )
			die ("{\"Success\": false, \"Message\": 'This endpoint requires an URL parameter'}");

		// Not a valid URL
		if (false)
			die ("{\"Success\": false, \"Message\": 'Invalid URL specified'}");

		// Return status of URL
		// Returns true if it has been shortend
		if(Shortlink::hasURLbeenShortend($_GET['url']))
			die ('{"Success": true, "URL": "'.$_GET['url'].'"}');
		else
			die ('{"Success": false, "URL": "'.$_GET['url'].'"}');
		break;

	default:
		die ("{\"Success\": false,\"Message\": 'No such endpoint'}");
		break;
}