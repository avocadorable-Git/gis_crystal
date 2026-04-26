<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$result=$conn->query("
    SELECT p.id, v.name vendor, p.purchase_date, COUNT(pi.id) items, SUM(pi.quantity*pi.purchase_price) total
    FROM purchases p
    JOIN vendors v ON v.id=p.vendor_id
    LEFT JOIN purchase_items pi ON pi.purchase_id=p.id
    GROUP BY p.id ORDER BY p.id DESC
");
?>
<div class="page-header">
  <div><div class="page-title">Purchases</div><div class="page-subtitle">All purchase records</div></div>
  <a href="create.php" class="btn btn-primary"><i class="bi bi-cart-plus"></i>New Purchase</a>
</div>
<div class="card"><div class="table-wrap"><table>
  <thead><tr><th>ID</th><th>Vendor</th><th>Date</th><th>Items</th><th>Total Value</th><th>Actions</th></tr></thead>
  <tbody>
  <?php if ($result->num_rows===0): ?>
    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-cart3"></i><p>No purchases yet. <a href="create.php" style="color:var(--accent)">Record one →</a></p></div></td></tr>
  <?php endif; while ($row=$result->fetch_assoc()): ?>
  <tr>
    <td class="td-id">#<?= $row['id'] ?></td>
    <td class="td-main"><?= htmlspecialchars($row['vendor']) ?></td>
    <td><?= $row['purchase_date'] ?></td>
    <td><?= $row['items'] ?> item(s)</td>
    <td style="color:var(--accent);font-family:'DM Mono',monospace">Rs. <?= number_format($row['total'],2) ?></td>
    <td><a href="show.php?id=<?= $row['id'] ?>" class="btn btn-ghost btn-sm"><i class="bi bi-eye"></i>View</a></td>
  </tr>
  <?php endwhile; ?>
  </tbody>
</table></div></div>
<?php require_once '../includes/footer.php'; ?>
