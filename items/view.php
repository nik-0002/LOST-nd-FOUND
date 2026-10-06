<?php include '../includes/header.php';
$s=$pdo->prepare('SELECT i.*,u.name uname FROM items i JOIN users u ON u.id=i.user_id WHERE i.id=?');$s->execute([(int)$_GET['id']]);$i=$s->fetch();if(!$i)die('<div class="empty-state"><span class="icon">❌</span><h3>Item Not Found</h3><p>This item may have been removed.</p></div>');

// Get claim count
$claimCount = $pdo->prepare('SELECT COUNT(*) FROM claims WHERE item_id=?');$claimCount->execute([$i['id']]);$claimNum=$claimCount->fetchColumn();

// Status map for timeline
$statusOrder = ['lost' => 0, 'found' => 0, 'claimed' => 1, 'returned' => 2];
$currentStep = $statusOrder[$i['status']] ?? 0;

// Time ago
$created = new DateTime($i['created_at']);
$now = new DateTime();
$diff = $now->diff($created);
if ($diff->days == 0) $timeAgo = 'Today';
elseif ($diff->days == 1) $timeAgo = 'Yesterday';
elseif ($diff->days < 7) $timeAgo = $diff->days . ' days ago';
elseif ($diff->days < 30) $timeAgo = floor($diff->days / 7) . ' weeks ago';
else $timeAgo = floor($diff->days / 30) . ' months ago';
?>

<div style="margin-bottom:20px">
  <a href="<?=BASE?>/items/list.php" style="font-family:'Syncopate',sans-serif;font-size:0.75rem;text-transform:uppercase;letter-spacing:1px">← Back to Browse</a>
</div>

<h2><?=e($i['name'])?> <span class="badge <?=e($i['status'])?>" style="position:relative;top:auto;right:auto;display:inline-block"><?=e($i['status'])?></span></h2>

<!-- Status Timeline -->
<div class="status-timeline">
  <div class="step <?=$currentStep >= 0 ? 'complete' : ''?> <?=$currentStep === 0 ? 'active' : ''?>">
    <div class="dot"></div>
    <span class="label"><?=ucfirst($i['type'])?></span>
  </div>
  <div class="step <?=$currentStep >= 1 ? 'complete' : ''?> <?=$currentStep === 1 ? 'active' : ''?>">
    <div class="dot"></div>
    <span class="label">Claimed</span>
  </div>
  <div class="step <?=$currentStep >= 2 ? 'complete' : ''?> <?=$currentStep === 2 ? 'active' : ''?>">
    <div class="dot"></div>
    <span class="label">Returned</span>
  </div>
</div>

<div class="item-detail">
  <!-- Image -->
  <div>
    <?php if($i['image_path']): ?>
      <img src="<?=BASE?>/assets/uploads/<?=e($i['image_path'])?>" alt="<?=e($i['name'])?>">
    <?php else: ?>
      <div class="card-placeholder" style="height:300px;border-radius:var(--radius);font-size:4rem">📦</div>
    <?php endif; ?>
  </div>
  
  <!-- Info -->
  <div class="info">
    <div class="info-row">
      <b>Category</b>
      <span><?=e($i['category'])?></span>
    </div>
    <div class="info-row">
      <b>Date</b>
      <span><?=e($i['date_event'])?></span>
    </div>
    <div class="info-row">
      <b>Location</b>
      <span><?=e($i['location_text'])?></span>
    </div>
    <div class="info-row">
      <b>Posted by</b>
      <span><?=e($i['uname'])?></span>
    </div>
    <div class="info-row">
      <b>Reported</b>
      <span><?=$timeAgo?> · <?=date('M j, Y', strtotime($i['created_at']))?></span>
    </div>
    <div class="info-row">
      <b>Claims</b>
      <span><?=$claimNum?> claim<?=$claimNum != 1 ? 's' : ''?> submitted</span>
    </div>
    
    <?php if($i['description']): ?>
    <div style="margin-top:10px;padding:20px;background:rgba(255,255,255,0.03);border-radius:var(--radius-sm);border:1px solid rgba(255,255,255,0.05)">
      <b style="font-family:'Syncopate',sans-serif;font-size:0.7rem;color:var(--fg-muted);text-transform:uppercase;letter-spacing:1px;display:block;margin-bottom:10px">Description</b>
      <p style="margin:0"><?=nl2br(e($i['description']))?></p>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Claim Form -->
<?php if(user()&&in_array($i['status'], ['found', 'lost'])&&user()['id']!=$i['user_id']): ?>
<hr>
<h3><?= $i['type']==='found' ? '✋ Claim this item' : '📍 I found this item' ?></h3>
<form method="post" action="<?=BASE?>/claims/submit.php">
  <?=csrf()?>
  <input type="hidden" name="item_id" value="<?=$i['id']?>">
  <textarea name="proof" placeholder="<?= $i['type']==='found' ? 'Prove ownership: colour, marks, contents, any identifying details...' : 'Where did you find it? Describe the condition, exact location...' ?>" required rows="4"></textarea>
  <button><?= $i['type']==='found' ? '✋ Submit Claim' : '📍 Contact Owner' ?></button>
</form>
<?php elseif(!user()): ?>
<hr>
<div style="text-align:center;padding:30px;background:rgba(255,255,255,0.03);border-radius:var(--radius);border:1px solid rgba(255,255,255,0.05)">
  <p style="margin-bottom:15px">Want to claim this item?</p>
  <a href="<?=BASE?>/auth/login.php"><button>Login to Claim</button></a>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
