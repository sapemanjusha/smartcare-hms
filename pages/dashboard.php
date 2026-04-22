<?php require_once("../auth_check.php"); ?>
<?php allow_roles(['Admin']); ?>

<?php
$conn = new mysqli("localhost", "root", "", "smartcare_hms");

// Dynamic data
$patients = $conn->query("SELECT COUNT(*) as total FROM patients")->fetch_assoc()['total'];
$doctors = $conn->query("SELECT COUNT(*) as total FROM doctors")->fetch_assoc()['total'];
$revenue = $conn->query("SELECT SUM(total_amount) as total FROM bills")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f8fb;">

<!-- Navbar -->
<nav class="navbar navbar-light bg-white shadow-sm px-4">
    <span class="navbar-brand">SmartCare HMS</span>

    <div>
        <span class="me-3">👤 <?php echo $_SESSION['username']; ?></span>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-5">

    <h2 class="mb-4">Dashboard</h2>

    <div class="row">

        <div class="col-md-4">
            <div class="card p-4 shadow">
                <h5>Patients</h5>
                <h2><?php echo $patients; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 shadow">
                <h5>Doctors</h5>
                <h2><?php echo $doctors; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4 shadow">
                <h5>Revenue</h5>
                <h2>₹<?php echo $revenue; ?></h2>
            </div>
        </div>

    </div>

</div>

</body>
</html>