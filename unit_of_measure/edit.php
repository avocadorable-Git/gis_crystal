<?php
require_once '../config/db.php';

$id=intval($_GET['id']??0);
if ($id<=0){header("Location: index.php");exit;}
$stmt=$conn->prepare("SELECT * FROM units_of_measure WHERE id=?");
$stmt->bind_param("i",$id);$stmt->execute();
$row=$stmt->get_result()->fetch_assoc();$stmt->close();
if (!$row){header("Location: index.php");exit;}
$errors=[];$name=$row['name'];$code=$row['code'];$description=$row['description'];$status=$row['status'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??'');$code=strtoupper(trim($_POST['code']??''));
    $description=trim($_POST['description']??'');$status=$_POST['status']??'';
    if (empty($name))            $errors[]="Name is required.";
    elseif (strlen($name)>100)   $errors[]="Name max 100 chars.";
    if (empty($code))            $errors[]="Code is required.";
    elseif (strlen($code)>20)    $errors[]="Code max 20 chars.";
    if (!in_array($status,['active','inactive'])) $errors[]="Invalid status.";
    if (empty($errors)) {
        $stmt=$conn->prepare("UPDATE units_of_measure SET name=?,code=?,description=?,status=? WHERE id=?");
        $stmt->bind_param("ssssi",$name,$code,$description,$status,$id);
        if ($stmt->execute()){$_SESSION['success']="Unit updated.";header("Location: index.php");exit;}
        else $errors[]="DB error.";$stmt->close();
    }
}
require_once '../includes/header.php';
?>
<div class="page-header">
  <div><div class="page-title">Edit Unit of Measure</div><div class="page-subtitle">ID #<?= $id ?></div></div>
  <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i>Back</a>
</div>
<div class="card" style="max-width:640px"><div class="card-body">
<?php if (!empty($errors)): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><div><ul style="margin:0 0 0 14px"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div></div><?php endif; ?>
<form method="POST" class="needs-validation" novalidate>
  <div class="form-grid">
    <div class="form-group"><label class="form-label">Name <span class="req">*</span></label>
      <input type="text" name="name" class="form-control" maxlength="100" value="<?= htmlspecialchars($name) ?>" required>
      <div class="invalid-feedback">Name is required.</div></div>
    <div class="form-group"><label class="form-label">Code <span class="req">*</span></label>
      <input type="text" name="code" class="form-control" maxlength="20" value="<?= htmlspecialchars($code) ?>" required>
      <div class="invalid-feedback">Code is required.</div></div>
  </div>
  <div class="form-group"><label class="form-label">Description</label>
    <textarea name="description" class="form-control"><?= htmlspecialchars($description) ?></textarea></div>
  <div class="form-group"><label class="form-label">Status <span class="req">*</span></label>
    <select name="status" class="form-select" required>
      <option value="">— Select —</option>
      <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
      <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inactive</option>
    </select><div class="invalid-feedback">Required.</div></div>
  <div style="display:flex;gap:10px"><button type="submit" class="btn btn-primary">Update Unit</button><a href="index.php" class="btn btn-outline">Cancel</a></div>
</form>
</div></div>
<?php require_once '../includes/footer.php'; ?>
