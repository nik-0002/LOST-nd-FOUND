<?php include '../includes/header.php'; need(); $u=user();
if($_SERVER['REQUEST_METHOD']==='POST'){check(); $c=claimAccess($pdo,(int)$_POST['claim_id']);
 if($u['role']!=='admin'&&$u['id']!=$c['owner_id'])die('Forbidden');
 if($_POST['act']==='approve'&&$c['status']==='pending'){
  $pdo->prepare("UPDATE claims SET status='approved' WHERE id=?")->execute([$c['id']]);
  $pdo->prepare("UPDATE items SET status='claimed' WHERE id=?")->execute([$c['item_id']]);
  $pdo->prepare('INSERT INTO handovers(claim_id,qr_token)VALUES(?,?)')->execute([$c['id'],bin2hex(random_bytes(16))]);
  notify($pdo,$c['claimant_id'],'Claim approved for "'.$c['item_name'].'"','/handover/qr.php?claim='.$c['id']);
 }elseif($_POST['act']==='reject'&&$c['status']==='pending'){
  $pdo->prepare("UPDATE claims SET status='rejected' WHERE id=?")->execute([$c['id']]);
  notify($pdo,$c['claimant_id'],'Claim rejected for "'.$c['item_name'].'"','/claims/manage.php');}
 header('Location: manage.php');exit;}

$q=$pdo->prepare('SELECT c.*,i.name iname,u.name cname FROM claims c JOIN items i ON i.id=c.item_id JOIN users u ON u.id=c.claimant_id WHERE i.user_id=? ORDER BY c.id DESC');$q->execute([$u['id']]);
$incomingClaims = $q->fetchAll();

$q=$pdo->prepare('SELECT c.*,i.name iname FROM claims c JOIN items i ON i.id=c.item_id WHERE c.claimant_id=? ORDER BY c.id DESC');$q->execute([$u['id']]);
$myClaims = $q->fetchAll();

$pendingCount = count(array_filter($incomingClaims, fn($c) => $c['status'] === 'pending'));
?>

<h2>🤝 Claims Dashboard</h2>

<!-- Quick Stats -->
<div class="stats" style="margin-bottom:40px">
  <div>
    <b class="stat-number"><?=count($incomingClaims)?></b>
    <br><span style="font-family:'Syncopate',sans-serif;font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted)">Incoming Claims</span>
  </div>
  <div>
    <b class="stat-number"><?=$pendingCount?></b>
    <br><span style="font-family:'Syncopate',sans-serif;font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted)">Pending Review</span>
  </div>
  <div>
    <b class="stat-number"><?=count($myClaims)?></b>
    <br><span style="font-family:'Syncopate',sans-serif;font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted)">My Claims</span>
  </div>
</div>

<h3>📩 Claims on My Items</h3>
<?php if($incomingClaims): ?>
<div class="grid" style="margin-top:20px">
  <?php foreach($incomingClaims as $idx => $c): ?>
  <div class="card" style="animation-delay:<?=$idx * 0.08?>s">
    <span class="badge <?=e($c['status'])?>"><?=e($c['status'])?></span>
    <h3><?=e($c['iname'])?></h3>
    <p><strong>Claimant:</strong> <?=e($c['cname'])?></p>
    <p style="background:rgba(255,255,255,0.03);padding:12px;border-radius:var(--radius-sm);border:1px solid rgba(255,255,255,0.05);font-style:italic">"<?=e($c['proof_description'])?>"</p>
    <?php if($c['status']==='pending'): ?>
    <form method="post" style="flex-direction:row;padding:10px 0;background:transparent;box-shadow:none;border:none;backdrop-filter:none;animation:none">
      <?=csrf()?><input type="hidden" name="claim_id" value="<?=$c['id']?>">
      <button name="act" value="approve" class="success" style="flex:1">✅ Approve</button>
      <button name="act" value="reject" class="danger" style="flex:1">❌ Reject</button>
    </form>
    <?php elseif($c['status']==='approved'): ?>
    <div style="display:flex;gap:10px;margin-top:10px">
      <a href="<?=BASE?>/chat/chat.php?claim=<?=$c['id']?>" style="flex:1;text-align:center;padding:12px;background:rgba(255,255,255,0.1);border-radius:50px;border:1px solid rgba(255,255,255,0.2);font-family:'Syncopate',sans-serif;font-size:0.7rem">💬 Chat</a>
      <a href="<?=BASE?>/handover/verify.php?claim=<?=$c['id']?>" style="flex:1;text-align:center;padding:12px;background:rgba(0,255,204,0.1);border-radius:50px;border:1px solid rgba(0,255,204,0.2);font-family:'Syncopate',sans-serif;font-size:0.7rem;color:var(--status-found)">🔐 Verify</a>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state">
  <span class="icon">📭</span>
  <h3>No Incoming Claims</h3>
  <p>When someone claims your items, they'll appear here.</p>
</div>
<?php endif; ?>

<hr>

<h3>📤 My Claims</h3>
<?php if($myClaims): ?>
<div class="grid" style="margin-top:20px">
  <?php foreach($myClaims as $idx => $c): ?>
  <div class="card" style="animation-delay:<?=$idx * 0.08?>s">
    <span class="badge <?=e($c['status'])?>"><?=e($c['status'])?></span>
    <h3><?=e($c['iname'])?></h3>
    <?php if($c['status']==='approved'): ?>
    <div style="display:flex;gap:10px;margin-top:10px">
      <a href="<?=BASE?>/chat/chat.php?claim=<?=$c['id']?>" style="flex:1;text-align:center;padding:12px;background:rgba(255,255,255,0.1);border-radius:50px;border:1px solid rgba(255,255,255,0.2);font-family:'Syncopate',sans-serif;font-size:0.7rem">💬 Chat</a>
      <a href="<?=BASE?>/handover/qr.php?claim=<?=$c['id']?>" style="flex:1;text-align:center;padding:12px;background:rgba(0,255,204,0.1);border-radius:50px;border:1px solid rgba(0,255,204,0.2);font-family:'Syncopate',sans-serif;font-size:0.7rem;color:var(--status-found)">📱 Show QR</a>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state">
  <span class="icon">🔍</span>
  <h3>No Claims Yet</h3>
  <p>Browse items and submit claims to get started.</p>
  <a href="<?=BASE?>/items/list.php" style="margin-top:15px;display:inline-block"><button class="secondary">Browse Items →</button></a>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
