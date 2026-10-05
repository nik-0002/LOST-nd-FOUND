<?php require_once '../config/db.php'; need(); check(); $c=claimAccess($pdo,(int)$_POST['claim_id']); $b=trim($_POST['body']);
if($b!==''){$pdo->prepare('INSERT INTO messages(claim_id,sender_id,body)VALUES(?,?,?)')->execute([$c['id'],user()['id'],$b]);
 $to=user()['id']==$c['owner_id']?$c['claimant_id']:$c['owner_id'];notify($pdo,$to,'New message about "'.$c['item_name'].'"','/chat/chat.php?claim='.$c['id']);}
