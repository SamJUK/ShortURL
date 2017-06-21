<?php
// Error settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require ('Shortlink.php');

$shortLink = new ShortLink('test.com');

var_dump($shortLink);