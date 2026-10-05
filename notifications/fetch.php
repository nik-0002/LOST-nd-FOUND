<?php require_once '../config/db.php'; need(); header('Content-Type: application/json');
if(isset($_GET['read']))$pdo->prepare('UPDATE notifications SET is_read=1 WHERE user_id=?')->execute([user()['id']]);
$s=$pdo->prepare('SELECT message,link,is_read FROM notifications WHERE user_id=? ORDER BY id DESC LIMIT 10');$s->execute([user()['id']]);$r=$s->fetchAll();
$u=$pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0');$u->execute([user()['id']]);
foreach($r as &$n){$n['message']=e($n['message']);$n['link']=BASE.$n['link'];}
echo json_encode(['unread'=>(int)$u->fetchColumn(),'items'=>$r]);
