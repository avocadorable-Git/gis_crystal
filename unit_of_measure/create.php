<?php
require_once '../config/db.php';

$errors=[];$name=$code=$description=$status='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=trim($_POST['name']??'');$code=strtoupper(trim($_POST['code']??''));
    $description=trim($_POST['description']??'');$status=$_POST['status']??'';
    if (empty($name))            $errors[]="Name is required.";
    elseif (strlen($name)>100)   $errors[]="Name max 100 chars.";
    if (empty($code))            $errors[]="Code is required.";
    elseif (strlen($code)>20)    $errors[]="Code max 20 chars.";
    if (!in_array($status,['active','inactive'])) $errors[]="Invalid status.";
    if (empty($errors)) {
        $stmt=$conn->prepare("INSERT INTO units_of_measure (name,code,description,status) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss",$name,$code,$description,$status);
        if ($stmt->execute()){$_SESSION['success']="Unit created.";header("Location: index.php");exit;}
        else $errors[]="DB error.";$stmt->close();
    }
}
require_once '../includes/header.php';
?>
<div class="page-header">
  <div><div class="page-title">New Unit of Measure</div></div>
  <a href="index.php" class="btn btn-outline"><i class="bi bi-arrow-left"></i>Back</a>
</div>
<div class="card" style="max-width:640px"><div class="card-body">
<?php if (!empty($errors)): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><div><ul style="margin:0 0 0 14px"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div></div><?php endif; ?>
<form method="POST" class="needs-validation" novalidate>
  <div class="form-grid">
    <div class="form-group"><label class="form-label">Name <span class="req">*</span></label>
      <input type="text" name="name" class="form-control" maxlength="100" value="<?= htmlspecialchars($name) ?>" placeholder="e.g. Kilogram" required>
      <div class="invalid-feedback">Name is required.</div></div>
    <div class="form-group"><label class="form-label">Code <span class="req">*</span></label>
      <input type="text" name="code" class="form-control" maxlength="20" value="<?= htmlspecialchars($code) ?>" placeholder="e.g. KG" required>
      <div class="invalid-feedback">Code is required.</div>
      <div class="form-hint">Short abbreviation (auto uppercased)</div></div>
  </div>
  <div class="form-group"><label class="form-label">Description</label>
    <textarea name="description" class="form-control" placeholder="Optional"><?= htmlspecialchars($description) ?></textarea></div>
  <div class="form-group"><label class="form-label">Status <span class="req">*</span></label>
    <select name="status" class="form-select" required>
      <option value="">— Select —</option>
      <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
      <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inactive</option>
    </select><div class="invalid-feedback">Required.</div></div>
  <div style="display:flex;gap:10px"><button type="submit" class="btn btn-primary">Save Unit</button><a href="index.php" class="btn btn-outline">Cancel</a></div>
</form>
</div></div>
<?php require_once '../includes/footer.php'; ?>
