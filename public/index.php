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
$lisavar = getenv('name');
echo $lisavar;

if (empty($_FILES)) {
    die('Service online');
}

// Twilio Exmaple from https://www.twilio.com/docs/libraries/php
$AccountSid = getenv('twilio_account_sid'); // Your Account SID from www.twilio.com/console
$AuthToken = getenv('twilio_account_auth_token');   // Your Auth Token from www.twilio.com/console

$client = new Services_Twilio($AccountSid, $AuthToken);

$message = $client->account->messages->create(array(
    "From" => getenv('secretary_phone'), // From a valid Twilio number
    "To" => $_POST['phone_number'],   // Text this number
    "Body" => "Your Parking spot is ready!",
));


// Get the file size limit (or default to 15 MB)
// Note, if you go over a certain size, you may need to add a custom ini setting for Heroku
$maxFileSize = getenv('MAX_FILE_SIZE');
$maxFileSize = (!empty($maxFileSize)) ? $maxFileSize : 15;

// Convert to bytes
$maxFileSizeInBytes = $maxFileSize * 1024 * 1024;

if ($_FILES['userfile']['size'] > $maxFileSizeInBytes) {
    die('The file you are trying to upload is too big. It must not be more than ' . $maxFileSize . ' megabytes (MB).');
}

$originalFilename = basename($_FILES['userfile']['name']);
$originalExtension = (count(explode('.', $originalFilename)) > 1) ? '.' . array_reverse(explode('.', $originalFilename))[0] : '';

$newFilename = uniqid() .  $originalExtension;
$uploadTarget = 'uploads/' . $newFilename;

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadTarget)) {
    //echo "File is valid, and was successfully uploaded.\n";

        $redirectTarget = (!empty($_POST['redirect_target'])) ? $_POST['redirect_target'] : $_SERVER['HTTP_REFERER'];
        header('Location: ' . $redirectTarget);
        die();
}
