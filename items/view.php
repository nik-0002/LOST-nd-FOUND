<?php include '../includes/header.php';
$s=$pdo->prepare('SELECT i.*,u.name uname FROM items i JOIN users u ON u.id=i.user_id WHERE i.id=?');$s->execute([(int)$_GET['id']]);$i=$s->fetch();if(!$i)die('Not found'); ?>
<h2><?=e($i['name'])?> <span class="badge <?=e($i['status'])?>"><?=e($i['status'])?></span></h2>
<?php if($i['image_path'])echo '<img style="max-width:300px" src="'.BASE.'/assets/uploads/'.e($i['image_path']).'">'; ?>
<p><b>Category:</b> <?=e($i['category'])?><br><b>Date:</b> <?=e($i['date_event'])?><br><b>Location:</b> <?=e($i['location_text'])?><br><b>Posted by:</b> <?=e($i['uname'])?></p><p><?=nl2br(e($i['description']))?></p>
<?php if(user()&&in_array($i['status'], ['found', 'lost'])&&user()['id']!=$i['user_id']): ?>
<h3><?= $i['type']==='found' ? 'Claim this item' : 'I found this item' ?></h3><form method="post" action="<?=BASE?>/claims/submit.php"><?=csrf()?><input type="hidden" name="item_id" value="<?=$i['id']?>"><textarea name="proof" placeholder="<?= $i['type']==='found' ? 'Prove ownership: colour, marks, contents...' : 'Where did you find it? Add details...' ?>" required></textarea><button><?= $i['type']==='found' ? 'Submit Claim' : 'Contact Owner' ?></button></form>
<?php elseif(!user()): ?><p><a href="<?=BASE?>/auth/login.php">Login</a> to claim.</p><?php endif; ?>
<?php include '../includes/footer.php'; ?>
