<?php include '../includes/header.php'; need(); $c=claimAccess($pdo,(int)$_GET['claim']); $m=''; $success=false;
if(user()['id']!=$c['owner_id']&&user()['role']!=='admin')die('Only finder/admin can verify');
if($_SERVER['REQUEST_METHOD']==='POST'){check();
 $s=$pdo->prepare("SELECT * FROM handovers WHERE claim_id=? AND status='pending'");$s->execute([$c['id']]);$h=$s->fetch();
 if($h&&hash_equals($h['qr_token'],trim($_POST['token']))&&!empty($_POST['idok'])){
  $pdo->prepare("UPDATE handovers SET status='verified',verified_by=?,verified_at=NOW() WHERE id=?")->execute([user()['id'],$h['id']]);
  $pdo->prepare("UPDATE items SET status='returned' WHERE id=?")->execute([$c['item_id']]);
  notify($pdo,$c['claimant_id'],'"'.$c['item_name'].'" marked as returned','/items/view.php?id='.$c['item_id']);
  $m='✅ Verified! Item has been marked as Returned.'; $success=true;
 }else $m='❌ Invalid token, ID not confirmed, or already verified.';}
?>

<div style="margin-bottom:20px">
  <a href="<?=BASE?>/claims/manage.php" style="font-family:'Syncopate',sans-serif;font-size:0.75rem;text-transform:uppercase;letter-spacing:1px">← Back to Claims</a>
</div>

<h2>🔐 Verify Handover</h2>

<div style="max-width:500px;margin:0 auto">
  <div style="text-align:center;padding:30px;background:rgba(255,255,255,0.03);border-radius:var(--radius);border:1px solid rgba(255,255,255,0.05);margin-bottom:30px">
    <span style="font-size:2.5rem;display:block;margin-bottom:15px">🤝</span>
    <h3><?=e($c['item_name'])?></h3>
  </div>
  
  <?php if($m): ?>
    <p class="<?=$success?'success-msg':'err'?>"><?=e($m)?></p>
  <?php endif; ?>
  
  <?php if(!$success): ?>
  <form method="post">
    <?=csrf()?>
    <div class="form-group">
      <input name="token" placeholder="Paste or scan QR token" required>
      <label>QR Token</label>
    </div>
    
    <label style="display:flex;align-items:center;gap:12px;cursor:pointer;padding:16px;background:rgba(255,255,255,0.03);border-radius:var(--radius-sm);border:1px solid rgba(255,255,255,0.05)">
      <input type="checkbox" name="idok" value="1" style="width:auto;box-shadow:none" required>
      <span style="font-family:'Space Grotesk',sans-serif;font-size:0.95rem;color:var(--fg)">I have checked the claimant's ID card</span>
    </label>
    
    <button class="success" style="width:100%">🔐 Confirm Handover</button>
  </form>
  <?php else: ?>
  <div style="text-align:center;padding:40px">
    <span style="font-size:4rem;display:block;animation:successPop 0.5s ease-out">🎉</span>
    <h3 style="margin-top:20px">Handover Complete!</h3>
    <p>The item has been successfully returned to its owner.</p>
    <a href="<?=BASE?>/claims/manage.php" style="display:inline-block;margin-top:20px"><button class="secondary">← Back to Claims</button></a>
  </div>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
