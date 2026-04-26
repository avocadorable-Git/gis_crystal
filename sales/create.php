<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$errors = [];
$customer_id = 0;
$sale_date   = date('Y-m-d');

$customers_q = $conn->query("SELECT id, name FROM customers WHERE status='active' ORDER BY name");
$products_q  = $conn->query("SELECT p.id, p.name, u.code unit FROM products p LEFT JOIN units_of_measure u ON u.id=p.unit_id WHERE p.status='active' ORDER BY p.name");
$prod_arr = [];
while ($r = $products_q->fetch_assoc()) $prod_arr[] = $r;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = intval($_POST['customer_id'] ?? 0); // 0 = walk-in, allowed
    $sale_date   = trim($_POST['sale_date'] ?? '');
    $items       = $_POST['items'] ?? [];

    // Server-side validation
    if (empty($sale_date) || !strtotime($sale_date)) $errors[] = "Valid sale date is required.";
    if (empty($items))                                $errors[] = "Add at least one product line.";

    foreach ($items as $i => $item) {
        $r = $i + 1;
        if (empty($item['product_id']))                                           $errors[] = "Row $r: Select a product.";
        if (!isset($item['qty']) || !is_numeric($item['qty']) || $item['qty'] <= 0) $errors[] = "Row $r: Valid quantity required.";
        if (!isset($item['sale_price']) || !is_numeric($item['sale_price']) || $item['sale_price'] <= 0) $errors[] = "Row $r: Valid sale price required.";
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            $cid = $customer_id > 0 ? $customer_id : null;
            $stmt = $conn->prepare("INSERT INTO sales (customer_id, sale_date) VALUES (?, ?)");
            $stmt->bind_param("is", $cid, $sale_date);
            $stmt->execute();
            $sid = $conn->insert_id;
            $stmt->close();

            $stmt2 = $conn->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, sale_price) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $prod = intval($item['product_id']);
                $qty  = floatval($item['qty']);
                $sp   = floatval($item['sale_price']);
                $stmt2->bind_param("iidd", $sid, $prod, $qty, $sp);
                $stmt2->execute();
            }
            $stmt2->close();
            $conn->commit();
            $_SESSION['success'] = "Sale #$sid recorded successfully.";
            header("Location: show.php?id=$sid");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Transaction failed: " . $e->getMessage();
        }
    }
}
?>
<div class="page-header">
  <div><div class="page-title">New Sale</div><div class="page-subtitle">Create a new sales invoice</div></div>
  <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i>Back</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
  <i class="bi bi-exclamation-triangle-fill"></i>
  <div><ul style="margin:0 0 0 14px"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
</div>
<?php endif; ?>

<form method="POST" id="salesForm" class="needs-validation" novalidate>

  <div class="card" style="margin-bottom:20px"><div class="card-body">
    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Customer <span style="color:var(--text3);font-weight:400">(optional — leave blank for walk-in)</span></label>
        <select name="customer_id" class="form-select">
          <option value="0">— Walk-in Customer —</option>
          <?php while ($c = $customers_q->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>" <?= $customer_id == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Sale Date <span class="req">*</span></label>
        <input type="date" name="sale_date" class="form-control" value="<?= htmlspecialchars($sale_date) ?>" required>
        <div class="invalid-feedback">Date is required.</div>
      </div>
    </div>
  </div></div>

  <div class="card" style="margin-bottom:20px">
    <div class="card-header-bar"><i class="bi bi-list-ul" style="color:var(--accent2)"></i>Sale Items
      <span style="margin-left:auto;font-size:12px;color:var(--text3);font-family:'DM Mono',monospace">You can override the sale price per row</span>
    </div>
    <div class="items-table">
      <table id="itemsTable">
        <thead>
          <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Sale Price (৳)</th>
            <th>Line Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="itemBody"></tbody>
      </table>
    </div>
    <div style="padding:12px 16px;display:flex;align-items:center;justify-content:space-between">
      <button type="button" class="add-row-btn" onclick="addRow()"><i class="bi bi-plus-lg"></i>Add Product Row</button>
      <div style="font-family:'DM Mono',monospace;font-size:14px;color:var(--text2)">
        Grand Total: <span id="grandTotal" style="color:var(--accent2);font-weight:600">৳ 0.00</span>
      </div>
    </div>
  </div>

  <div style="display:flex;align-items:center;justify-content:space-between">
    <div style="color:var(--text3);font-size:13px">Sale entry cannot be edited after saving.</div>
    <button type="submit" class="btn btn-primary" style="padding:11px 28px">
      <i class="bi bi-check-lg"></i>Save Sale
    </button>
  </div>
</form>

<script>
const products = <?= json_encode($prod_arr) ?>;
let idx = 0;

function productOptions(sel = '') {
    return products.map(p =>
        `<option value="${p.id}" ${sel == p.id ? 'selected' : ''}>${p.name}${p.unit ? ' (' + p.unit + ')' : ''}</option>`
    ).join('');
}

function calcRow(tr) {
    const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
    const sp  = parseFloat(tr.querySelector('.sp-input').value)  || 0;
    const tot = qty * sp;
    tr.querySelector('.line-total').textContent = '৳ ' + tot.toFixed(2);
    calcGrand();
}

function calcGrand() {
    let total = 0;
    document.querySelectorAll('.line-total').forEach(el => {
        total += parseFloat(el.textContent.replace('৳ ', '')) || 0;
    });
    document.getElementById('grandTotal').textContent = '৳ ' + total.toFixed(2);
}

function addRow(pid = '', qty = '', sp = '') {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
          <select name="items[${idx}][product_id]" class="form-select" required>
            <option value="">— Product —</option>${productOptions(pid)}
          </select>
        </td>
        <td><input type="number" name="items[${idx}][qty]" class="form-control qty-input" min="0.01" step="0.01" placeholder="0.00" value="${qty}" required></td>
        <td><input type="number" name="items[${idx}][sale_price]" class="form-control sp-input" min="0.01" step="0.01" placeholder="0.00" value="${sp}" required></td>
        <td class="line-total" style="font-family:'DM Mono',monospace;color:var(--accent2)">৳ 0.00</td>
        <td><button type="button" class="remove-row-btn" onclick="this.closest('tr').remove();calcGrand()"><i class="bi bi-x"></i></button></td>`;
    const tbody = document.getElementById('itemBody');
    tbody.appendChild(tr);
    tr.querySelector('.qty-input').addEventListener('input', () => calcRow(tr));
    tr.querySelector('.sp-input').addEventListener('input',  () => calcRow(tr));
    idx++;
}

addRow();
addRow();
</script>

<?php require_once '../includes/footer.php'; ?>
