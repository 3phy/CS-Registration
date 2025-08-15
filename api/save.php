<?php
require '../db_config.php';

$upload_dir = '../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$profile_pic = '';

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $filename = basename($_FILES['profile_pic']['name']);
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $new_name = uniqid('pic_', true) . '.' . $ext; // Unique file name
    $target_path = $upload_dir . '/' . $new_name;

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
    // Fetch the newly inserted officer for verification
    $last_id = $conn->insert_id;
    $result = $conn->query("SELECT * FROM class_officers WHERE id = $last_id");
    if ($result && $result->num_rows > 0):
        $row = $result->fetch_assoc();
?>
    <div class="officer-summary" style="max-width:400px;margin:auto;padding:20px;border:1px solid #ccc;border-radius:8px;">
        <h3>Officer Details Submitted</h3>
        <ul style="list-style:none;padding:0;">
            <li><strong>Name:</strong> <?= htmlspecialchars($row['name']) ?></li>
            <li><strong>Student No.:</strong> <?= htmlspecialchars($row['student_no']) ?></li>
            <li><strong>Position:</strong> <?= htmlspecialchars($row['position']) ?></li>
            <li><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></li>
            <li><strong>Course:</strong> <?= htmlspecialchars($row['course']) ?></li>
            <li><strong>Year Level:</strong> <?= htmlspecialchars($row['year_level']) ?></li>
            <li><strong>Landline/Mobile No.:</strong> <?= htmlspecialchars($row['landline']) ?></li>
            <li><strong>Contact Person:</strong> <?= htmlspecialchars($row['contact_person']) ?></li>
            <li><strong>Emergency No.:</strong> <?= htmlspecialchars($row['mobile']) ?></li>
            <li><strong>Role:</strong> <?= htmlspecialchars($row['role']) ?></li>
        </ul>
        <?php
            $profilePicPath = '../uploads/' . ltrim($row['profile_pic'], '/');
            if (!empty($row['profile_pic']) && file_exists($profilePicPath)) {
                echo '<div style="margin-top:10px;"><img src="' . htmlspecialchars($profilePicPath) . '" alt="Photo" style="max-width:100px;border-radius:4px;"></div>';
            }
        ?>
        <div style="text-align:center; margin-top:20px;">
            <button type="button" onclick="window.location.href='../index.html';" style="padding:10px 20px;">✅ Done</button>
        </div>
    </div>
<?php
    else:
        echo "❌ Error fetching officer details.";
    endif;
} else {
    echo "❌ Error: " . $stmt->error;
}

?>
