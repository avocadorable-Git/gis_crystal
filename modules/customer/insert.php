<?php
require_once '../../config/db.php';
require_once '../../includes/functions.php';

$name = sanitize($_POST['name']);
$price = $_POST['price'];

if (!validateNumber($price)) die("Invalid");

$stmt = $conn->prepare("INSERT INTO products(name,price) VALUES(?,?)");
$stmt->execute([$name,$price]);

header("Location:list.php");