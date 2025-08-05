<?php
$servername = getenv('localhost');
$username   = getenv('root');
$password   = getenv('');
$dbname     = getenv('comsa_db');
$port       = getenv('3306');

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]));
}
?>
