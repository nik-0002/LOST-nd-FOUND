<?php
session_start();
$pdo=new PDO('mysql:host=localhost;dbname=lostfound;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
define('BASE','/lostfound');
define('MAPS_KEY','YOUR_GOOGLE_MAPS_KEY'); // restrict by HTTP referrer
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function csrf(){if(empty($_SESSION['t']))$_SESSION['t']=bin2hex(random_bytes(16));return '<input type="hidden" name="t" value="'.$_SESSION['t'].'">';}
function check(){if($_SERVER['REQUEST_METHOD']==='POST'&&!hash_equals($_SESSION['t']??'',$_POST['t']??''))die('Bad CSRF token');}
function user(){return $_SESSION['u']??null;}
function need($admin=false){if(!user()){header('Location:'.BASE.'/auth/login.php');exit;}if($admin&&user()['role']!=='admin')die('Forbidden');}
function notify($pdo,$uid,$msg,$link=''){$pdo->prepare('INSERT INTO notifications(user_id,message,link)VALUES(?,?,?)')->execute([$uid,$msg,$link]);}
function claimAccess($pdo,$cid){$s=$pdo->prepare('SELECT c.*,i.user_id owner_id,i.name item_name FROM claims c JOIN items i ON i.id=c.item_id WHERE c.id=?');$s->execute([$cid]);$c=$s->fetch();
 if(!$c)die('Not found');$u=user();if($u['role']!=='admin'&&$u['id']!=$c['claimant_id']&&$u['id']!=$c['owner_id'])die('Forbidden');return $c;}
