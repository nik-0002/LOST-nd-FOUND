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
  else{$lat=$_POST['lat']!==''?(float)$_POST['lat']:null;$lng=$_POST['lng']!==''?(float)$_POST['lng']:null;
   $pdo->prepare('INSERT INTO items(user_id,type,name,category,description,image_path,date_event,location_text,lat,lng,status)VALUES(?,?,?,?,?,?,?,?,?,?,?)')
   ->execute([user()['id'],$type,trim($_POST['name']),$_POST['category'],$_POST['description'],$img,$_POST['date'],$_POST['location'],$lat,$lng,$type]);
   header('Location: list.php');exit;}}}
?><h2>Report <?=ucfirst($type)?> Item</h2><p class="err"><?=e($err)?></p>
<form method="post" enctype="multipart/form-data"><?=csrf()?><input name="name" placeholder="Item name" required>
<select name="category"><?php foreach($cats as $c)echo '<option>'.e($c).'</option>'; ?></select>
<textarea name="description" placeholder="Description (colour, marks, brand...)"></textarea>
<input type="date" name="date" max="<?=date('Y-m-d')?>" required><input name="location" placeholder="Location (e.g. Library 2nd floor)">
<input type="hidden" name="lat" id="lat"><input type="hidden" name="lng" id="lng"><div id="map" style="height:260px"></div>
<label>Image (JPG/PNG, max 2MB)</label><input type="file" name="image" accept="image/*"><button>Submit</button></form>
<script>function initMap(){const m=new google.maps.Map(document.getElementById('map'),{center:{lat:16.4419,lng:80.6226},zoom:16});let k;
m.addListener('click',e=>{if(k)k.setMap(null);k=new google.maps.Marker({position:e.latLng,map:m});lat.value=e.latLng.lat();lng.value=e.latLng.lng();});}</script>
<script async src="https://maps.googleapis.com/maps/api/js?key=<?=MAPS_KEY?>&callback=initMap"></script>
<?php include '../includes/footer.php'; ?>
