<?php require_once("../auth_check.php"); ?>
<?php allow_roles(['Admin','Receptionist']); ?>
<?php include('../config/db.php'); ?>

<?php
$message = "";
$billDetails = [];

// Fetch patients
$patients = $conn->query("SELECT patient_id, first_name, last_name FROM patients");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $patient_id = $_POST['patient_id'];

    // Get prescriptions for this patient
    $prescriptions = $conn->query("
        SELECT p.medicine_id, p.quantity, m.price, m.medicine_name
        FROM prescriptions p
        JOIN medicines m ON p.medicine_id = m.medicine_id
        WHERE p.patient_id = '$patient_id'
    ");

    $total = 0;
    $items = [];

    while ($row = $prescriptions->fetch_assoc()) {
        $amount = $row['quantity'] * $row['price'];
        $total += $amount;

        $items[] = [
            'medicine_id' => $row['medicine_id'],
            'medicine_name' => $row['medicine_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'amount' => $amount
        ];
    }

    if (count($items) > 0) {

        // Insert into bills
        $conn->query("
            INSERT INTO bills (patient_id, total_amount, payment_status)
            VALUES ('$patient_id', '$total', 'Pending')
        ");

        $bill_id = $conn->insert_id;

        // Insert bill items
        foreach ($items as $item) {
            $conn->query("
                INSERT INTO bill_items (bill_id, medicine_id, quantity, price)
                VALUES ('$bill_id', '{$item['medicine_id']}', '{$item['quantity']}', '{$item['price']}')
            ");
        }

        $message = "✅ Bill Generated Successfully!";
        $billDetails = $items;

    } else {
        $message = "❌ No prescriptions found for this patient!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Billing - SmartCare HMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #f4f7fc; }

.form-card, .table-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.btn-custom {
    background: #2a7be4;
    border: none;
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
  <h2>Generate Bill</h2>
  <p class="text-muted">Automatically generate billing based on prescriptions</p>
</div>

<!-- FORM -->
<div class="container mt-4">

<?php if ($message != ""): ?>
  <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<div class="form-card">

<form method="POST">

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

<button type="submit" class="btn btn-custom w-100 text-white">Generate Bill</button>

</form>

</div>

<!-- BILL TABLE -->
<?php if (!empty($billDetails)): ?>

<div class="table-card mt-4">

<h5 class="mb-3">Bill Breakdown</h5>

<table class="table table-bordered">
<thead>
<tr>
<th>Medicine</th>
<th>Quantity</th>
<th>Price</th>
<th>Total</th>
</tr>
</thead>

<tbody>
<?php $grand = 0; ?>
<?php foreach ($billDetails as $item): ?>
<tr>
<td><?= $item['medicine_name'] ?></td>
<td><?= $item['quantity'] ?></td>
<td>₹<?= $item['price'] ?></td>
<td>₹<?= $item['amount'] ?></td>
</tr>
<?php $grand += $item['amount']; ?>
<?php endforeach; ?>
</tbody>

<tfoot>
<tr>
<th colspan="3">Grand Total</th>
<th>₹<?= $grand ?></th>
</tr>
</tfoot>

</table>

</div>

<?php endif; ?>

</div>

</body>
</html>