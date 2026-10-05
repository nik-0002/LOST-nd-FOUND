<?php include '../includes/header.php'; need(); $c=claimAccess($pdo,(int)$_GET['claim']); $m='';
if(user()['id']!=$c['owner_id']&&user()['role']!=='admin')die('Only finder/admin can verify');
if($_SERVER['REQUEST_METHOD']==='POST'){check();
 $s=$pdo->prepare("SELECT * FROM handovers WHERE claim_id=? AND status='pending'");$s->execute([$c['id']]);$h=$s->fetch();
 if($h&&hash_equals($h['qr_token'],trim($_POST['token']))&&!empty($_POST['idok'])){
  $pdo->prepare("UPDATE handovers SET status='verified',verified_by=?,verified_at=NOW() WHERE id=?")->execute([user()['id'],$h['id']]);
  $pdo->prepare("UPDATE items SET status='returned' WHERE id=?")->execute([$c['item_id']]);
  notify($pdo,$c['claimant_id'],'"'.$c['item_name'].'" marked as returned','/items/view.php?id='.$c['item_id']);$m='Verified. Item marked Returned.';
 }else $m='Invalid token, ID not confirmed, or already verified.';}
?><h2>Verify handover: <?=e($c['item_name'])?></h2><p><?=e($m)?></p>
<form method="post"><?=csrf()?><input name="token" placeholder="Paste or scan QR token" required><label><input type="checkbox" name="idok" value="1"> I checked the claimant's ID card</label><button>Confirm Handover</button></form>
<?php include '../includes/footer.php'; ?>
