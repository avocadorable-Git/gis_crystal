<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$errors=[];
$vendor_id=0;$purchase_date=date('Y-m-d');

$vendors=$conn->query("SELECT id,name FROM vendors WHERE status='active' ORDER BY name");
$products_q=$conn->query("SELECT p.id,p.name,u.code unit FROM products p LEFT JOIN units_of_measure u ON u.id=p.unit_id WHERE p.status='active' ORDER BY p.name");
$prod_arr=[];while($r=$products_q->fetch_assoc())$prod_arr[]=$r;

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $vendor_id=intval($_POST['vendor_id']??0);
    $purchase_date=trim($_POST['purchase_date']??'');
    $items=$_POST['items']??[];

    // Server-side validation
    if ($vendor_id<=0)       $errors[]="Please select a vendor.";
    if (empty($purchase_date)||!strtotime($purchase_date)) $errors[]="Valid purchase date is required.";
    if (empty($items))       $errors[]="Add at least one product line.";
    foreach ($items as $i=>$item) {
        $r=$i+1;
        if (empty($item['product_id']))                                   $errors[]="Row $r: Select a product.";
        if (!isset($item['qty'])||!is_numeric($item['qty'])||$item['qty']<=0) $errors[]="Row $r: Valid quantity required.";
        if (!isset($item['purchase_price'])||!is_numeric($item['purchase_price'])||$item['purchase_price']<=0) $errors[]="Row $r: Valid purchase price required.";
        if (!isset($item['sale_price'])||!is_numeric($item['sale_price'])||$item['sale_price']<=0) $errors[]="Row $r: Valid sale price required.";
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            $stmt=$conn->prepare("INSERT INTO purchases (vendor_id,purchase_date) VALUES (?,?)");
            $stmt->bind_param("is",$vendor_id,$purchase_date);
            $stmt->execute();$pid=$conn->insert_id;$stmt->close();
            $stmt2=$conn->prepare("INSERT INTO purchase_items (purchase_id,product_id,quantity,purchase_price,sale_price) VALUES (?,?,?,?,?)");
            foreach ($items as $item) {
                $prod=intval($item['product_id']);$qty=floatval($item['qty']);
                $pp=floatval($item['purchase_price']);$sp=floatval($item['sale_price']);
                $stmt2->bind_param("iiddd",$pid,$prod,$qty,$pp,$sp);$stmt2->execute();
            }
            $stmt2->close();
            $conn->commit();
            $_SESSION['success']="Purchase #$pid recorded successfully.";
            header("Location: show.php?id=$pid");exit;
        } catch (Exception $e) {
            $conn->rollback();$errors[]="Transaction failed: ".$e->getMessage();
        }
    }
}
?>
<div class="page-header">
  <div><div class="page-title">New Purchase</div><div class="page-subtitle">Record a stock purchase from a vendor</div></div>
  <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i>Back</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><div><ul style="margin:0 0 0 14px"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div></div>
<?php endif; ?>

<form method="POST" id="purchaseForm" class="needs-validation" novalidate>
<div class="card" style="margin-bottom:20px"><div class="card-body">
  <div class="form-grid">
    <div class="form-group"><label class="form-label">Vendor <span class="req">*</span></label>
      <select name="vendor_id" class="form-select" required>
        <option value="">— Select Vendor —</option>
        <?php while ($v=$vendors->fetch_assoc()): ?>
          <option value="<?= $v['id'] ?>" <?= $vendor_id==$v['id']?'selected':'' ?>><?= htmlspecialchars($v['name']) ?></option>
        <?php endwhile; ?>
      </select><div class="invalid-feedback">Select a vendor.</div></div>
    <div class="form-group"><label class="form-label">Purchase Date <span class="req">*</span></label>
      <input type="date" name="purchase_date" class="form-control" value="<?= htmlspecialchars($purchase_date) ?>" required>
      <div class="invalid-feedback">Date required.</div></div>
  </div>
</div></div>

<div class="card" style="margin-bottom:20px">
  <div class="card-header-bar"><i class="bi bi-list-ul" style="color:var(--accent)"></i>Purchase Items</div>
  <div class="items-table">
    <table id="itemsTable">
      <thead><tr><th>Product</th><th>Qty</th><th>Purchase Price (৳)</th><th>Sale Price (৳)</th><th></th></tr></thead>
      <tbody id="itemBody"></tbody>
    </table>
  </div>
  <div style="padding:12px 16px">
    <button type="button" class="add-row-btn" onclick="addRow()"><i class="bi bi-plus-lg"></i>Add Product Row</button>
  </div>
</div>

<div style="display:flex;align-items:center;justify-content:space-between">
  <div style="color:var(--text3);font-size:13px">Purchase entry cannot be edited after saving.</div>
  <button type="submit" class="btn btn-primary" style="padding:11px 28px">
    <i class="bi bi-check-lg"></i>Save Purchase
  </button>
</div>
</form>

<script>
const products = <?= json_encode($prod_arr) ?>;
let idx = 0;

function productOptions(sel='') {
    return products.map(p=>`<option value="${p.id}" ${sel==p.id?'selected':''}>${p.name}${p.unit?' ('+p.unit+')':''}</option>`).join('');
}

function addRow(pid='',qty='',pp='',sp='') {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><select name="items[${idx}][product_id]" class="form-select" required>
            <option value="">— Product —</option>${productOptions(pid)}</select></td>
        <td><input type="number" name="items[${idx}][qty]" class="form-control" min="0.01" step="0.01" placeholder="0.00" value="${qty}" required></td>
        <td><input type="number" name="items[${idx}][purchase_price]" class="form-control" min="0.01" step="0.01" placeholder="0.00" value="${pp}" required></td>
        <td><input type="number" name="items[${idx}][sale_price]" class="form-control" min="0.01" step="0.01" placeholder="0.00" value="${sp}" required></td>
        <td><button type="button" class="remove-row-btn" onclick="this.closest('tr').remove()"><i class="bi bi-x"></i></button></td>`;
    document.getElementById('itemBody').appendChild(tr);
    idx++;
}

// Start with 2 rows
addRow(); addRow();
</script>

<?php require_once '../includes/footer.php'; ?>
