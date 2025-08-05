<?php
require 'connect.php';
header('Content-Type: application/json');

$sql = "SELECT * FROM users"; // make sure this table exists
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);

$conn->close();
?>
