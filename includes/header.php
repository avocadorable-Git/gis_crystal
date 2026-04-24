<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="/index.php">Inventory</a>
    <div>
      <a class="btn btn-outline-light btn-sm me-1" href="/product/index.php">Products</a>
      <a class="btn btn-outline-light btn-sm me-1" href="/vendor/index.php">Vendors</a>
      <a class="btn btn-outline-light btn-sm me-1" href="/customer/index.php">Customers</a>
      <a class="btn btn-outline-light btn-sm me-1" href="/purchase/create.php">New Purchase</a>
      <a class="btn btn-outline-light btn-sm me-1" href="/sales/create.php">New Sale</a>
      <a class="btn btn-outline-light btn-sm" href="/stock/index.php">Stock</a>
    </div>
  </div>
</nav>
<div class="container"></div>
