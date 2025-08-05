<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
require '../db_config.php';
$id = $_GET['id'];
$conn->query("DELETE FROM class_officers WHERE id=$id");
header("Location: dashboard.php");
exit;
?>