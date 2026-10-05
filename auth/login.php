<?php include '../includes/header.php'; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){check();
 $s=$pdo->prepare('SELECT * FROM users WHERE email=?');$s->execute([trim($_POST['email'])]);$u=$s->fetch();
 if($u&&password_verify($_POST['password'],$u['password_hash'])){ if($u['status']==='blocked')$err='Account blocked.'; else{session_regenerate_id(true);$_SESSION['u']=['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role']];header('Location: '.BASE.'/index.php');exit;}}
 else $err='Invalid credentials.';}
?><h2>Login</h2><p class="err"><?=e($err)?></p><form method="post"><?=csrf()?><input name="email" type="email" placeholder="Email" required><input name="password" type="password" placeholder="Password" required><button>Login</button></form>
<?php include '../includes/footer.php'; ?>
