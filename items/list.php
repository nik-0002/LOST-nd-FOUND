<?php require_once '../config/db.php';
$w=['1=1'];$p=[];
if(!empty($_GET['q'])){$w[]='(name LIKE ? OR description LIKE ? OR location_text LIKE ?)';$q='%'.$_GET['q'].'%';array_push($p,$q,$q,$q);}
if(!empty($_GET['category'])){$w[]='category=?';$p[]=$_GET['category'];}
if(!empty($_GET['status'])){$w[]='status=?';$p[]=$_GET['status'];}
if(!empty($_GET['from'])){$w[]='date_event>=?';$p[]=$_GET['from'];}
if(!empty($_GET['to'])){$w[]='date_event<=?';$p[]=$_GET['to'];}
$s=$pdo->prepare('SELECT * FROM items WHERE '.implode(' AND ',$w).' ORDER BY created_at DESC LIMIT 100');$s->execute($p);$rows=$s->fetchAll();
$totalCount = count($rows);
ob_start(); foreach($rows as $idx => $i): ?>
<div class="card" style="animation-delay: <?=$idx * 0.06?>s">
  <?php if($i['image_path']): ?>
    <img src="<?=BASE?>/assets/uploads/<?=e($i['image_path'])?>" alt="<?=e($i['name'])?>" loading="lazy">
  <?php else: ?>
    <div class="card-placeholder">📦</div>
  <?php endif; ?>
  <span class="badge <?=e($i['status'])?>"><?=e($i['status'])?></span>
  <h3><?=e($i['name'])?></h3>
  <p><?=e($i['category'])?> · <?=e($i['date_event'])?><br><?=e($i['location_text'])?></p>
  <a href="view.php?id=<?=$i['id']?>">View Details →</a>
</div>
<?php endforeach;
if(!$rows): ?>
<div class="empty-state" style="grid-column:1/-1">
  <span class="icon">🔍</span>
  <h3>No Items Found</h3>
  <p>Try adjusting your search filters or report a new item.</p>
</div>
<?php endif;
$html=ob_get_clean();
if(isset($_GET['ajax'])){echo $html;exit;}
include '../includes/header.php';
$cats=$pdo->query('SELECT name FROM categories')->fetchAll(PDO::FETCH_COLUMN); ?>

<div class="section-header">
  <h2>Browse Items</h2>
  <span style="font-family:'Syncopate',sans-serif;font-size:0.8rem;color:var(--fg-muted);text-transform:uppercase;letter-spacing:1px"><?=$totalCount?> result<?=$totalCount!==1?'s':''?></span>
</div>

<form id="f" class="filters">
  <input name="q" placeholder="🔍 Search by keyword..." value="<?=e($_GET['q'] ?? '')?>">
  <select name="category">
    <option value="">All categories</option>
    <?php foreach($cats as $c): ?>
      <option <?=($_GET['category']??'')===$c?'selected':''?>><?=e($c)?></option>
    <?php endforeach; ?>
  </select>
  <select name="status">
    <option value="">Any status</option>
    <option <?=($_GET['status']??'')==='lost'?'selected':''?>>lost</option>
    <option <?=($_GET['status']??'')==='found'?'selected':''?>>found</option>
    <option <?=($_GET['status']??'')==='claimed'?'selected':''?>>claimed</option>
    <option <?=($_GET['status']??'')==='returned'?'selected':''?>>returned</option>
  </select>
  <input type="date" name="from" value="<?=e($_GET['from'] ?? '')?>" placeholder="From date">
  <input type="date" name="to" value="<?=e($_GET['to'] ?? '')?>" placeholder="To date">
</form>

<div id="grid" class="grid"><?=$html?></div>

<?php include '../includes/footer.php'; ?>
