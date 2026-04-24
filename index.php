<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GIS Crystal Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
        }

        .container {
            padding: 20px;
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: 0.3s;
            text-align: center;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            color: #777;
        }

        .card a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }

        .card a:hover {
            background: #45a049;
        }

        .navbar {
            background: #1e293b;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div><strong>GIS Crystal</strong></div>
    <div>
        <a href="modules/product/list.php">Products</a>
        <a href="modules/vendor/list.php">Vendors</a>
        <a href="modules/customer/list.php">Customers</a>
        <a href="modules/purchase/create.php">Purchase</a>
        <a href="modules/sales/create.php">Sales</a>
    </div>
</div>

<div class="container">

    <div class="dashboard-title">Dashboard</div>

    <div class="cards">

        <div class="card">
            <h3>Products</h3>
            <p>Manage all your products</p>
            <a href="modules/product/list.php">View</a>
        </div>

        <div class="card">
            <h3>Vendors</h3>
            <p>Manage vendors</p>
            <a href="modules/vendor/list.php">View</a>
        </div>

        <div class="card">
            <h3>Customers</h3>
            <p>Manage customers</p>
            <a href="modules/customer/list.php">View</a>
        </div>

        <div class="card">
            <h3>Purchase</h3>
            <p>Add stock from vendors</p>
            <a href="modules/purchase/create.php">Go</a>
        </div>

        <div class="card">
            <h3>Sales</h3>
            <p>Sell products</p>
            <a href="modules/sales/create.php">Go</a>
        </div>

    </div>

    <div class="footer">
        © 2026 GIS Crystal Inventory System
    </div>

</div>

</body>
</html>