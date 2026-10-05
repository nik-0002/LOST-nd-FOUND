<?php include '../includes/header.php'; need(); $c=claimAccess($pdo,(int)$_GET['claim']);
if(user()['id']!=$c['claimant_id']&&user()['role']!=='admin')die('Only the approved claimant can view the QR');
$s=$pdo->prepare('SELECT * FROM handovers WHERE claim_id=?');$s->execute([$c['id']]);$h=$s->fetch();if(!$h||$c['status']!=='approved')die('Not approved'); ?>
<h2>Handover QR for "<?=e($c['item_name'])?>"</h2><div id="qr"></div><p>Token: <code><?=e($h['qr_token'])?></code></p><p>Show this QR and your ID card to the finder/admin.</p>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script><script>new QRCode(document.getElementById('qr'),{text:'<?=e($h['qr_token'])?>',width:200,height:200});</script>
<?php include '../includes/footer.php'; ?>
