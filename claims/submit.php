<?php require_once '../config/db.php'; need(); check();
$s=$pdo->prepare("SELECT * FROM items WHERE id=? AND (status='found' OR status='lost')");$s->execute([(int)$_POST['item_id']]);$i=$s->fetch();
if(!$i||$i['user_id']==user()['id']||!trim($_POST['proof']))die('Invalid claim');
$pdo->prepare('INSERT INTO claims(item_id,claimant_id,proof_description)VALUES(?,?,?)')->execute([$i['id'],user()['id'],trim($_POST['proof'])]);
notify($pdo,$i['user_id'],'New claim on "'.$i['name'].'"','/claims/manage.php');
header('Location: manage.php');
