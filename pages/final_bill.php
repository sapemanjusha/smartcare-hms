<?php require_once("../auth_check.php"); ?>
<?php allow_roles(['Admin','Receptionist']); ?>
<?php include('../config/db.php'); ?>

<?php
$result = null;

if (isset($_POST['generate'])) {

    $patient_id = $_POST['patient_id'];

    // Get latest admission
    $admission = $conn->query("
        SELECT a.*, r.charge_per_day
        FROM admissions a
        JOIN rooms r ON a.room_id = r.room_id
        WHERE a.patient_id = '$patient_id'
        ORDER BY a.admission_id DESC LIMIT 1
    ")->fetch_assoc();

    if ($admission && $admission['discharge_date'] != NULL) {

        // Calculate days stayed
        $admit = strtotime($admission['admit_date']);
        $discharge = strtotime($admission['discharge_date']);
        $days = ceil(($discharge - $admit) / (60*60*24));

        if ($days <= 0) $days = 1;

        $room_total = $days * $admission['charge_per_day'];

        // Medicine charges
        $meds = $conn->query("
            SELECT m.medicine_name, m.price, p.quantity
            FROM prescriptions p
            JOIN medicines m ON p.medicine_id = m.medicine_id
            WHERE p.patient_id = '$patient_id'
        ");

        $medicine_total = 0;
        $medicine_list = [];

        while ($row = $meds->fetch_assoc()) {
            $cost = $row['price'] * $row['quantity'];
            $medicine_total += $cost;
            $medicine_list[] = [
                'name' => $row['medicine_name'],
                'qty' => $row['quantity'],
                'price' => $row['price'],
                'total' => $cost
            ];
        }

        $grand_total = $room_total + $medicine_total;

        // Save bill
        $conn->query("
            INSERT INTO bills (patient_id, total_amount, payment_status)
            VALUES ('$patient_id', '$grand_total', 'Pending')
        ");

        $result = [
            'days' => $days,
            'room_total' => $room_total,
            'medicines' => $medicine_list,
            'medicine_total' => $medicine_total,
            'grand_total' => $grand_total
        ];
    } else {
        $error = "❌ Patient not discharged yet!";
    }
}

// Fetch patients
$patients = $conn->query("SELECT * FROM patients");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Final Bill - SmartCare HMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #f4f7fc; }

.card-custom {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.btn-primary {
    background: #2a7be4;
    border: none;
}

.btn-primary:hover {
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
<div class="text-center mt-5">
    <h2>Final Billing</h2>
    <p class="text-muted">Generate complete bill after patient discharge</p>
</div>

<div class="container mt-4">

<div class="card-custom">

<form method="POST">
<select name="patient_id" class="form-control mb-3" required>
    <option value="">Select Patient</option>
    <?php while($row = $patients->fetch_assoc()): ?>
        <option value="<?= $row['patient_id'] ?>">
            <?= $row['first_name']." ".$row['last_name'] ?>
        </option>
    <?php endwhile; ?>
</select>

<button type="submit" name="generate" class="btn btn-primary w-100">
    Generate Final Bill
</button>
</form>

</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger mt-3"><?= $error ?></div>
<?php endif; ?>

<?php if ($result): ?>

<div class="card-custom mt-4">

<h4>Bill Summary</h4>

<p><strong>Days Stayed:</strong> <?= $result['days'] ?></p>
<p><strong>Room Charges:</strong> ₹<?= $result['room_total'] ?></p>

<h5 class="mt-3">Medicines</h5>

<table class="table table-bordered">
<tr>
<th>Name</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
</tr>

<?php foreach ($result['medicines'] as $med): ?>
<tr>
<td><?= $med['name'] ?></td>
<td><?= $med['qty'] ?></td>
<td>₹<?= $med['price'] ?></td>
<td>₹<?= $med['total'] ?></td>
</tr>
<?php endforeach; ?>

</table>

<p><strong>Medicine Total:</strong> ₹<?= $result['medicine_total'] ?></p>

<h4 class="mt-3">Grand Total: ₹<?= $result['grand_total'] ?></h4>

</div>

<?php endif; ?>

</div>

</body>
</html>