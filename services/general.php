<!DOCTYPE html>
<html>
<head>
<title>General</title>

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
  display:flex;
  align-items:center;
  justify-content:center;
  margin:auto;
  margin-bottom:15px;
  font-size:24px;
}
</style>

</head>

<body>

<header class="p-3 bg-white shadow-sm d-flex justify-content-between">
<h3>SmartCare HMS</h3>
<a href="../index.php" class="btn btn-primary">Home</a>
</header>

<section class="text-center py-5 bg-light">
<h2>General Medical Services</h2>
<p>All essential healthcare services</p>
</section>

<div class="container py-5">
<div class="row text-center">

<div class="col-md-4">
<div class="card-box">
<div class="icon-circle"><i class="bi bi-clipboard-check"></i></div>
<h5>Checkups</h5>
</div>
</div>

<div class="col-md-4">
<div class="card-box">
<div class="icon-circle"><i class="bi bi-person-check"></i></div>
<h5>Consultation</h5>
</div>
</div>

<div class="col-md-4">
<div class="card-box">
<div class="icon-circle"><i class="bi bi-heart"></i></div>
<h5>Preventive Care</h5>
</div>
</div>

</div>
</div>

</body>
</html>