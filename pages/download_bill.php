<?php require_once("../auth_check.php"); ?>
<?php
require '../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include('../config/db.php');

$bill_id = $_GET['id'];

// Get bill + patient
$bill = $conn->query("
    SELECT b.*, p.first_name, p.last_name
    FROM bills b
    JOIN patients p ON b.patient_id = p.patient_id
    WHERE b.bill_id='$bill_id'
")->fetch_assoc();

$patient_id = $bill['patient_id'];

// Get admission + room
$admission = $conn->query("
    SELECT a.*, r.room_type, r.charge_per_day
    FROM admissions a
    JOIN rooms r ON a.room_id = r.room_id
    WHERE a.patient_id = '$patient_id'
    ORDER BY a.admission_id DESC LIMIT 1
")->fetch_assoc();

// Calculate stay
$days = 1;
$room_total = 0;

if ($admission && $admission['discharge_date']) {
    $admit = strtotime($admission['admit_date']);
    $discharge = strtotime($admission['discharge_date']);
    $days = ceil(($discharge - $admit) / (60*60*24));
    if ($days <= 0) $days = 1;

    $room_total = $days * $admission['charge_per_day'];
}

// Get medicines
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

$html = "
<!DOCTYPE html>
<html>
<head>
<style>
body {
    font-family: Arial;
    padding: 20px;
}

.header {
    text-align: center;
}

.header h1 {
    margin: 0;
    color: #2a7be4;
}

.sub {
    color: gray;
    font-size: 14px;
}

.line {
    border-top: 2px solid #2a7be4;
    margin: 15px 0;
}

.section {
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid #ddd;
}

th {
    background: #2a7be4;
    color: white;
    padding: 10px;
}

td {
    padding: 8px;
}

.total-box {
    text-align: right;
    margin-top: 20px;
}

.total-box h2 {
    color: #2a7be4;
}

.footer {
    margin-top: 30px;
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

<div class='section'>
    <strong>Invoice ID:</strong> {$bill['bill_id']}<br>
    <strong>Patient:</strong> {$bill['first_name']} {$bill['last_name']}<br>
    <strong>Date:</strong> {$bill['generated_at']}<br>
    <strong>Status:</strong> {$bill['payment_status']}
</div>

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

<div class='total-box'>
    <h4>Room Total: ₹{$room_total}</h4>
    <h4>Medicine Total: ₹{$medicine_total}</h4>
    <h2>Grand Total: ₹{$bill['total_amount']}</h2>
</div>

<div class='footer'>
    Thank you for choosing SmartCare HMS<br>
    Wishing you good health!
</div>

</body>
</html>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("SmartCare_Final_Bill.pdf", ["Attachment" => true]);