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
echo '<h2>Claims on my items</h2>'; foreach($q as $c): ?>
<div class="card"><b><?=e($c['iname'])?></b> — <?=e($c['cname'])?> <span class="badge <?=e($c['status'])?>"><?=e($c['status'])?></span><p><?=e($c['proof_description'])?></p>
<?php if($c['status']==='pending'): ?><form method="post"><?=csrf()?><input type="hidden" name="claim_id" value="<?=$c['id']?>"><button name="act" value="approve">Approve</button> <button name="act" value="reject">Reject</button></form>
<?php elseif($c['status']==='approved'): ?><a href="<?=BASE?>/chat/chat.php?claim=<?=$c['id']?>">Chat</a> · <a href="<?=BASE?>/handover/verify.php?claim=<?=$c['id']?>">Verify handover</a><?php endif; ?></div>
<?php endforeach;
$q=$pdo->prepare('SELECT c.*,i.name iname FROM claims c JOIN items i ON i.id=c.item_id WHERE c.claimant_id=? ORDER BY c.id DESC');$q->execute([$u['id']]);
echo '<h2>My claims</h2>'; foreach($q as $c): ?>
<div class="card"><b><?=e($c['iname'])?></b> <span class="badge <?=e($c['status'])?>"><?=e($c['status'])?></span>
<?php if($c['status']==='approved'): ?> <a href="<?=BASE?>/chat/chat.php?claim=<?=$c['id']?>">Chat</a> · <a href="<?=BASE?>/handover/qr.php?claim=<?=$c['id']?>">Show QR</a><?php endif; ?></div>
<?php endforeach; include '../includes/footer.php'; ?>
