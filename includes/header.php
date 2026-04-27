<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIS Crystal — Inventory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg:       #0b0d12;
            --surface:  #11141c;
            --surface2: #181c27;
            --border:   #1f2333;
            --border2:  #2a2f45;
            --accent:   #00e5b0;
            --accent2:  #3b82f6;
            --danger:   #f43f5e;
            --warn:     #f59e0b;
            --text:     #e2e5ef;
            --text2:    #8892aa;
            --text3:    #4a5068;
            --sidebar-w:236px;
            --nav-h:    58px;
            --radius:   10px;
            --radius-sm:6px;
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{background:var(--bg);color:var(--text);font-family:'DM Sans',sans-serif;font-size:14px;min-height:100vh}

        /* TOPBAR */
        .topbar{position:fixed;top:0;left:0;right:0;z-index:200;height:var(--nav-h);background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:16px}
        .brand{font-family:'Syne',sans-serif;font-weight:800;font-size:17px;color:var(--accent);text-decoration:none;letter-spacing:-0.5px;display:flex;align-items:center;gap:10px}
        .brand-dot{width:8px;height:8px;border-radius:50%;background:var(--accent);box-shadow:0 0 12px var(--accent);animation:glow 2s ease-in-out infinite}
        @keyframes glow{0%,100%{box-shadow:0 0 12px var(--accent)}50%{box-shadow:0 0 4px var(--accent)}}
        .topbar-spacer{flex:1}
        .topbar-chip{font-family:'DM Mono',monospace;font-size:11px;color:var(--text3);background:var(--bg);border:1px solid var(--border2);padding:4px 12px;border-radius:20px}

        /* LAYOUT */
        .layout{display:flex;padding-top:var(--nav-h);min-height:100vh}

        /* SIDEBAR */
        .sidebar{width:var(--sidebar-w);min-width:var(--sidebar-w);background:var(--surface);border-right:1px solid var(--border);position:fixed;top:var(--nav-h);bottom:0;overflow-y:auto;padding:16px 10px 32px}
        .sidebar::-webkit-scrollbar{width:3px}
        .sidebar::-webkit-scrollbar-thumb{background:var(--border2);border-radius:4px}
        .nav-section{font-family:'DM Mono',monospace;font-size:9.5px;font-weight:500;color:var(--text3);text-transform:uppercase;letter-spacing:1.8px;padding:18px 10px 6px}
        .nav-link{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:var(--radius-sm);color:var(--text2);text-decoration:none;font-size:13.5px;transition:all 0.15s;margin-bottom:1px}
        .nav-link i{font-size:14px;width:16px;text-align:center;flex-shrink:0}
        .nav-link:hover{background:var(--surface2);color:var(--text)}
        .nav-link.active{background:rgba(0,229,176,0.08);color:var(--accent);font-weight:500}
        .nav-link.active i{color:var(--accent)}

        /* MAIN */
        .main{margin-left:var(--sidebar-w);flex:1;padding:32px;min-height:calc(100vh - var(--nav-h))}

        /* PAGE HEADER */
        .page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:16px}
        .page-title{font-family:'Syne',sans-serif;font-size:21px;font-weight:700;color:var(--text)}
        .page-subtitle{color:var(--text3);font-size:12.5px;margin-top:3px;font-family:'DM Mono',monospace}

        /* CARDS */
        .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
        .card-body{padding:24px}
        .card-header-bar{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;font-weight:600;font-size:13.5px}

        /* STAT CARDS */
        .stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;margin-bottom:28px}
        .stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;transition:border-color .2s,transform .2s;cursor:default}
        .stat-card:hover{border-color:var(--border2);transform:translateY(-2px)}
        .stat-icon{width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:16px}
        .stat-val{font-family:'Syne',sans-serif;font-size:30px;font-weight:700;line-height:1;color:var(--text)}
        .stat-lbl{color:var(--text3);font-size:11.5px;margin-top:5px;font-family:'DM Mono',monospace;text-transform:uppercase;letter-spacing:.8px}

        /* TABLES */
        .table-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse}
        thead th{font-family:'DM Mono',monospace;font-size:10.5px;text-transform:uppercase;letter-spacing:1px;color:var(--text3);font-weight:500;padding:11px 16px;border-bottom:1px solid var(--border);white-space:nowrap;background:var(--bg)}
        tbody tr{border-bottom:1px solid var(--border);transition:background .1s}
        tbody tr:last-child{border-bottom:none}
        tbody tr:hover{background:rgba(255,255,255,0.02)}
        tbody td{padding:12px 16px;color:var(--text2);vertical-align:middle}
        .td-id{color:var(--text3)!important;font-family:'DM Mono',monospace;font-size:12px}
        .td-main{color:var(--text)!important;font-weight:500}
        .td-code{font-family:'DM Mono',monospace;font-size:12px;color:var(--accent2)!important;background:rgba(59,130,246,0.08);padding:2px 8px;border-radius:4px;display:inline-block}

        /* BADGES */
        .badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:10.5px;font-weight:500;font-family:'DM Mono',monospace}
        .badge::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0}
        .badge-active{background:rgba(0,229,176,0.1);color:var(--accent)}
        .badge-active::before{background:var(--accent);box-shadow:0 0 6px var(--accent)}
        .badge-inactive{background:rgba(244,63,94,0.1);color:var(--danger)}
        .badge-inactive::before{background:var(--danger)}

        /* BUTTONS */
        .btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:var(--radius-sm);font-size:13px;font-weight:500;border:none;cursor:pointer;text-decoration:none;transition:all .15s;font-family:'DM Sans',sans-serif;white-space:nowrap}
        .btn-primary{background:var(--accent);color:#000}
        .btn-primary:hover{background:#00cfA0;color:#000;transform:translateY(-1px);box-shadow:0 4px 16px rgba(0,229,176,0.25)}
        .btn-outline{background:transparent;border:1px solid var(--border2);color:var(--text2)}
        .btn-outline:hover{border-color:var(--text2);color:var(--text);background:var(--surface2)}
        .btn-ghost{background:var(--surface2);color:var(--text2)}
        .btn-ghost:hover{color:var(--text)}
        .btn-danger-soft{background:rgba(244,63,94,0.1);color:var(--danger);border:1px solid rgba(244,63,94,0.2)}
        .btn-danger-soft:hover{background:rgba(244,63,94,0.2)}
        .btn-sm{padding:5px 12px;font-size:12px}

        /* FORMS */
        .form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px}
        .form-grid.cols-3{grid-template-columns:repeat(3,1fr)}
        .form-full{grid-column:1/-1}
        .form-group{margin-bottom:20px}
        .form-label{display:block;margin-bottom:7px;font-size:12px;font-weight:500;color:var(--text2);letter-spacing:.3px}
        .form-label .req{color:var(--danger)}
        .form-control,.form-select{width:100%;background:var(--bg);border:1px solid var(--border2);border-radius:var(--radius-sm);padding:10px 14px;color:var(--text);font-size:14px;font-family:'DM Sans',sans-serif;transition:border-color .15s,box-shadow .15s;outline:none;-webkit-appearance:none}
        .form-control:focus,.form-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,229,176,0.08)}
        .form-control::placeholder{color:var(--text3)}
        .form-select option{background:var(--surface);color:var(--text)}
        textarea.form-control{resize:vertical;min-height:88px;line-height:1.6}
        .form-hint{color:var(--text3);font-size:11.5px;margin-top:5px}
        .invalid-feedback{color:var(--danger);font-size:11.5px;margin-top:5px;display:none}
        .was-validated .form-control:invalid,.was-validated .form-select:invalid{border-color:var(--danger);box-shadow:0 0 0 3px rgba(244,63,94,0.08)}
        .was-validated .form-control:invalid~.invalid-feedback,.was-validated .form-select:invalid~.invalid-feedback{display:block}

        /* ALERTS */
        .alert{padding:13px 16px;border-radius:var(--radius-sm);margin-bottom:20px;font-size:13px;display:flex;align-items:flex-start;gap:10px;line-height:1.5}
        .alert i{flex-shrink:0;margin-top:1px}
        .alert-danger{background:rgba(244,63,94,0.08);border:1px solid rgba(244,63,94,0.25);color:#fb7185}
        .alert-success{background:rgba(0,229,176,0.08);border:1px solid rgba(0,229,176,0.25);color:var(--accent)}
        .alert ul{margin:6px 0 0 16px}
        .alert-close{margin-left:auto;background:none;border:none;color:inherit;cursor:pointer;font-size:17px;line-height:1;flex-shrink:0;padding:0}

        /* ITEMS TABLE (purchase/sales rows) */
        .items-table{border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:20px}
        .items-table table{margin:0}
        .items-table thead th{background:var(--surface2)}
        .items-table tbody td{padding:8px 12px}
        .items-table .form-control,.items-table .form-select{padding:8px 10px;font-size:13px}
        .add-row-btn{display:inline-flex;align-items:center;gap:6px;background:none;border:1px dashed var(--border2);color:var(--text3);padding:9px 18px;border-radius:var(--radius-sm);cursor:pointer;font-size:13px;font-family:'DM Sans',sans-serif;transition:all .15s;margin-bottom:20px}
        .add-row-btn:hover{border-color:var(--accent);color:var(--accent)}
        .remove-row-btn{background:rgba(244,63,94,0.1);border:none;color:var(--danger);width:28px;height:28px;border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:background .15s}
        .remove-row-btn:hover{background:rgba(244,63,94,0.25)}

        /* DETAIL VIEW */
        .detail-table{width:100%;border-collapse:collapse}
        .detail-table tr{border-bottom:1px solid var(--border)}
        .detail-table tr:last-child{border-bottom:none}
        .detail-table .dl{padding:12px 16px;color:var(--text3);font-family:'DM Mono',monospace;font-size:11.5px;width:180px;vertical-align:top;text-transform:uppercase;letter-spacing:.5px}
        .detail-table .dv{padding:12px 16px;color:var(--text);font-size:14px}

        /* DIVIDER */
        hr{border:none;border-top:1px solid var(--border);margin:24px 0}

        /* EMPTY STATE */
        .empty-state{text-align:center;padding:56px 20px;color:var(--text3)}
        .empty-state i{font-size:36px;margin-bottom:12px;display:block;opacity:.4}
        .empty-state p{font-size:14px}

        /* SCROLLBAR */
        ::-webkit-scrollbar{width:5px;height:5px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:var(--border2);border-radius:5px}

        /* ANIMATION */
        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .main>*{animation:fadeUp .22s ease both}
        .main>*:nth-child(2){animation-delay:.04s}
        .main>*:nth-child(3){animation-delay:.08s}
    </style>
</head>
<body>

<header class="topbar">
    <a href="index.php" class="brand"><span class="brand-dot"></span>GIS Crystal</a>
    <div class="topbar-spacer"></div>
    <span class="topbar-chip">General Inventory System</span>
</header>

<div class="layout">
<aside class="sidebar">
    <div class="nav-section">Master Data</div>
    <a class="nav-link" href="/gis_crystal/product_group/index.php"><i class="bi bi-tags"></i>Product Groups</a>
    <a class="nav-link" href="/gis_crystal/unit_of_measure/index.php"><i class="bi bi-rulers"></i>Units of Measure</a>
    <a class="nav-link" href="/gis_crystal/product/index.php"><i class="bi bi-box-seam"></i>Products</a>
    <a class="nav-link" href="/gis_crystal/vendor/index.php"><i class="bi bi-truck"></i>Vendors</a>
    <a class="nav-link" href="/gis_crystal/customer/index.php"><i class="bi bi-people"></i>Customers</a>
    <div class="nav-section">Transactions</div>
    <a class="nav-link" href="/gis_crystal/purchase/create.php"><i class="bi bi-cart-plus"></i>New Purchase</a>
    <a class="nav-link" href="/gis_crystal/purchase/index.php"><i class="bi bi-cart3"></i>All Purchases</a>
    <a class="nav-link" href="/gis_crystal/sales/create.php"><i class="bi bi-plus-circle"></i>New Sale</a>
    <a class="nav-link" href="/gis_crystal/sales/index.php"><i class="bi bi-receipt"></i>All Sales</a>
    <div class="nav-section">Reports</div>
    <a class="nav-link" href="/gis_crystal/stock/index.php"><i class="bi bi-bar-chart-line"></i>Stock Overview</a>
</aside>

<main class="main">
<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><?= htmlspecialchars($_SESSION['success']) ?><button class="alert-close" onclick="this.parentElement.remove()">×</button></div>
<?php unset($_SESSION['success']); endif; ?>
<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><?= htmlspecialchars($_SESSION['error']) ?><button class="alert-close" onclick="this.parentElement.remove()">×</button></div>
<?php unset($_SESSION['error']); endif; ?>
