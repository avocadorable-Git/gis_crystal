<?php
require_once '../config/db.php';

$id=intval($_GET['id']??0);
if ($id<=0){header("Location: index.php");exit;}
$stmt=$conn->prepare("SELECT * FROM vendors WHERE id=?");
$stmt->bind_param("i",$id);$stmt->execute();
$row=$stmt->get_result()->fetch_assoc();$stmt->close();
if (!$row){header("Location: index.php");exit;}
$errors=[];
$name=$row['name'];$description=$row['description'];$status=$row['status'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??'');
    $description=trim($_POST['description']??'');
    $status=$_POST['status']??'';
    if (empty($name)) $errors[]="Name is required.";
    elseif (strlen($name)>150) $errors[]="Name must be under 150 characters.";
    if (!in_array($status,['active','inactive'])) $errors[]="Invalid status.";
    if (empty($errors)) {
        $stmt=$conn->prepare("UPDATE vendors SET name=?,description=?,status=? WHERE id=?");
        $stmt->bind_param("sssi",$name,$description,$status,$id);
        if ($stmt->execute()) { $_SESSION['success']="Vendor updated."; header("Location: index.php"); exit; }
        else $errors[]="Database error.";
        $stmt->close();
    }
}
require_once '../includes/header.php';
?>
<div class="page-header">
  <div><div class="page-title">Edit Vendor</div><div class="page-subtitle">ID #<?= $id ?></div></div>
  <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i>Back</a>
</div>
<div class="card" style="max-width:640px">
  <div class="card-body">
<?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><div><ul style="margin:0 0 0 14px"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div></div>
<?php endif; ?>
<form method="POST" class="needs-validation" novalidate>
  <div class="form-group"><label class="form-label">Name <span class="req">*</span></label>
    <input type="text" name="name" class="form-control" maxlength="150" value="<?= htmlspecialchars($name) ?>" required>
    <div class="invalid-feedback">Name is required.</div></div>
  <div class="form-group"><label class="form-label">Description</label>
    <textarea name="description" class="form-control"><?= htmlspecialchars($description) ?></textarea></div>
  <div class="form-group"><label class="form-label">Status <span class="req">*</span></label>
    <select name="status" class="form-select" required>
      <option value="">— Select Status —</option>
      <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
      <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inactive</option>
    </select><div class="invalid-feedback">Please select a status.</div></div>
  <div style="display:flex;gap:10px;margin-top:8px">
    <button type="submit" class="btn btn-primary">Update Vendor</button>
    <a href="index.php" class="btn btn-outline">Cancel</a>
  </div>
</form>
  </div>
</div>
<?php require_once '../includes/footer.php'; ?>
