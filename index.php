<?php include 'includes/header.php';
$n=$pdo->query("SELECT status,COUNT(*) c FROM items GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$recent=$pdo->query("SELECT * FROM items ORDER BY created_at DESC LIMIT 6")->fetchAll();
?>

<div style="text-align: center; margin: 40px 0 60px;">
  <h1>Intelligent Lost & Found</h1>
  <p style="font-size: 1.2rem; max-width: 600px; margin: 0 auto 30px;">Seamlessly report, search, claim, and recover lost belongings across campus in real time.</p>
  
  <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
    <a href="<?=BASE?>/items/report.php?type=lost"><button style="background: var(--status-lost); border-color: transparent;">Report Lost Item</button></a>
    <a href="<?=BASE?>/items/report.php?type=found"><button style="background: var(--status-found); border-color: transparent; color: #000;">Report Found Item</button></a>
    <a href="<?=BASE?>/items/list.php"><button style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3);">Browse All Items</button></a>
  </div>
</div>

<h2>Campus Overview</h2>
<div class="stats">
  <?php foreach(['lost','found','claimed','returned'] as $s): ?>
    <div>
      <b><?=$n[$s]??0?></b>
      <br><?=ucfirst($s)?>
    </div>
  <?php endforeach; ?>
</div>

<div style="margin-top: 60px;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Recent Reports</h2>
    <a href="<?=BASE?>/items/list.php" style="font-family: 'Syncopate', sans-serif; font-size: 0.8rem; text-transform: uppercase;">View All →</a>
  </div>
  
  <div class="grid">
    <?php foreach($recent as $i): ?>
      <div class="card">
        <?php if($i['image_path']): ?>
          <img src="<?=BASE?>/assets/uploads/<?=e($i['image_path'])?>" alt="<?=e($i['name'])?>">
        <?php else: ?>
          <div style="height: 180px; background: rgba(255,255,255,0.05); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: var(--fg-subtle); font-size: 2rem; margin-bottom: 20px;">📦</div>
        <?php endif; ?>
        <span class="badge <?=e($i['status'])?>"><?=e($i['status'])?></span>
        <h3><?=e($i['name'])?></h3>
        <p><?=e($i['category'])?> · <?=e($i['date_event'])?><br><?=e($i['location_text'])?></p>
        <a href="<?=BASE?>/items/view.php?id=<?=$i['id']?>">View Details →</a>
      </div>
    <?php endforeach; ?>
    <?php if(!$recent): ?>
      <p>No recent items reported yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
