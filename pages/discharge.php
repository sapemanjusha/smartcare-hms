<?php require_once("../auth_check.php"); ?>
<?php include('../config/db.php'); ?>

<?php
$message = "";

// Handle discharge
if (isset($_GET['discharge_id'])) {

    $admission_id = $_GET['discharge_id'];

    // Get room_id
    $res = $conn->query("SELECT room_id FROM admissions WHERE admission_id = '$admission_id'");
    $row = $res->fetch_assoc();
    $room_id = $row['room_id'];

    // Update discharge date
    $conn->query("
        UPDATE admissions 
        SET discharge_date = NOW() 
        WHERE admission_id = '$admission_id'
    ");

    // Free the room
    $conn->query("
        UPDATE rooms 
        SET availability = 1 
        WHERE room_id = '$room_id'
    ");

    $message = "✅ Patient discharged successfully!";
}

// Fetch active admissions
$admissions = $conn->query("
    SELECT a.admission_id, p.first_name, p.last_name, r.room_type, a.admit_date
    FROM admissions a
    JOIN patients p ON a.patient_id = p.patient_id
    JOIN rooms r ON a.room_id = r.room_id
    WHERE a.discharge_date IS NULL
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Discharge - SmartCare HMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #f4f7fc; }

.card-custom {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.btn-discharge {
    background: #dc3545;
    color: white;
}

.btn-discharge:hover {
    background: #b02a37;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-light bg-white shadow-sm px-4">
  <a class="navbar-brand fw-bold" href="/smartcare_hms/">SmartCare HMS</a>

  <div class="ms-auto">
    <a href="/smartcare_hms/" class="btn btn-outline-primary me-2">Home</a>
    <a href="/smartcare_hms/pages/dashboard.php" class="btn btn-primary">Dashboard</a>
  </div>
</nav>

<!-- TITLE -->
<div class="text-center mt-5">
    <h2>Discharge Patients</h2>
    <p class="text-muted">Manage patient discharge and free room allocation</p>
</div>

<div class="container mt-4">

<?php if ($message != ""): ?>
  <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<div class="card-custom">

<table class="table table-bordered">
<thead class="table-primary">
<tr>
<th>Patient Name</th>
<th>Room</th>
<th>Admit Date</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php while($row = $admissions->fetch_assoc()): ?>
<tr>
<td><?= $row['first_name'] . " " . $row['last_name'] ?></td>
<td><?= $row['room_type'] ?></td>
<td><?= $row['admit_date'] ?></td>
<td>
    <a href="?discharge_id=<?= $row['admission_id'] ?>" 
       class="btn btn-discharge btn-sm"
       onclick="return confirm('Discharge this patient?')">
       Discharge
    </a>
</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>
</div>

</body>
</html>