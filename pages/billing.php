<?php require_once("../auth_check.php"); ?> 
<?php allow_roles(['Admin','Receptionist']); ?>
<?php include('../config/db.php'); ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";
$billDetails = [];
$room_total = 0;
$medicine_total = 0;

// Fetch patients
$patients = $conn->query("SELECT patient_id, first_name, last_name FROM patients");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $patient_id = $_POST['patient_id'];

    // ROOM CHARGES
    $admission = $conn->query("
        SELECT a.*, r.charge_per_day
        FROM admissions a
        JOIN rooms r ON a.room_id = r.room_id
        WHERE a.patient_id = '$patient_id'
        ORDER BY a.admission_id DESC LIMIT 1
    ")->fetch_assoc();

    if ($admission && $admission['discharge_date']) {
        $admit = strtotime($admission['admit_date']);
        $discharge = strtotime($admission['discharge_date']);
        $days = ceil(($discharge - $admit) / (60*60*24));
        if ($days <= 0) $days = 1;

        $room_total = $days * $admission['charge_per_day'];
    }

    // MEDICINES
    $prescriptions = $conn->query("
        SELECT p.medicine_id, p.quantity, m.price, m.medicine_name
        FROM prescriptions p
        JOIN medicines m ON p.medicine_id = m.medicine_id
        WHERE p.patient_id = '$patient_id'
    ");

    $items = [];

    while ($row = $prescriptions->fetch_assoc()) {
        $amount = $row['quantity'] * $row['price'];
        $medicine_total += $amount;

        $items[] = [
            'medicine_name' => $row['medicine_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'amount' => $amount
        ];
    }

    if (count($items) > 0) {

        $grand_total = $room_total + $medicine_total;

        $conn->query("
            INSERT INTO bills (patient_id, total_amount, payment_status)
            VALUES ('$patient_id', '$grand_total', 'Pending')
        ");

        $message = "✅ Bill Generated Successfully!";
        $billDetails = $items;

    } else {
        $message = "❌ No prescriptions found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Billing</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f7fc;">

<div class="container mt-5">

<h2 class="text-center">Generate Bill</h2>

<?php if ($message): ?>
<div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<form method="POST" class="card p-4 mt-4">

<select name="patient_id" class="form-control mb-3" required>
<option value="">Select Patient</option>
<?php while($p = $patients->fetch_assoc()): ?>
<option value="<?= $p['patient_id'] ?>">
<?= $p['first_name']." ".$p['last_name'] ?>
</option>
<?php endwhile; ?>
</select>

<button class="btn btn-primary">Generate Bill</button>

</form>

<?php if ($billDetails): ?>

<div class="card p-4 mt-4">

<h4>Bill Details</h4>

<table class="table table-bordered">
<tr>
<th>Medicine</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
</tr>

<?php foreach ($billDetails as $item): ?>
<tr>
<td><?= $item['medicine_name'] ?></td>
<td><?= $item['quantity'] ?></td>
<td>₹<?= $item['price'] ?></td>
<td>₹<?= $item['amount'] ?></td>
</tr>
<?php endforeach; ?>

</table>

<p><strong>Room Charges:</strong> ₹<?= $room_total ?></p>
<p><strong>Medicine Total:</strong> ₹<?= $medicine_total ?></p>
<h4>Grand Total: ₹<?= $room_total + $medicine_total ?></h4>

</div>

<?php endif; ?>

</div>

</body>
</html>