<?php require_once("../auth_check.php"); ?>
<?php allow_roles(['Admin','Receptionist']); ?>
<?php include("../config/db.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Book Appointment - SmartCare</title>

  <!-- Bootstrap -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/main.css" rel="stylesheet">
</head>

<body>

<!-- ======= HEADER ======= -->
<header class="bg-white shadow-sm p-3 mb-4">
  <div class="container d-flex justify-content-between">
    <h3>SmartCare HMS</h3>
    <a href="../index.php" class="btn btn-outline-primary">Back to Home</a>
  </div>
</header>

<!-- ======= HERO SECTION ======= -->
<section style="background: linear-gradient(to right, #1977cc, #4da3ff); color: white; padding: 60px 0;">
  <div class="container text-center">
    <h1>Book an Appointment</h1>
    <p>Schedule your visit with our expert doctors</p>
  </div>
</section>

<!-- ======= FORM SECTION ======= -->
<section class="py-5">
  <div class="container">
    <div class="card shadow-lg p-4" style="border-radius: 15px;">

      <h3 class="mb-4 text-center">Appointment Details</h3>

      <form method="POST">

        <!-- Patient -->
        <div class="mb-3">
          <label class="form-label">Select Patient</label>
          <select name="patient_id" class="form-control" required>
            <option value="">-- Select Patient --</option>
            <?php
            $result = $conn->query("SELECT * FROM patients");
            while($row = $result->fetch_assoc()){
              echo "<option value='{$row['patient_id']}'>{$row['first_name']} {$row['last_name']}</option>";
            }
            ?>
          </select>
        </div>

        <!-- Doctor -->
        <div class="mb-3">
          <label class="form-label">Select Doctor</label>
          <select name="doctor_id" class="form-control" required>
            <option value="">-- Select Doctor --</option>
            <?php
            $result = $conn->query("SELECT * FROM doctors");
            while($row = $result->fetch_assoc()){
              echo "<option value='{$row['doctor_id']}'>Dr. {$row['first_name']} ({$row['specialization']})</option>";
            }
            ?>
          </select>
        </div>

        <!-- Date -->
        <div class="mb-3">
          <label class="form-label">Appointment Date & Time</label>
          <input type="datetime-local" name="appointment_datetime" class="form-control" required>
        </div>

        <!-- Button -->
        <div class="text-center">
          <button type="submit" name="submit" class="btn btn-primary px-5">
            Book Appointment
          </button>
        </div>

      </form>

    </div>
  </div>
</section>

<!-- ======= FOOTER ======= -->
<footer class="bg-dark text-white text-center p-3 mt-5">
  <p>© 2026 SmartCare HMS | DBMS Project</p>
</footer>

</body>
</html>

<?php
if(isset($_POST['submit'])){
  $patient = $_POST['patient_id'];
  $doctor = $_POST['doctor_id'];
  $date = $_POST['appointment_datetime'];

  $conn->query("INSERT INTO appointments(patient_id, doctor_id, appointment_datetime, status)
                VALUES('$patient', '$doctor', '$date', 'Scheduled')");

  echo "<script>alert('Appointment Booked Successfully!'); window.location='../index.php';</script>";
}
?>