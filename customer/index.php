<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$result = $conn->query("SELECT * FROM customers ORDER BY id DESC");
?>
<div class="page-header">
  <div><div class="page-title">Customer\s</div><div class="page-subtitle">All records</div></div>
  <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i>Add New</a>
</div>
<div class="card">
  <div class="table-wrap"><table>
    <thead><tr><th>ID</th><th>Name</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php if ($result->num_rows === 0): ?>
      <tr><td colspan="5"><div class="empty-state"><i class="bi bi-inbox"></i><p>No records found. <a href="create.php" style="color:var(--accent)">Add one →</a></p></div></td></tr>
    <?php endif; while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td class="td-id"><?= $row['id'] ?></td>
        <td class="td-main"><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['description'] ?? '') ?></td>
        <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
        <td><a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-ghost btn-sm"><i class="bi bi-pencil"></i>Edit</a></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table></div>
</div>
<?php require_once '../includes/footer.php'; ?>
