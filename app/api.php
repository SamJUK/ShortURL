<?php
// Error settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required classes
require 'classes/Shortlink.php';

// URL Regex
$URLRegex = "/[(http(s)?):\/\/(www\.)?a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)/i";

// Create JSON Object to echo out
$JDATA = new stdClass();

// If no action parmeter is declared, die with json output
if ( !isSet($_GET['action']) )
{
	$JDATA->Success = false;
	$JDATA->Message = 'No endpoint specified';
	die(json_encode($JDATA, JSON_FORCE_OBJECT)); 
}

// Switch dependent on the action parameter
switch ($_GET['action']) {
	
	// Create Endpoint
	case 'create':

		// Die if missing URL parameter
		if ( !isSet($_GET['url']) )
		{
			$JDATA->Success = false;
			$JDATA->Message = 'This endpoint requires an URL parameter';
			die(json_encode($JDATA, JSON_FORCE_OBJECT)); 
		};

		// Trimmy Trimmy the URLy
		$url = trim($_GET['url'], '!"#$%&\'()*+,-./@:;<=>[\\]^_`{|}~');

		// If the URL is bad die
		if (!preg_match($URLRegex, $url))
		{
			$JDATA->Success = false;
			$JDATA->Message = 'Invalid URL specified';
			die(json_encode($JDATA, JSON_FORCE_OBJECT)); 
		};

		// Create Shortlink and die with json output
		$Shortlink = new Shortlink($_GET['url']);
		$SLJSONString = $Shortlink->retreiveJSONState();

		die ($SLJSONString);
	break;
	
	// Duplicate endpoint
	case 'duplicate':

		// Die if missing URL parameter
		if ( !isSet($_GET['url']) )
		{
			$JDATA->Success = false;
			$JDATA->Message = 'This endpoint requires an URL parameter';
			die(json_encode($JDATA, JSON_FORCE_OBJECT)); 
		};

		// Not a valid URL
		if (false)
		{
			$JDATA->Success = false;
			$JDATA->Message = 'Invalid URL specified';
			die(json_encode($JDATA, JSON_FORCE_OBJECT)); 
		};

		// Return status of URL
		// Returns true if it has been shortend
		if(Shortlink::hasURLbeenShortend($_GET['url']))
		{
			$JDATA->Success = true;
			$JDATA->URL = $_GET['url'];
			die(json_encode($JDATA, JSON_FORCE_OBJECT)); 
		}
		else
		{
			$JDATA->Success = false;
			$JDATA->URL = $_GET['url'];
			die(json_encode($JDATA, JSON_FORCE_OBJECT)); 
		}
		break;

	default:
		$JDATA->Success = false;
		$JDATA->Message = 'No such endpoint';
		die(json_encode($JDATA, JSON_FORCE_OBJECT)); 
		break;
}