<?php include '../includes/header.php'; need();
$type=($_GET['type']??'lost')==='found'?'found':'lost'; $err='';
$cats=$pdo->query('SELECT name FROM categories')->fetchAll(PDO::FETCH_COLUMN);
if($_SERVER['REQUEST_METHOD']==='POST'){check(); $img=null;
 if(!empty($_FILES['image']['name'])){
  $f=$_FILES['image'];$mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$ext=['image/jpeg'=>'jpg','image/png'=>'png'][$mime]??null;
  if(!$ext||$f['size']>2*1024*1024)$err='Image must be JPG/PNG under 2MB.';
  else{$img=bin2hex(random_bytes(8)).'.'.$ext;move_uploaded_file($f['tmp_name'],'../assets/uploads/'.$img);}}
 if(!$err){
  if(!trim($_POST['name'])||!in_array($_POST['category'],$cats)||!$_POST['date'])$err='Fill all required fields.';
  else{$lat=null;$lng=null;
   $pdo->prepare('INSERT INTO items(user_id,type,name,category,description,image_path,date_event,location_text,lat,lng,status)VALUES(?,?,?,?,?,?,?,?,?,?,?)')
   ->execute([user()['id'],$type,trim($_POST['name']),$_POST['category'],$_POST['description'],$img,$_POST['date'],$_POST['location'],$lat,$lng,$type]);
   header('Location: list.php');exit;}}}
?>
<div style="margin-bottom:20px">
  <a href="<?=BASE?>/items/list.php" style="font-family:'Syncopate',sans-serif;font-size:0.75rem;text-transform:uppercase;letter-spacing:1px">← Back to Browse</a>
</div>

<h2><?=$type==='lost'?'📢':'🎉'?> Report <?=ucfirst($type)?> Item</h2>

<?php if($err): ?><p class="err"><?=e($err)?></p><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <?=csrf()?>
  
  <div class="form-group">
    <input name="name" placeholder="Item name (e.g. Black iPhone 15, Blue Backpack)" required>
    <label>Item Name</label>
  </div>
  
  <select name="category">
    <?php foreach($cats as $c)echo '<option>'.e($c).'</option>'; ?>
  </select>
  
  <div class="form-group">
    <textarea name="description" placeholder="Describe the item in detail — colour, brand, marks, contents, any unique identifiers..." rows="4"></textarea>
    <label>Description</label>
  </div>
  
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
    <div>
      <label style="display:block;margin-bottom:8px">Date <?=$type==='lost'?'lost':'found'?></label>
      <input type="date" name="date" max="<?=date('Y-m-d')?>" required>
    </div>
    <div class="form-group">
      <input name="location" placeholder="e.g. Library 2nd floor, Cafeteria">
      <label>Location</label>
    </div>
  </div>
  
  <div style="padding:24px;background:rgba(255,255,255,0.03);border-radius:var(--radius-sm);border:2px dashed rgba(255,255,255,0.1);text-align:center;cursor:pointer;transition:var(--transition)" id="dropzone">
    <span style="font-size:2rem;display:block;margin-bottom:10px">📷</span>
    <p style="margin:0">Drag & drop an image or click to upload</p>
    <p style="margin:5px 0 0;font-size:0.8rem;color:var(--fg-subtle)">JPG/PNG, max 2MB</p>
    <input type="file" name="image" accept="image/*" style="display:none" id="fileInput">
    <div id="preview" style="margin-top:15px"></div>
  </div>
  
  <button><?=$type==='lost'?'📢 Report Lost':'🎉 Report Found'?></button>
</form>

<script>
  // Drag & drop upload
  const dropzone = document.getElementById('dropzone');
  const fileInput = document.getElementById('fileInput');
  const preview = document.getElementById('preview');
  
  dropzone.addEventListener('click', () => fileInput.click());
  dropzone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropzone.style.borderColor = 'rgba(0,255,204,0.5)';
    dropzone.style.background = 'rgba(0,255,204,0.05)';
  });
  dropzone.addEventListener('dragleave', () => {
    dropzone.style.borderColor = 'rgba(255,255,255,0.1)';
    dropzone.style.background = 'rgba(255,255,255,0.03)';
  });
  dropzone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzone.style.borderColor = 'rgba(255,255,255,0.1)';
    dropzone.style.background = 'rgba(255,255,255,0.03)';
    if (e.dataTransfer.files.length) {
      fileInput.files = e.dataTransfer.files;
      showPreview(e.dataTransfer.files[0]);
    }
  });
  fileInput.addEventListener('change', () => {
    if (fileInput.files.length) showPreview(fileInput.files[0]);
  });
  
  function showPreview(file) {
    if (!file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.innerHTML = `<img src="${e.target.result}" style="max-width:200px;max-height:150px;border-radius:var(--radius-sm);margin-top:10px;animation:fadeInScale 0.3s ease-out">`;
      dropzone.querySelector('p').textContent = file.name;
    };
    reader.readAsDataURL(file);
  }
</script>

<?php include '../includes/footer.php'; ?>
