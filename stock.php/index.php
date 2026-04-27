<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$result = $conn->query("
    SELECT
        p.id,
        p.name product,
        u.code unit,
        COALESCE(SUM(pi.quantity), 0)                                                          AS purchased_qty,
        COALESCE(SUM(pi.quantity * pi.purchase_price), 0)                                      AS total_purchase_value,
        COALESCE(SUM(pi.quantity * pi.purchase_price) / NULLIF(SUM(pi.quantity), 0), 0)        AS avg_purchase_rate,
        COALESCE((SELECT SUM(si.quantity) FROM sale_items si WHERE si.product_id = p.id), 0)   AS sold_qty,
        COALESCE((SELECT SUM(si.quantity * si.sale_price) FROM sale_items si WHERE si.product_id = p.id), 0) AS total_sale_value
    FROM products p
    LEFT JOIN purchase_items pi ON pi.product_id = p.id
    LEFT JOIN units_of_measure u ON u.id = p.unit_id
    GROUP BY p.id, p.name, u.code
    ORDER BY p.name
");

$rows = [];
$total_stock_val = 0;
$total_sold_val  = 0;
while ($r = $result->fetch_assoc()) {
    $r['in_stock']   = $r['purchased_qty'] - $r['sold_qty'];
    $r['stock_value'] = $r['in_stock'] * $r['avg_purchase_rate'];
    $total_stock_val += $r['stock_value'];
    $total_sold_val  += $r['total_sale_value'];
    $rows[] = $r;
}
$total_purchase_val = array_sum(array_column($rows, 'total_purchase_value'));
?>

<div class="page-header">
  <div><div class="page-title">Stock Overview</div><div class="page-subtitle">Current inventory levels & valuation</div></div>
</div>

<!-- Summary Cards -->
<div class="stat-grid" style="margin-bottom:24px">
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(0,229,176,0.1);color:var(--accent)"><i class="bi bi-boxes"></i></div>
    <div class="stat-val"><?= count($rows) ?></div>
    <div class="stat-lbl">Total Products</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:var(--accent2)"><i class="bi bi-cash-stack"></i></div>
    <div class="stat-val" style="font-size:18px">Rs. <?= number_format($total_stock_val, 0) ?></div>
    <div class="stat-lbl">Current Stock Value</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(0,229,176,0.1);color:var(--accent)"><i class="bi bi-graph-up-arrow"></i></div>
    <div class="stat-val" style="font-size:18px">Rs. <?= number_format($total_sold_val, 0) ?></div>
    <div class="stat-lbl">Total Sales Revenue</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b"><i class="bi bi-calculator"></i></div>
    <div class="stat-val" style="font-size:18px">Rs. <?= number_format($total_purchase_val, 0) ?></div>
    <div class="stat-lbl">Total Purchase Value</div>
  </div>
</div>

<div class="card">
  <div class="card-header-bar">
    <i class="bi bi-bar-chart-line" style="color:var(--accent)"></i>Stock Details
    <span style="margin-left:auto;font-size:12px;color:var(--text3);font-family:'DM Mono',monospace">Avg rate = Total purchase amount ÷ Total quantity</span>
  </div>
  <div class="table-wrap"><table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Unit</th>
        <th>Purchased Qty</th>
        <th>Sold Qty</th>
        <th>In Stock</th>
        <th>Avg Purchase Rate</th>
        <th>Stock Value</th>
        <th>Sales Revenue</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($rows)): ?>
      <tr><td colspan="8"><div class="empty-state"><i class="bi bi-bar-chart"></i><p>No stock data yet. Start by recording purchases.</p></div></td></tr>
    <?php endif; ?>
    <?php foreach ($rows as $r):
        $low = $r['in_stock'] <= 0;
        $warn = $r['in_stock'] > 0 && $r['in_stock'] < 5;
    ?>
    <tr>
      <td class="td-main"><?= htmlspecialchars($r['product']) ?></td>
      <td><?= $r['unit'] ? '<span class="td-code">' . htmlspecialchars($r['unit']) . '</span>' : '—' ?></td>
      <td style="font-family:'DM Mono',monospace"><?= number_format($r['purchased_qty'], 2) ?></td>
      <td style="font-family:'DM Mono',monospace"><?= number_format($r['sold_qty'], 2) ?></td>
      <td>
        <?php if ($low): ?>
          <span style="font-family:'DM Mono',monospace;color:var(--danger);font-weight:600"><?= number_format($r['in_stock'], 2) ?> <span style="font-size:11px;background:rgba(244,63,94,0.1);padding:2px 6px;border-radius:4px">OUT</span></span>
        <?php elseif ($warn): ?>
          <span style="font-family:'DM Mono',monospace;color:var(--warn);font-weight:600"><?= number_format($r['in_stock'], 2) ?> <span style="font-size:11px;background:rgba(245,158,11,0.1);padding:2px 6px;border-radius:4px">LOW</span></span>
        <?php else: ?>
          <span style="font-family:'DM Mono',monospace;color:var(--accent);font-weight:600"><?= number_format($r['in_stock'], 2) ?></span>
        <?php endif; ?>
      </td>
      <td style="font-family:'DM Mono',monospace">৳ <?= number_format($r['avg_purchase_rate'], 2) ?></td>
      <td style="font-family:'DM Mono',monospace;color:var(--accent)">Rs. <?= number_format($r['stock_value'], 2) ?></td>
      <td style="font-family:'DM Mono',monospace;color:var(--accent2)">Rs. <?= number_format($r['total_sale_value'], 2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <?php if (!empty($rows)): ?>
    <tfoot>
      <tr style="border-top:2px solid var(--border2)">
        <td colspan="4" style="padding:12px 16px;text-align:right;color:var(--text3);font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:1px">Totals</td>
        <td></td>
        <td></td>
        <td style="padding:12px 16px;font-family:'DM Mono',monospace;font-weight:700;color:var(--accent)">Rs. <?= number_format($total_stock_val, 2) ?></td>
        <td style="padding:12px 16px;font-family:'DM Mono',monospace;font-weight:700;color:var(--accent2)">Rs. <?= number_format($total_sold_val, 2) ?></td>
      </tr>
    </tfoot>
    <?php endif; ?>
  </table></div>
</div>

<div style="margin-top:16px;padding:14px 18px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);font-size:12.5px;color:var(--text3);display:flex;gap:24px;flex-wrap:wrap">
  <span><span style="color:var(--accent)">●</span> In Stock — sufficient quantity available</span>
  <span><span style="color:var(--warn)">●</span> Low — stock below 5 units</span>
  <span><span style="color:var(--danger)">●</span> Out — stock is zero or negative</span>
  <span style="margin-left:auto;font-family:'DM Mono',monospace">Purchases increase stock · Sales reduce stock</span>
</div>

<?php require_once '../includes/footer.php'; ?>
