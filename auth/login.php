<?php include '../includes/header.php'; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){check();
 $s=$pdo->prepare('SELECT * FROM users WHERE email=?');$s->execute([trim($_POST['email'])]);$u=$s->fetch();
 if($u&&password_verify($_POST['password'],$u['password_hash'])){ if($u['status']==='blocked')$err='Account blocked by administrator.'; else{session_regenerate_id(true);$_SESSION['u']=['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role']];header('Location: '.BASE.'/index.php');exit;}}
 else $err='Invalid email or password.';}
?>

<div style="max-width:500px;margin:60px auto;text-align:center">
  <span style="font-size:3rem;display:inline-block;margin-bottom:20px;animation:orbitFloat 4s ease-in-out infinite">🔐</span>
  <h2 style="text-align:center">Welcome Back</h2>
  <p style="text-align:center;margin-bottom:30px">Sign in to report items, manage claims, and chat.</p>
  
  <?php if($err): ?><p class="err"><?=e($err)?></p><?php endif; ?>
  
  <form method="post" style="max-width:100%">
    <?=csrf()?>
    <div class="form-group">
      <input name="email" type="email" placeholder="Email address" required autofocus>
      <label>Email</label>
    </div>
    <div class="form-group">
      <input name="password" type="password" placeholder="Password" required>
      <label>Password</label>
    </div>
    <button style="width:100%">🔓 Login</button>
    <p style="text-align:center;margin-top:20px">Don't have an account? <a href="<?=BASE?>/auth/register.php">Register →</a></p>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
