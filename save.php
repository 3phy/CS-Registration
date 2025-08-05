<?php
require 'db_config.php';

$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$profile_pic = '';

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $filename = basename($_FILES['profile_pic']['name']);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $new_name = uniqid('pic_', true) . '.' . $ext; // Unique file name
    $target_path = $upload_dir . $new_name;

    // Optional: Validate file size (max 5MB) and type (jpg/png)
    if ($_FILES['profile_pic']['size'] > 5 * 1024 * 1024) {
        die("❌ File too large. Max 5MB allowed.");
    }

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($ext), $allowed_types)) {
        die("❌ Invalid file type. Only JPG, PNG, GIF allowed.");
    }

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
        $profile_pic = $target_path; // Save relative path
    } else {
        die("❌ Failed to upload image.");
    }
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO class_officers 
    (name, student_no, position, address, course, year_level, landline, contact_person, mobile, role, profile_pic) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssssssss", 
    $_POST['name'],
    $_POST['student_no'],
    $_POST['position'],
    $_POST['address'],
    $_POST['course'],
    $_POST['year_level'],
    $_POST['landline'],
    $_POST['contact_person'],
    $_POST['mobile'],
    $_POST['role'],
    $profile_pic
);

// Execute and respond
if ($stmt->execute()) {
    echo "✅ Officer saved successfully! <a href='admin/dashboard.php'>Go to Dashboard</a>";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>
