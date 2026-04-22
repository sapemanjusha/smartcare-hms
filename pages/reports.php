<?php require_once("../auth_check.php"); ?>
<?php allow_roles(['Admin']); ?>
<?php include('../config/db.php'); ?>

<?php
// Monthly Revenue
$revenue = $conn->query("
    SELECT DATE_FORMAT(generated_at, '%Y-%m') AS month, SUM(total_amount) AS revenue
    FROM bills
    GROUP BY month
");

// Doctor Workload
$workload = $conn->query("
    SELECT d.first_name, COUNT(a.appointment_id) AS total
    FROM doctors d
    LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
    GROUP BY d.doctor_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports - SmartCare HMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f7fc;
}

/* Section Title */
.section-title {
    text-align: center;
    margin-top: 40px;
}

/* Cards */
.card-custom {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

/* Table */
.table thead {
    background: #2a7be4;
    color: white;
}
</style>
</head>

<body>

<!-- 🔵 NAVBAR -->
<nav class="navbar navbar-light bg-white shadow-sm px-4">
  <a class="navbar-brand fw-bold" href="/smartcare_hms/">SmartCare HMS</a>

  <div class="ms-auto">
    <a href="/smartcare_hms/" class="btn btn-outline-primary me-2">Home</a>
    <a href="/smartcare_hms/pages/dashboard.php" class="btn btn-primary">Dashboard</a>
  </div>
</nav>

<!-- 🔥 TITLE -->
<div class="section-title">
    <h2>Reports & Analytics</h2>
    <p class="text-muted">Overview of hospital performance and doctor activity</p>
</div>

<div class="container mt-4 mb-5">

<!-- 💰 REVENUE CARD -->
<div class="card-custom mb-4">
    <h4 class="mb-3">Monthly Revenue</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Month</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $revenue->fetch_assoc()): ?>
            <tr>
                <td><?= $row['month'] ?></td>
                <td>₹<?= $row['revenue'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- 👨‍⚕️ WORKLOAD CARD -->
<div class="card-custom">
    <h4 class="mb-3">Doctor Workload</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Total Appointments</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $workload->fetch_assoc()): ?>
            <tr>
                <td><?= $row['first_name'] ?></td>
                <td><?= $row['total'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</div>

</body>
</html>