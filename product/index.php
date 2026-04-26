<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$result=$conn->query("SELECT p.*,u.name unit_name,u.code unit_code FROM products p LEFT JOIN units_of_measure u ON u.id=p.unit_id ORDER BY p.id DESC");
?>
<div class="page-header">
  <div><div class="page-title">Products</div><div class="page-subtitle">Manage your product catalog</div></div>
  <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i>Add New</a>
</div>
<div class="card"><div class="table-wrap"><table>
  <thead><tr><th>ID</th><th>Name</th><th>Unit</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
  <?php if ($result->num_rows===0): ?>
    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-box-seam"></i><p>No products yet. <a href="create.php" style="color:var(--accent)">Add one →</a></p></div></td></tr>
  <?php endif; while ($row=$result->fetch_assoc()): ?>
  <tr>
    <td class="td-id"><?= $row['id'] ?></td>
    <td class="td-main"><?= htmlspecialchars($row['name']) ?></td>
    <td><?php if($row['unit_code']): ?><span class="td-code"><?= htmlspecialchars($row['unit_code']) ?></span> <span style="color:var(--text3);font-size:12px"><?= htmlspecialchars($row['unit_name']) ?></span><?php else: ?>—<?php endif; ?></td>
    <td><?= htmlspecialchars($row['description']??'') ?></td>
    <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
    <td><a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-ghost btn-sm"><i class="bi bi-pencil"></i>Edit</a></td>
  </tr>
  <?php endwhile; ?>
  </tbody>
</table></div></div>
<?php require_once '../includes/footer.php'; ?>
