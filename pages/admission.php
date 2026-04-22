<?php require_once("../auth_check.php"); ?>
<?php allow_roles(['Admin','Receptionist']); ?>
<?php include('../config/db.php'); ?>

<?php
$message = "";

// Fetch patients
$patients = $conn->query("SELECT patient_id, first_name, last_name FROM patients");

// Fetch rooms
$rooms = $conn->query("SELECT room_id, room_type, availability FROM rooms");

// Handle form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $patient = $_POST['patient_id'];
    $room = $_POST['room_id'];
    $date = $_POST['admit_date'];

    // Check availability
    $check = $conn->query("SELECT availability FROM rooms WHERE room_id = '$room'");
    $row = $check->fetch_assoc();

    if ($row['availability'] == 0) {
        $message = "❌ Room is not available!";
    } else {

        // Insert admission
        $sql = "INSERT INTO admissions (patient_id, room_id, admit_date)
                VALUES ('$patient', '$room', '$date')";

        if ($conn->query($sql)) {

            // Mark room occupied
            $conn->query("UPDATE rooms SET availability = 0 WHERE room_id = '$room'");

            $message = "✅ Patient admitted successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Admit Patient - SmartCare HMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #f4f7fc; }

.form-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.btn-custom {
    background: #2a7be4;
    color: white;
}

.btn-custom:hover {
    background: #1e5ec9;
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
<div class="container text-center mt-5">
  <h2>Admit Patient</h2>
  <p class="text-muted">Assign rooms and manage hospital admissions</p>
</div>

<!-- FORM -->
<div class="container mt-4 mb-5">

<?php if ($message != ""): ?>
  <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<div class="form-card">

<form method="POST">

<!-- Patient -->
<div class="mb-3">
<select name="patient_id" class="form-control" required>
  <option value="">Select Patient</option>
  <?php while($p = $patients->fetch_assoc()): ?>
    <option value="<?= $p['patient_id'] ?>">
      <?= $p['first_name'] . " " . $p['last_name'] ?>
    </option>
  <?php endwhile; ?>
</select>
</div>

<!-- Room -->
<div class="mb-3">
<select name="room_id" class="form-control" required>
  <option value="">Select Room</option>
  <?php while($r = $rooms->fetch_assoc()): ?>
    <option value="<?= $r['room_id'] ?>" <?= $r['availability'] == 0 ? 'disabled' : '' ?>>
      <?= $r['room_type'] ?> (<?= $r['availability'] ? 'Available' : 'Occupied' ?>)
    </option>
  <?php endwhile; ?>
</select>
</div>

<!-- Date -->
<div class="mb-3">
<input type="date" name="admit_date" class="form-control" required>
</div>

<button type="submit" class="btn btn-custom w-100">Admit Patient</button>

</form>

</div>
</div>

</body>
</html>