<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Emergency</title>

<link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

<style>
.card-box {
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
  transition: 0.3s;
}
.card-box:hover {
  transform: translateY(-10px);
}
.icon-circle {
  width: 60px;
  height: 60px;
  background: #2a7be4;
  color: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
  font-size: 24px;
}
</style>
</head>

<body>

<header class="p-3 bg-white shadow-sm d-flex justify-content-between">
<h3>SmartCare HMS</h3>
<a href="../index.php" class="btn btn-primary">Home</a>
</header>

<section class="text-center py-5 bg-light">
<h2>Emergency & Critical Care</h2>
<p>Immediate life-saving treatment</p>
</section>

<div class="container py-5">
<div class="row">

<div class="col-md-3">
<div class="card-box">
<div class="icon-circle"><i class="bi bi-heart-pulse"></i></div>
<h5>Trauma Care</h5>
</div>
</div>

<div class="col-md-3">
<div class="card-box">
<div class="icon-circle"><i class="bi bi-heart"></i></div>
<h5>Cardiac Emergencies</h5>
</div>
</div>

<div class="col-md-3">
<div class="card-box">
<div class="icon-circle"><i class="bi bi-hospital"></i></div>
<h5>ICU Support</h5>
</div>
</div>

<div class="col-md-3">
<div class="card-box">
<div class="icon-circle"><i class="bi bi-truck"></i></div>
<h5>Ambulance</h5>
</div>
</div>

</div>
</div>

</body>
</html>