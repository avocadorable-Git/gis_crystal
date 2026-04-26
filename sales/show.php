<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: index.php"); exit; }

$stmt = $conn->prepare("SELECT s.*, COALESCE(c.name,'Walk-in Customer') cust FROM sales s LEFT JOIN customers c ON c.id=s.customer_id WHERE s.id=?");
$stmt->bind_param("i", $id); $stmt->execute();
$sale = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$sale) { header("Location: index.php"); exit; }

$stmt = $conn->prepare("SELECT si.*, p.name product, u.code unit FROM sale_items si JOIN products p ON p.id=si.product_id LEFT JOIN units_of_measure u ON u.id=p.unit_id WHERE si.sale_id=?");
$stmt->bind_param("i", $id); $stmt->execute();
$items_q = $stmt->get_result(); $stmt->close();

$grand = 0; $item_rows = [];
while ($r = $items_q->fetch_assoc()) {
    $r['line_total'] = $r['quantity'] * $r['sale_price'];
    $grand += $r['line_total'];
    $item_rows[] = $r;
}
?>
<div class="page-header">
  <div>
    <div class="page-title">Sale #<?= $id ?></div>
    <div class="page-subtitle"><?= $sale['sale_date'] ?> — <?= htmlspecialchars($sale['cust']) ?></div>
  </div>
  <div style="display:flex;gap:10px">
    <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i>All Sales</a>
    <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i>New Sale</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px">
  <div class="card" style="align-self:start">
    <div class="card-header-bar"><i class="bi bi-info-circle" style="color:var(--accent2)"></i>Invoice Info</div>
    <table class="detail-table">
      <tr><td class="dl">Sale #</td><td class="dv" style="font-family:'DM Mono',monospace">#<?= $id ?></td></tr>
      <tr><td class="dl">Customer</td><td class="dv td-main"><?= htmlspecialchars($sale['cust']) ?></td></tr>
      <tr><td class="dl">Sale Date</td><td class="dv"><?= $sale['sale_date'] ?></td></tr>
      <tr><td class="dl">Created At</td><td class="dv" style="color:var(--text3);font-size:12px"><?= $sale['created_at'] ?></td></tr>
      <tr>
        <td class="dl">Grand Total</td>
        <td class="dv" style="color:var(--accent2);font-family:'DM Mono',monospace;font-weight:700;font-size:16px">Rs. <?= number_format($grand, 2) ?></td>
      </tr>
    </table>
  </div>

  <div class="card">
    <div class="card-header-bar"><i class="bi bi-receipt" style="color:var(--accent2)"></i>Items Sold (<?= count($item_rows) ?>)</div>
    <div class="table-wrap"><table>
      <thead><tr><th>Product</th><th>Unit</th><th>Qty</th><th>Sale Price</th><th>Line Total</th></tr></thead>
      <tbody>
      <?php foreach ($item_rows as $r): ?>
      <tr>
        <td class="td-main"><?= htmlspecialchars($r['product']) ?></td>
        <td><?= $r['unit'] ? '<span class="td-code">' . htmlspecialchars($r['unit']) . '</span>' : '—' ?></td>
        <td style="font-family:'DM Mono',monospace"><?= number_format($r['quantity'], 2) ?></td>
        <td style="font-family:'DM Mono',monospace">Rs. <?= number_format($r['sale_price'], 2) ?></td>
        <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--accent2)">Rs. <?= number_format($r['line_total'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr style="border-top:2px solid var(--border2)">
          <td colspan="4" style="padding:12px 16px;text-align:right;color:var(--text2);font-size:12px;font-family:'DM Mono',monospace;text-transform:uppercase;letter-spacing:1px">Grand Total</td>
          <td style="padding:12px 16px;font-family:'DM Mono',monospace;font-weight:700;color:var(--accent2);font-size:16px">Rs. <?= number_format($grand, 2) ?></td>
        </tr>
      </tfoot>
    </table></div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
