<?php require_once("../auth_check.php"); ?>
<?php allow_roles(['Admin','Doctor']); ?>
<?php include('../config/db.php'); ?>

<?php
$message = "";

// Fetch data
$patients = $conn->query("SELECT patient_id, first_name, last_name FROM patients");
$doctors = $conn->query("SELECT doctor_id, first_name, last_name FROM doctors");
$medicines = $conn->query("SELECT medicine_id, medicine_name, stock FROM medicines");

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient = $_POST['patient_id'];
    $doctor = $_POST['doctor_id'];
    $medicine = $_POST['medicine_id'];
    $qty = $_POST['quantity'];

    // ✅ Check stock
    $check = $conn->query("SELECT stock FROM medicines WHERE medicine_id = '$medicine'");
    $row = $check->fetch_assoc();

    if ($qty > $row['stock']) {
        $message = "❌ Not enough stock available!";
    } else {
        $sql = "INSERT INTO prescriptions (patient_id, doctor_id, medicine_id, quantity)
                VALUES ('$patient', '$doctor', '$medicine', '$qty')";

        if ($conn->query($sql)) {
            $message = "✅ Prescription added successfully!";
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
  <title>Prescription - SmartCare HMS</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f4f7fc;
    }

    .form-card {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .btn-custom {
      background: #2a7be4;
      color: white;
      border-radius: 8px;
    }

    .btn-custom:hover {
      background: #1e5ec9;
    }
  </style>
</head>

<body>

<!-- 🔵 NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4">
  <a class="navbar-brand fw-bold" href="/smartcare_hms/">SmartCare HMS</a>

  <div class="ms-auto">
    <a href="/smartcare_hms/" class="btn btn-outline-primary me-2">Home</a>
    <a href="/smartcare_hms/pages/dashboard.php" class="btn btn-primary">Dashboard</a>
  </div>
</nav>

<div class="container mt-5">

  <h2 class="mb-4 fw-bold">Add Prescription</h2>

  <!-- MESSAGE -->
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

      <!-- Doctor -->
      <div class="mb-3">
        <select name="doctor_id" class="form-control" required>
          <option value="">Select Doctor</option>
          <?php while($d = $doctors->fetch_assoc()): ?>
            <option value="<?= $d['doctor_id'] ?>">
              <?= $d['first_name'] . " " . $d['last_name'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Medicine -->
      <div class="mb-3">
        <select name="medicine_id" class="form-control" required>
          <option value="">Select Medicine</option>
          <?php while($m = $medicines->fetch_assoc()): ?>
            <option value="<?= $m['medicine_id'] ?>" <?= $m['stock'] == 0 ? 'disabled' : '' ?>>
              <?= $m['medicine_name'] ?> (Stock: <?= $m['stock'] ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Quantity -->
      <div class="mb-3">
        <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
      </div>

      <button type="submit" class="btn btn-custom w-100">Add Prescription</button>

    </form>

  </div>

</div>

</body>
</html>