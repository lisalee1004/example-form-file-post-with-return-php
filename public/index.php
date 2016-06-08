<?php

require('../vendor/autoload.php');

// Log errors (but don't display any publicly)
error_reporting(-1);
ini_set('display_errors', 0);

// Load environment variables from .env if present
if (file_exists('../.env')) {
    $dotenv = new Dotenv\Dotenv('../');
    $dotenv->load();
}

$originalFilename = basename($_FILES['userfile']['name']);
$originalExtension = (count(explode('.', $originalFilename)) > 1) ? '.' . array_reverse(explode('.', $originalFilename))[0] : '';

$newFilename = uniqid() .  $originalExtension;
$uploadTarget = 'uploads/' . $newFilename;

echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadTarget)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}