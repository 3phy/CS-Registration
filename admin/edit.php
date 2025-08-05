<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
require '../db_config.php';
$id = $_GET['id'];
$data = $conn->query("SELECT * FROM class_officers WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE class_officers SET name=?, student_no=?, position=?, year_level=? WHERE id=?");
    $stmt->bind_param("ssssi", $_POST['name'], $_POST['student_no'], $_POST['position'], $_POST['year_level'], $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}
?>
<h2>Edit Officer</h2>
<form method="post">
  <input name="name" value="<?= $data['name'] ?>" required><br>
  <input name="student_no" value="<?= $data['student_no'] ?>" required><br>
  <input name="position" value="<?= $data['position'] ?>"><br>
  <input name="year_level" value="<?= $data['year_level'] ?>"><br>
  <button type="submit">Save</button>
</form>