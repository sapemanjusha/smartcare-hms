<!DOCTYPE html>
<html>
<head>
<title>SmartCare HMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
}
.sidebar {
    height: 100vh;
    background: #1e293b;
    color: white;
    padding: 20px;
}
.sidebar a {
    color: #cbd5e1;
    display: block;
    margin: 10px 0;
    text-decoration: none;
}
.sidebar a:hover {
    color: white;
}
.content {
    padding: 20px;
}
.card {
    border-radius: 12px;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
<div class="col-md-2 sidebar">
    <h4>SmartCare</h4>

	<a href="/smartcare_hms/pages/dashboard.php">Dashboard</a>
	<a href="/smartcare_hms/pages/add_patient.php">Add Patient</a>
	<a href="/smartcare_hms/pages/appointment.php">Appointments</a>
	<a href="/smartcare_hms/pages/prescription.php">Prescriptions</a>
	<a href="/smartcare_hms/pages/admission.php">Admissions</a>
	<a href="/smartcare_hms/pages/billing.php">Billing</a>
	<a href="/smartcare_hms/pages/reports.php">Reports</a>
</div>

<!-- Content -->
<div class="col-md-10 content">