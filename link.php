<?php 

	$SU = (isSet($_GET['su'])) ? $_GET['su'] : null;

?>
<!DOCTYPE html>
<html>
<head>
	<title>URL Shortner</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="ui/css/style.css" rel="stylesheet">
</head>
<body>

	<?php require 'ui/views/link.php'; ?>

	<script src="ui/js/main.js"></script>
</body>
</html>