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

</nav>
<div class="container"></div>
<style>
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    margin-top: 20px;
}
th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
th {
    background: #1e293b;
    color: white;
}
tr:hover {
    background: #f1f5f9;
}
.btn {
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}
.btn-edit { background: #3b82f6; color: white; }
.btn-view { background: #10b981; color: white; }
</style>
<div class="layout">
<aside class="sidebar">
    <div class="nav-section">Master Data</div>
    <a class="nav-link" href="/gis_crystal/product_group/index.php"><i class="bi bi-tags"></i>Product Groups</a>
    <a class="nav-link" href="/gis_crystal/unit_of_measure/index.php"><i class="bi bi-rulers"></i>Units of Measure</a>
    <a class="nav-link" href="/gis_crystal/product/index.php"><i class="bi bi-box-seam"></i>Products</a>
    <a class="nav-link" href="/gis_crystal/vendor/index.php"><i class="bi bi-truck"></i>Vendors</a>
    <a class="nav-link" href="/gis_crystal/customer/index.php"><i class="bi bi-people"></i>Customers</a>
    <div class="nav-section">Transactions</div>
    <a class="nav-link" href="/gis_crystal/purchase/create.php"><i class="bi bi-cart-plus"></i>New Purchase</a>
    <a class="nav-link" href="/gis_crystal/purchase/index.php"><i class="bi bi-cart3"></i>All Purchases</a>
    <a class="nav-link" href="/gis_crystal/sales/create.php"><i class="bi bi-plus-circle"></i>New Sale</a>
    <a class="nav-link" href="/gis_crystal/sales/index.php"><i class="bi bi-receipt"></i>All Sales</a>
    <div class="nav-section">Reports</div>
    <a class="nav-link" href="/gis_crystal/stock/index.php"><i class="bi bi-bar-chart-line"></i>Stock Overview</a>
</aside>