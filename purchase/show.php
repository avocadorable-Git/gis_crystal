<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$id=intval($_GET['id']??0);
if ($id<=0){header("Location: index.php");exit;}

$stmt=$conn->prepare("SELECT p.*,v.name vendor FROM purchases p JOIN vendors v ON v.id=p.vendor_id WHERE p.id=?");
$stmt->bind_param("i",$id);$stmt->execute();
$purchase=$stmt->get_result()->fetch_assoc();$stmt->close();
if (!$purchase){header("Location: index.php");exit;}

$stmt=$conn->prepare("SELECT pi.*,pr.name product,u.code unit FROM purchase_items pi JOIN products pr ON pr.id=pi.product_id LEFT JOIN units_of_measure u ON u.id=pr.unit_id WHERE pi.purchase_id=?");
$stmt->bind_param("i",$id);$stmt->execute();
$items=$stmt->get_result();$stmt->close();

$total_val=0;$item_rows=[];
while($r=$items->fetch_assoc()){$r['line_total']=$r['quantity']*$r['purchase_price'];$total_val+=$r['line_total'];$item_rows[]=$r;}
?>
<div class="page-header">
  <div><div class="page-title">Purchase #<?= $id ?></div><div class="page-subtitle"><?= $purchase['purchase_date'] ?> — <?= htmlspecialchars($purchase['vendor']) ?></div></div>
  <div style="display:flex;gap:10px">
    <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i>All Purchases</a>
    <a href="create.php" class="btn btn-primary"><i class="bi bi-cart-plus"></i>New Purchase</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px">
  <div class="card" style="align-self:start">
    <div class="card-header-bar"><i class="bi bi-info-circle" style="color:var(--accent2)"></i>Purchase Info</div>
    <table class="detail-table">
      <tr><td class="dl">Purchase #</td><td class="dv" style="font-family:'DM Mono',monospace">#<?= $id ?></td></tr>
      <tr><td class="dl">Vendor</td><td class="dv td-main"><?= htmlspecialchars($purchase['vendor']) ?></td></tr>
      <tr><td class="dl">Date</td><td class="dv"><?= $purchase['purchase_date'] ?></td></tr>
      <tr><td class="dl">Created</td><td class="dv" style="color:var(--text3);font-size:12px"><?= $purchase['created_at'] ?></td></tr>
      <tr><td class="dl">Total Value</td><td class="dv" style="color:var(--accent);font-family:'DM Mono',monospace;font-weight:600">Rs. <?= number_format($total_val,2) ?></td></tr>
    </table>
  </div>
  <div class="card">
    <div class="card-header-bar"><i class="bi bi-list-ul" style="color:var(--accent)"></i>Items (<?= count($item_rows) ?>)</div>
    <div class="table-wrap"><table>
      <thead><tr><th>Product</th><th>Unit</th><th>Qty</th><th>Purchase Price</th><th>Sale Price</th><th>Line Total</th></tr></thead>
      <tbody>
      <?php foreach ($item_rows as $r): ?>
      <tr>
        <td class="td-main"><?= htmlspecialchars($r['product']) ?></td>
        <td><?= $r['unit']?'<span class="td-code">'.htmlspecialchars($r['unit']).'</span>':'—' ?></td>
        <td style="font-family:'DM Mono',monospace"><?= number_format($r['quantity'],2) ?></td>
        <td style="font-family:'DM Mono',monospace">Rs. <?= number_format($r['purchase_price'],2) ?></td>
        <td style="font-family:'DM Mono',monospace;color:var(--accent2)">Rs. <?= number_format($r['sale_price'],2) ?></td>
        <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--accent)">Rs. <?= number_format($r['line_total'],2) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr style="border-top:2px solid var(--border2)">
          <td colspan="5" style="padding:12px 16px;text-align:right;color:var(--text2);font-size:12px;font-family:'DM Mono',monospace;text-transform:uppercase;letter-spacing:1px">Total Purchase Value</td>
          <td style="padding:12px 16px;font-family:'DM Mono',monospace;font-weight:700;color:var(--accent);font-size:16px">Rs. <?= number_format($total_val,2) ?></td>
        </tr>
      </tfoot>
    </table></div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
