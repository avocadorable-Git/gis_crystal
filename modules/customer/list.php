<?php
require_once '../../config/db.php';
include '../../includes/header.php';

$stmt = $conn->query("SELECT * FROM customers");
$customers = $stmt->fetchAll();
?>

<h3>Customer List</h3>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
</tr>

<?php foreach ($customers as $c): ?>
<tr>
    <td><?= $c['id'] ?></td>
    <td><?= $c['name'] ?></td>
</tr>
<?php endforeach; ?>

</table>

<?php include '../../includes/footer.php'; ?>