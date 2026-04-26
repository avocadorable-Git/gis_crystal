<?php
require_once '../config/db.php';
require_once '../includes/header.php';
$id=intval($_GET['id']??0);
if ($id<=0){header("Location: index.php");exit;}
$stmt=$conn->prepare("SELECT * FROM product_groups WHERE id=?");
$stmt->bind_param("i",$id);$stmt->execute();
$row=$stmt->get_result()->fetch_assoc();$stmt->close();
if (!$row){header("Location: index.php");exit;}
$errors=[];$name=$row['name'];$description=$row['description'];$status=$row['status'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??'');$description=trim($_POST['description']??'');$status=$_POST['status']??'';
    if (empty($name)) $errors[]="Name is required.";
    elseif (strlen($name)>150) $errors[]="Name max 150 chars.";
    if (!in_array($status,['active','inactive'])) $errors[]="Invalid status.";
    if (empty($errors)) {
        $stmt=$conn->prepare("UPDATE product_groups SET name=?,description=?,status=? WHERE id=?");
        $stmt->bind_param("sssi",$name,$description,$status,$id);
        if ($stmt->execute()){ $_SESSION['success']="Product Group updated."; header("Location: index.php"); exit; }
        else $errors[]="DB error."; $stmt->close();
    }
}
?>
<div class="page-header">
  <div><div class="page-title">Edit Product Group</div><div class="page-subtitle">ID #<?= $id ?></div></div>
  <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i>Back</a>
</div>
<div class="card" style="max-width:640px"><div class="card-body">
<?php if (!empty($errors)): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><div><ul style="margin:0 0 0 14px"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div></div><?php endif; ?>
<form method="POST" class="needs-validation" novalidate>
  <div class="form-group"><label class="form-label">Name <span class="req">*</span></label>
    <input type="text" name="name" class="form-control" maxlength="150" value="<?= htmlspecialchars($name) ?>" required>
    <div class="invalid-feedback">Name is required.</div></div>
  <div class="form-group"><label class="form-label">Description</label>
    <textarea name="description" class="form-control"><?= htmlspecialchars($description) ?></textarea></div>
  <div class="form-group"><label class="form-label">Status <span class="req">*</span></label>
    <select name="status" class="form-select" required>
      <option value="">— Select —</option>
      <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
      <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inactive</option>
    </select><div class="invalid-feedback">Required.</div></div>
  <div style="display:flex;gap:10px"><button type="submit" class="btn btn-primary">Update</button><a href="index.php" class="btn btn-outline">Cancel</a></div>
</form>
</div></div>
<?php require_once '../includes/footer.php'; ?>
 