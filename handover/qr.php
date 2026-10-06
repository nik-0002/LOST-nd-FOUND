<?php include '../includes/header.php'; need(); $c=claimAccess($pdo,(int)$_GET['claim']);
if(user()['id']!=$c['claimant_id']&&user()['role']!=='admin')die('Only the approved claimant can view the QR');
$s=$pdo->prepare('SELECT * FROM handovers WHERE claim_id=?');$s->execute([$c['id']]);$h=$s->fetch();if(!$h||$c['status']!=='approved')die('Not approved'); ?>

<div style="margin-bottom:20px">
  <a href="<?=BASE?>/claims/manage.php" style="font-family:'Syncopate',sans-serif;font-size:0.75rem;text-transform:uppercase;letter-spacing:1px">← Back to Claims</a>
</div>

<h2>📱 Handover QR</h2>

<div style="text-align:center;padding:40px;background:rgba(255,255,255,0.03);border-radius:var(--radius);border:1px solid rgba(255,255,255,0.05);max-width:500px;margin:0 auto;animation:fadeInScale 0.8s ease-out">
  <h3 style="margin-bottom:20px"><?=e($c['item_name'])?></h3>
  
  <div id="qr" style="display:inline-block;padding:20px;background:#fff;border-radius:var(--radius-sm);margin-bottom:20px;animation:pulse 2s infinite"></div>
  
  <p style="margin-top:20px"><code style="background:rgba(255,255,255,0.1);padding:8px 16px;border-radius:8px;font-size:0.85rem;word-break:break-all"><?=e($h['qr_token'])?></code></p>
  
  <!-- Status Timeline -->
  <div class="status-timeline" style="max-width:300px;margin:30px auto">
    <div class="step complete">
      <div class="dot"></div>
      <span class="label">Claimed</span>
    </div>
    <div class="step active">
      <div class="dot"></div>
      <span class="label">Handover</span>
    </div>
    <div class="step">
      <div class="dot"></div>
      <span class="label">Returned</span>
    </div>
  </div>
  
  <div style="padding:20px;background:rgba(255,204,0,0.1);border:1px solid rgba(255,204,0,0.2);border-radius:var(--radius-sm);margin-top:20px">
    <p style="margin:0;color:var(--status-claimed)">⚠️ Show this QR code and your ID card to the finder or admin to complete the handover.</p>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>new QRCode(document.getElementById('qr'),{text:'<?=e($h['qr_token'])?>',width:200,height:200,colorDark:'#0a0b10',colorLight:'#ffffff'});</script>

<?php include '../includes/footer.php'; ?>
