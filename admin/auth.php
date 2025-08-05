<?php
session_start();

$correct_user = "comsa";
$correct_pass = "comsa123";

if ($_POST['username'] === $correct_user && $_POST['password'] === $correct_pass) {
    $_SESSION['admin'] = true;
    header("Location: dashboard.php");
} else {
    echo "Invalid credentials. <a href='index.php'>Try again</a>";
}
?>