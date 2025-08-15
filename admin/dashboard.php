<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
require '../db_config.php';

// Filters
$role = isset($_GET['role']) ? $conn->real_escape_string($_GET['role']) : '';
$year = isset($_GET['year']) ? $conn->real_escape_string($_GET['year']) : '';
$section = isset($_GET['section']) ? $conn->real_escape_string($_GET['section']) : '';

$conditions = [];
if (!empty($role)) $conditions[] = "role = '$role'";
if (!empty($year)) $conditions[] = "year_level = '$year'";
if (!empty($section)) $conditions[] = "course LIKE '%$section%'";

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
$orderBy = "ORDER BY created_at DESC";

// Custom sorting when role is Executive
if ($role === 'Executive') {
    $orderBy = "ORDER BY FIELD(position,
      'President',
      'Vice President Internal',
      'Vice President External',
      'Secretary',
      'Treasurer',
      'Auditor',
      'Business Manager',
      'PRO Male',
      'PRO Female',
      'Muse',
      'Escort'
    )";
}

$result = $conn->query("SELECT * FROM class_officers $whereClause $orderBy");

// Convert result to array for easier manipulation
$officers = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $officers[] = $row;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard - Print View</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 30px;
    }

    .print-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .header-logos {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-logos img {
      width: 100px;
    }

    .header-title {
      text-align: center;
      line-height: 1.5;
    }

    .officer-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .officer-table td {
      border: 1px solid #000;
      padding: 6px 10px;
      vertical-align: top;
    }

    .officer-photo {
      width: 180px;
      text-align: center;
    }

    .officer-photo img {
      max-width: 150px;
      border: 1px solid #000;
    }

    .signature {
      padding-top: 30px;
      text-align: center;
      font-style: italic;
    }

    form, .edit-link {
      display: inline-block;
      margin-top: 10px;
    }

    .page-group {
      page-break-after: always;
      margin-bottom: 40px;
    }

    .page-group:last-child {
      page-break-after: auto;
    }

    @media print {
      form, .edit-link {
        display: none !important;
      }

      .page-group {
        page-break-after: always;
        margin-bottom: 0;
      }

      .page-group:last-child {
        page-break-after: auto;
      }

      .print-header {
        margin-bottom: 20px;
      }

      .officer-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 12px;
        page-break-inside: avoid;
      }

      .officer-table td {
        border: 1px solid #000;
        padding: 4px 8px;
        vertical-align: top;
      }

      .officer-photo img {
        max-width: 120px;
        border: 1px solid #000;
      }

      .signature {
        padding-top: 20px;
        text-align: center;
        font-style: italic;
      }

      body {
        margin: 20px;
      }
    }

  </style>
</head>
<body>

<!-- Filter Form -->
<form method="GET" class="filter-form">
  <label>
    Role:
    <select name="role">
      <option value="">All</option>
      <option value="Class Officer" <?= $role == 'Class Officer' ? 'selected' : '' ?>>Class Officer</option>
      <option value="Executive" <?= $role == 'Executive' ? 'selected' : '' ?>>Executive</option>
      <option value="Member" <?= $role == 'Member' ? 'selected' : '' ?>>Member</option>
    </select>
  </label>

  <label>
    Year:
    <select name="year">
      <option value="">All</option>
      <option value="1st Year" <?= $year == '1st Year' ? 'selected' : '' ?>>1st Year</option>
      <option value="2nd Year" <?= $year == '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
      <option value="3rd Year" <?= $year == '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
      <option value="4th Year" <?= $year == '4th Year' ? 'selected' : '' ?>>4th Year</option>
    </select>
  </label>

  <label>
    Section:
    <select name="section">
      <option value="">All</option>
      <option value="A" <?= $section == 'A' ? 'selected' : '' ?>>A</option>
      <option value="B" <?= $section == 'B' ? 'selected' : '' ?>>B</option>
      <option value="C" <?= $section == 'C' ? 'selected' : '' ?>>C</option>
    </select>
  </label>

  <button type="submit">Filter</button>
  <button type="button" onclick="window.print()">🖨️ Print</button>
</form>

