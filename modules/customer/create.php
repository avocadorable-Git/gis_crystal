<?php include '../../includes/header.php'; ?>
<form method="POST" action="insert.php">
Name: <input type="text" name="name" required>
Price: <input type="number" name="price" required>
<button type="submit">Save</button>
</form>
<?php include '../../includes/footer.php'; ?>