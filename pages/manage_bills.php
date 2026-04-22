<?php require_once("../auth_check.php"); ?>

<?php include('../config/db.php'); ?>

<?php
// Mark as Paid
if (isset($_GET['pay'])) {
    $bill_id = $_GET['pay'];
    $conn->query("UPDATE bills SET payment_status='Paid' WHERE bill_id='$bill_id'");
}

// Fetch bills
$bills = $conn->query("
    SELECT b.*, p.first_name, p.last_name
    FROM bills b
    JOIN patients p ON b.patient_id = p.patient_id
    ORDER BY b.generated_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Bills</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #f4f7fc; }

.card-custom {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}
</style>

</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm px-4">
  <a class="navbar-brand fw-bold" href="/smartcare_hms/">SmartCare HMS</a>
</nav>

<div class="container mt-5">

<h3 class="mb-4">Manage Bills</h3>

<div class="card-custom">

<table class="table">
<tr>
<th>Patient</th>
<th>Amount</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row = $bills->fetch_assoc()): ?>
<tr>
<td><?= $row['first_name']." ".$row['last_name'] ?></td>
<td>₹<?= $row['total_amount'] ?></td>
<td>
    <span class="badge bg-<?= $row['payment_status']=='Paid' ? 'success' : 'warning' ?>">
        <?= $row['payment_status'] ?>
    </span>
</td>

<td>
<?php if ($row['payment_status'] == 'Pending'): ?>
    <a href="?pay=<?= $row['bill_id'] ?>" class="btn btn-sm btn-primary">
        Mark Paid
    </a>
<?php else: ?>
    ✅ Paid
<?php endif; ?>
</td>

</tr>
<?php endwhile; ?>

</table>

</div>
</div>

</body>
</html>