<?php require_once("../auth_check.php"); ?>
<?php allow_roles(['Admin','Receptionist']); ?>
<?php include('../config/db.php'); ?>

<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "INSERT INTO patients (first_name, last_name, gender, date_of_birth, phone, address)
            VALUES ('$fname', '$lname', '$gender', '$dob', '$phone', '$address')";

    if ($conn->query($sql)) {
        $message = "✅ Patient added successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Add Patient - SmartCare HMS</title>

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

  <h2 class="mb-4 fw-bold">Add Patient</h2>

  <!-- ✅ SUCCESS MESSAGE -->
  <?php if ($message != ""): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <div class="form-card">

    <form method="POST">

      <div class="row mb-3">
        <div class="col-md-6">
          <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
        </div>

        <div class="col-md-6">
          <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <select name="gender" class="form-control" required>
            <option value="">Select Gender</option>
            <option>Male</option>
            <option>Female</option>
            <option>Other</option>
          </select>
        </div>

        <div class="col-md-6">
          <input type="date" name="date_of_birth" class="form-control" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <input type="text" name="phone" class="form-control" placeholder="Phone" required>
        </div>

        <div class="col-md-6">
          <input type="text" name="address" class="form-control" placeholder="Address" required>
        </div>
      </div>

      <button type="submit" class="btn btn-custom w-100">Add Patient</button>

    </form>

  </div>

</div>

</body>
</html>