<?php if (!empty($officers)): ?>
  <?php 
  // Group officers into chunks of 3
  $officerChunks = array_chunk($officers, 3);
  ?>
  
  <?php foreach ($officerChunks as $chunkIndex => $chunk): ?>
    <div class="page-group">
      <!-- Header for each page -->
      <div class="print-header">
        <div class="header-logos">
          <img src="../assets/img/EARIST.png" alt="Left Logo" style="margin-left: 60px;">
          <div class="header-title" style="font-size: 13px;">
            <div>Republic of the Philippines</div>
            <div><strong>EULOGIO "AMANG" RODRIGUEZ<br>INSTITUTE OF SCIENCE AND TECHNOLOGY</strong></div>
            <div>Nagtahan, Sampaloc, Manila</div>
            <br><div><strong>COMPUTER SCIENCE STUDENT ASSOCIATION</strong></div>
            <div><strong>
              <?php
                if ($role === 'Executive') {
                    echo 'EXECUTIVE OFFICERS LIST';
                } elseif ($role === 'Class Officer') {
                    echo 'CLASS OFFICERS LIST';
                } elseif ($role === 'Member') {
                    echo 'MEMBERS LIST';
                } else {
                    echo 'LIST';
                }
              ?>
            </strong></div>
          </div>
          <img src="../assets/img/comsa.png" alt="Right Logo" style="margin-right: 70px;">
        </div>
      </div>

      <!-- Officers in this chunk (up to 3) -->
      <?php foreach ($chunk as $row): ?>
        <table class="officer-table">
          <tr>
            <td rowspan="11" class="officer-photo">
              <?php
                $profilePicPath = '../uploads/' . ltrim($row['profile_pic'], '/');
                if (!empty($row['profile_pic']) && file_exists($profilePicPath)) {
                    echo '<img src="' . htmlspecialchars($profilePicPath) . '" alt="Photo">';
                } else {
                    echo '<span>No photo</span>';
                }
              ?>
            </td>
            <td><strong>Name:</strong> <?= htmlspecialchars($row['name']) ?></td>
          </tr>
          <tr><td><strong>Student No.:</strong> <?= htmlspecialchars($row['student_no']) ?></td></tr>
          <tr><td><strong>Position:</strong> <?= htmlspecialchars($row['position']) ?></td></tr>
          <tr><td><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></td></tr>
          <tr><td><strong>College, Program, Major, Section:</strong> <?= htmlspecialchars($row['course']) ?></td></tr>
          <tr><td><strong>Year Level:</strong> <?= htmlspecialchars($row['year_level']) ?></td></tr>
          <tr><td><strong>Landline/Mobile No.:</strong> <?= htmlspecialchars($row['landline']) ?></td></tr>
          <tr><td><strong>Contact Person (in case of emergency):</strong> <?= htmlspecialchars($row['contact_person']) ?></td></tr>
          <tr><td><strong>Emergency Landline/Mobile No.:</strong> <?= htmlspecialchars($row['mobile']) ?></td></tr>
          <tr><td class="signature"><strong>Signature</strong><br>__________________________</td></tr>
          <tr>
            <td style="text-align:right;">
              <a href="edit_officer.php?id=<?= htmlspecialchars($row['id']) ?>" class="edit-link">✏️ Edit</a>
              <form method="POST" action="delete.php?id=<?= htmlspecialchars($row['id']) ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this officer?');">
                <button type="submit" style="background:none;border:none;color:red;cursor:pointer;">🗑️ Delete</button>
              </form>
            </td>
          </tr>
        </table>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <div class="print-header">
    <div class="header-logos">
      <img src="../assets/img/EARIST.png" alt="Left Logo" style="margin-left: 60px;">
      <div class="header-title" style="font-size: 13px;">
        <div>Republic of the Philippines</div>
        <div><strong>EULOGIO "AMANG" RODRIGUEZ<br>INSTITUTE OF SCIENCE AND TECHNOLOGY</strong></div>
        <div>Nagtahan, Sampaloc, Manila</div>
        <br><div><strong>COMPUTER SCIENCE STUDENT ASSOCIATION</strong></div>
        <div><strong>LIST</strong></div>
      </div>
      <img src="../assets/img/comsa.png" alt="Right Logo" style="margin-right: 70px;">
    </div>
  </div>
  <p>No class officers found.</p>
<?php endif; ?>

</body>
</html>