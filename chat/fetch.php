<?php require_once '../config/db.php'; need(); $c=claimAccess($pdo,(int)$_GET['claim']);
$s=$pdo->prepare('SELECT m.sender_id,u.name,m.body FROM messages m JOIN users u ON u.id=m.sender_id WHERE claim_id=? ORDER BY m.id');$s->execute([$c['id']]);
$r=$s->fetchAll();foreach($r as &$m){$m['name']=e($m['name']);$m['body']=e($m['body']);}
header('Content-Type: application/json');echo json_encode($r);
