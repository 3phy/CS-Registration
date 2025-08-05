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
      margin-bottom: 60px;
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

    @media print {
      form, .edit-link {
        display: none !important;
      }

      .officer-table {
        page-break-inside: avoid;
      }
    }

    .filter-form {
      margin-bottom: 20px;
    }

    .filter-form label {
      margin-right: 10px;
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

<!-- Header -->
<div class="print-header">
  <div class="header-logos">
    <img src="../assets/img/EARIST.png" alt="Left Logo" style="margin-left: 60px;">
    <div class="header-title">
      <div>Republic of the Philippines</div>
      <div><strong>EULOGIO "AMANG" RODRIGUEZ<br>INSTITUTE OF SCIENCE AND TECHNOLOGY</strong></div>
      <div>Nagtahan, Sampaloc, Manila</div>
      <br>
      <div><strong>COMPUTER SCIENCE STUDENT ASSOCIATION</strong></div>
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

<?php if ($result && $result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <table class="officer-table">
      <tr>
        <td rowspan="11" class="officer-photo">
          <img src="../<?= htmlspecialchars($row['profile_pic']) ?>" alt="Photo">
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
      <tr><td style="text-align:right;"><a href="edit_officer.php?id=<?= $row['id'] ?>" class="edit-link">✏️ Edit</a></td></tr>
    </table>
  <?php endwhile; ?>
<?php else: ?>
  <p>No class officers found.</p>
<?php endif; ?>

</body>
</html>
