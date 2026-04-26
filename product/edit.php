<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$id=intval($_GET['id']??0);
if ($id<=0){header("Location: index.php");exit;}
$stmt=$conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i",$id);$stmt->execute();
$row=$stmt->get_result()->fetch_assoc();$stmt->close();
if (!$row){header("Location: index.php");exit;}
$errors=[];$name=$row['name'];$description=$row['description'];$unit_id=$row['unit_id'];$status=$row['status'];
$units=$conn->query("SELECT id,name,code FROM units_of_measure WHERE status='active' ORDER BY name");
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??'');$description=trim($_POST['description']??'');
    $unit_id=intval($_POST['unit_id']??0);$status=$_POST['status']??'';
    if (empty($name))            $errors[]="Name is required.";
    elseif (strlen($name)>150)   $errors[]="Name max 150 chars.";
    if ($unit_id<=0)             $errors[]="Please select a unit.";
    if (!in_array($status,['active','inactive'])) $errors[]="Invalid status.";
    if (empty($errors)) {
        $stmt=$conn->prepare("UPDATE products SET name=?,description=?,unit_id=?,status=? WHERE id=?");
        $stmt->bind_param("sssii",$name,$description,$unit_id,$status,$id);
        if ($stmt->execute()){$_SESSION['success']="Product updated.";header("Location: index.php");exit;}
        else $errors[]="DB error.";$stmt->close();
    }
}
?>
<div class="page-header">
  <div><div class="page-title">Edit Product</div><div class="page-subtitle">ID #<?= $id ?></div></div>
  <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i>Back</a>
</div>
<div class="card" style="max-width:700px"><div class="card-body">
<?php if (!empty($errors)): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><div><ul style="margin:0 0 0 14px"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div></div><?php endif; ?>
<form method="POST" class="needs-validation" novalidate>
  <div class="form-grid">
    <div class="form-group" style="grid-column:1/-1"><label class="form-label">Product Name <span class="req">*</span></label>
      <input type="text" name="name" class="form-control" maxlength="150" value="<?= htmlspecialchars($name) ?>" required>
      <div class="invalid-feedback">Name is required.</div></div>
    <div class="form-group"><label class="form-label">Unit of Measure <span class="req">*</span></label>
      <select name="unit_id" class="form-select" required>
        <option value="">— Select Unit —</option>
        <?php while ($u=$units->fetch_assoc()): ?>
          <option value="<?= $u['id'] ?>" <?= $unit_id==$u['id']?'selected':'' ?>><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['code']) ?>)</option>
        <?php endwhile; ?>
      </select><div class="invalid-feedback">Required.</div></div>
    <div class="form-group"><label class="form-label">Status <span class="req">*</span></label>
      <select name="status" class="form-select" required>
        <option value="">— Select —</option>
        <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inactive</option>
      </select><div class="invalid-feedback">Required.</div></div>
    <div class="form-group" style="grid-column:1/-1"><label class="form-label">Description</label>
      <textarea name="description" class="form-control"><?= htmlspecialchars($description) ?></textarea></div>
  </div>
  <div style="display:flex;gap:10px;margin-top:8px"><button type="submit" class="btn btn-primary">Update Product</button><a href="index.php" class="btn btn-outline">Cancel</a></div>
</form>
</div></div>
<?php require_once '../includes/footer.php'; ?>
