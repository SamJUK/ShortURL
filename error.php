<?php 
	
// If no error die early
if ( !isSet($_GET['e']) )
	die ( 'There is no error!' );

// Error Codes
switch ($_GET['e'])
{

	case 1:
		die ( 'That URL is already in use!' );
		break;
	default:
		die ( 'Generic Error' );
		break;
}