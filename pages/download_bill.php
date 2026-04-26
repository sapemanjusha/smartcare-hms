<?php require_once("../auth_check.php"); ?>

<?php
require '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include('../config/db.php');

$bill_id = $_GET['id'];

// Bill + Patient
$bill = $conn->query("
    SELECT b.*, p.first_name, p.last_name
    FROM bills b
    JOIN patients p ON b.patient_id = p.patient_id
    WHERE b.bill_id='$bill_id'
")->fetch_assoc();

$patient_id = $bill['patient_id'];

// Admission + Room
$admission = $conn->query("
    SELECT a.*, r.room_type, r.charge_per_day
    FROM admissions a
    JOIN rooms r ON a.room_id = r.room_id
    WHERE a.patient_id = '$patient_id'
    ORDER BY a.admission_id DESC LIMIT 1
")->fetch_assoc();

// Stay calculation
$days = 1;
$room_total = 0;

if ($admission && $admission['discharge_date']) {
    $admit = strtotime($admission['admit_date']);
    $discharge = strtotime($admission['discharge_date']);
    $days = ceil(($discharge - $admit) / (60*60*24));
    if ($days <= 0) $days = 1;

    $room_total = $days * $admission['charge_per_day'];
}

// Medicines
$meds = $conn->query("
    SELECT m.medicine_name, m.price, p.quantity
    FROM prescriptions p
    JOIN medicines m ON p.medicine_id = m.medicine_id
    WHERE p.patient_id = '$patient_id'
");

$medicine_rows = '';
$medicine_total = 0;

while ($row = $meds->fetch_assoc()) {
    $total = $row['price'] * $row['quantity'];
    $medicine_total += $total;

    $medicine_rows .= "
    <tr>
        <td>{$row['medicine_name']}</td>
        <td>{$row['quantity']}</td>
        <td>₹{$row['price']}</td>
        <td>₹{$total}</td>
    </tr>";
}

// ✅ FINAL CORRECT TOTAL (IMPORTANT FIX)
$grand_total = $room_total + $medicine_total;

$html = "
<!DOCTYPE html>
<html>
<head>
<style>
body {
    font-family: Arial, sans-serif;
    padding: 30px;
    color: #333;
}

.header {
    text-align: center;
}

.header h1 {
    margin: 0;
    color: #2a7be4;
    font-size: 28px;
}

.sub {
    font-size: 14px;
    color: gray;
}

.line {
    border-top: 2px solid #2a7be4;
    margin: 20px 0;
}

.section {
    margin-bottom: 20px;
}

.info-table {
    width: 100%;
    margin-bottom: 20px;
}

.info-table td {
    padding: 6px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table th {
    background: #2a7be4;
    color: white;
    padding: 10px;
}

table td {
    padding: 8px;
    border: 1px solid #ddd;
}

.summary {
    margin-top: 25px;
    text-align: right;
}

.summary h4 {
    margin: 5px 0;
}

.summary .grand {
    font-size: 22px;
    color: #2a7be4;
    font-weight: bold;
}

.footer {
    margin-top: 40px;
    text-align: center;
    font-size: 13px;
    color: gray;
}
</style>
</head>

<body>

<div class='header'>
    <h1>SmartCare HMS</h1>
    <div class='sub'>Hospital Invoice</div>
</div>

<div class='line'></div>

<table class='info-table'>
<tr>
<td><strong>Invoice ID:</strong> {$bill['bill_id']}</td>
<td><strong>Date:</strong> {$bill['generated_at']}</td>
</tr>
<tr>
<td><strong>Patient:</strong> {$bill['first_name']} {$bill['last_name']}</td>
<td><strong>Status:</strong> {$bill['payment_status']}</td>
</tr>
</table>

<div class='section'>
<h3>Room Charges</h3>

<table>
<tr>
<th>Room Type</th>
<th>Days</th>
<th>Charge/Day</th>
<th>Total</th>
</tr>

<tr>
<td>{$admission['room_type']}</td>
<td>{$days}</td>
<td>₹{$admission['charge_per_day']}</td>
<td>₹{$room_total}</td>
</tr>
</table>
</div>

<div class='section'>
<h3>Medicine Charges</h3>

<table>
<tr>
<th>Medicine</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
</tr>

$medicine_rows

</table>
</div>

<div class='summary'>
<h4>Room Total: ₹{$room_total}</h4>
<h4>Medicine Total: ₹{$medicine_total}</h4>
<h4 class='grand'>Grand Total: ₹{$grand_total}</h4>
</div>

<div class='footer'>
Thank you for choosing SmartCare HMS<br>
Wishing you a speedy recovery 💙
</div>

</body>
</html>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("SmartCare_Invoice.pdf", ["Attachment" => true]);