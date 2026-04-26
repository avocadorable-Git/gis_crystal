<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$result=$conn->query("
    SELECT s.id, COALESCE(c.name,'Walk-in Customer') cust, s.sale_date,
           COUNT(si.id) items, SUM(si.quantity*si.sale_price) total
    FROM sales s
    LEFT JOIN customers c ON c.id=s.customer_id
    LEFT JOIN sale_items si ON si.sale_id=s.id
    GROUP BY s.id ORDER BY s.id DESC
");
?>
<div class="page-header">
  <div><div class="page-title">Sales</div><div class="page-subtitle">All sale invoices</div></div>
  <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i>New Sale</a>
</div>
<div class="card"><div class="table-wrap"><table>
  <thead><tr><th>ID</th><th>Customer</th><th>Date</th><th>Items</th><th>Total</th><th>Actions</th></tr></thead>
  <tbody>
  <?php if ($result->num_rows===0): ?>
    <tr><td colspan="6"><div class="empty-state"><i class="bi bi-receipt"></i><p>No sales yet. <a href="create.php" style="color:var(--accent)">Record one →</a></p></div></td></tr>
  <?php endif; while ($row=$result->fetch_assoc()): ?>
  <tr>
    <td class="td-id">#<?= $row['id'] ?></td>
    <td class="td-main"><?= htmlspecialchars($row['cust']) ?></td>
    <td><?= $row['sale_date'] ?></td>
    <td><?= $row['items'] ?> item(s)</td>
    <td style="color:var(--accent2);font-family:'DM Mono',monospace">Rs. <?= number_format($row['total'],2) ?></td>
    <td><a href="show.php?id=<?= $row['id'] ?>" class="btn btn-ghost btn-sm"><i class="bi bi-eye"></i>View</a></td>
  </tr>
  <?php endwhile; ?>
  </tbody>
</table></div></div>
<?php require_once '../includes/footer.php'; ?>
