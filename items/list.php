<?php require_once '../config/db.php';
$w=['1=1'];$p=[];
if(!empty($_GET['q'])){$w[]='(name LIKE ? OR description LIKE ? OR location_text LIKE ?)';$q='%'.$_GET['q'].'%';array_push($p,$q,$q,$q);}
if(!empty($_GET['category'])){$w[]='category=?';$p[]=$_GET['category'];}
if(!empty($_GET['status'])){$w[]='status=?';$p[]=$_GET['status'];}
if(!empty($_GET['from'])){$w[]='date_event>=?';$p[]=$_GET['from'];}
if(!empty($_GET['to'])){$w[]='date_event<=?';$p[]=$_GET['to'];}
$s=$pdo->prepare('SELECT * FROM items WHERE '.implode(' AND ',$w).' ORDER BY created_at DESC LIMIT 100');$s->execute($p);$rows=$s->fetchAll();
ob_start(); foreach($rows as $i): ?>
<div class="card"><?php if($i['image_path'])echo '<img src="'.BASE.'/assets/uploads/'.e($i['image_path']).'">'; ?><span class="badge <?=e($i['status'])?>"><?=e($i['status'])?></span>
<h3><?=e($i['name'])?></h3><p><?=e($i['category'])?> · <?=e($i['date_event'])?><br><?=e($i['location_text'])?></p><a href="view.php?id=<?=$i['id']?>">View →</a></div>
<?php endforeach; if(!$rows)echo '<p>No items found.</p>'; $html=ob_get_clean();
if(isset($_GET['ajax'])){echo $html;exit;}
include '../includes/header.php';
$cats=$pdo->query('SELECT name FROM categories')->fetchAll(PDO::FETCH_COLUMN); ?>
<h2>Browse Items</h2><form id="f" class="filters"><input name="q" placeholder="Keyword"><select name="category"><option value="">All categories</option><?php foreach($cats as $c)echo '<option>'.e($c).'</option>'; ?></select>
<select name="status"><option value="">Any status</option><option>lost</option><option>found</option><option>claimed</option><option>returned</option></select><input type="date" name="from"><input type="date" name="to"></form>
<div id="grid" class="grid"><?=$html?></div>
<script>const f=document.getElementById('f');f.addEventListener('input',()=>{fetch('list.php?ajax=1&'+new URLSearchParams(new FormData(f))).then(r=>r.text()).then(h=>grid.innerHTML=h)});</script>
<?php include '../includes/footer.php'; ?>
