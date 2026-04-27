<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$products  = $conn->query("SELECT COUNT(*) c FROM products WHERE status='active'")->fetch_assoc()['c'];
$vendors   = $conn->query("SELECT COUNT(*) c FROM vendors WHERE status='active'")->fetch_assoc()['c'];
$customers = $conn->query("SELECT COUNT(*) c FROM customers WHERE status='active'")->fetch_assoc()['c'];
$purchases = $conn->query("SELECT COUNT(*) c FROM purchases")->fetch_assoc()['c'];
$sales_cnt = $conn->query("SELECT COUNT(*) c FROM sales")->fetch_assoc()['c'];
$stock_val = $conn->query("SELECT COALESCE(SUM(quantity * purchase_price),0) v FROM purchase_items")->fetch_assoc()['v'];
?>

<div class="page-header">
    <div>
        <div class="page-title">Dashboard</div>
        <div class="page-subtitle">Overview — <?= date('l, d M Y') ?></div>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(0,229,176,0.1);color:var(--accent)"><i class="bi bi-box-seam"></i></div>
        <div class="stat-val"><?= $products ?></div>
        <div class="stat-lbl">Active Products</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:var(--accent2)"><i class="bi bi-truck"></i></div>
        <div class="stat-val"><?= $vendors ?></div>
        <div class="stat-lbl">Vendors</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b"><i class="bi bi-people"></i></div>
        <div class="stat-val"><?= $customers ?></div>
        <div class="stat-lbl">Customers</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(0,229,176,0.1);color:var(--accent)"><i class="bi bi-cart3"></i></div>
        <div class="stat-val"><?= $purchases ?></div>
        <div class="stat-lbl">Total Purchases</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(244,63,94,0.1);color:var(--danger)"><i class="bi bi-receipt"></i></div>
        <div class="stat-val"><?= $sales_cnt ?></div>
        <div class="stat-lbl">Total Sales</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);color:var(--accent2)"><i class="bi bi-cash-stack"></i></div>
        <div class="stat-val" style="font-size:20px">Rs. <?= number_format($stock_val,0) ?></div>
        <div class="stat-lbl">Inventory Value</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <div class="card">
        <div class="card-header-bar"><i class="bi bi-cart3" style="color:var(--accent)"></i> Recent Purchases</div>
        <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Vendor</th><th>Date</th><th>Items</th></tr></thead>
            <tbody>
            <?php
            $rp = $conn->query("SELECT p.id, v.name vendor, p.purchase_date, COUNT(pi.id) items FROM purchases p JOIN vendors v ON v.id=p.vendor_id LEFT JOIN purchase_items pi ON pi.purchase_id=p.id GROUP BY p.id ORDER BY p.id DESC LIMIT 5");
            if ($rp->num_rows === 0): ?>
                <tr><td colspan="4"><div class="empty-state"><i class="bi bi-inbox"></i><p>No purchases yet</p></div></td></tr>
            <?php endif;
            while ($r = $rp->fetch_assoc()): ?>
            <tr>
                <td class="td-id">#<?= $r['id'] ?></td>
                <td class="td-main"><?= htmlspecialchars($r['vendor']) ?></td>
                <td><?= $r['purchase_date'] ?></td>
                <td><?= $r['items'] ?> item(s)</td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header-bar"><i class="bi bi-receipt" style="color:var(--danger)"></i> Recent Sales</div>
        <div class="table-wrap">
        <table>
            <thead><tr><th>#</th><th>Customer</th><th>Date</th><th>Items</th></tr></thead>
            <tbody>
            <?php
            $rs = $conn->query("SELECT s.id, COALESCE(c.name,'Walk-in') cust, s.sale_date, COUNT(si.id) items FROM sales s LEFT JOIN customers c ON c.id=s.customer_id LEFT JOIN sale_items si ON si.sale_id=s.id GROUP BY s.id ORDER BY s.id DESC LIMIT 5");
            if ($rs->num_rows === 0): ?>
                <tr><td colspan="4"><div class="empty-state"><i class="bi bi-inbox"></i><p>No sales yet</p></div></td></tr>
            <?php endif;
            while ($r = $rs->fetch_assoc()): ?>
            <tr>
                <td class="td-id">#<?= $r['id'] ?></td>
                <td class="td-main"><?= htmlspecialchars($r['cust']) ?></td>
                <td><?= $r['sale_date'] ?></td>
                <td><?= $r['items'] ?> item(s)</td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
