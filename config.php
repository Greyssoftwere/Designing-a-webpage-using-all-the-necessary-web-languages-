<?php


$dbHost = 'YOUR_MYSQL_HOST';
$dbUser = 'YOUR_MYSQL_USERNAME';
$dbPassword = 'YOUR_HOSTING_ACCOUNT_PASSWORD';
$dbName = 'YOUR_DATABASE_NAME';

$conn = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($conn->connect_error) {
    http_response_code(500);
    die('Database connection failed.');
}

$conn->set_charset('utf8mb4');
?>
