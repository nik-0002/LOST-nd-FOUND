<?php include '../includes/header.php'; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){check();
 $n=trim($_POST['name']);$em=trim($_POST['email']);$ph=trim($_POST['phone']);$r=$_POST['role'];$p=$_POST['password'];
 if(!$n||!filter_var($em,FILTER_VALIDATE_EMAIL)||!preg_match('/^\d{10}$/',$ph)||strlen($p)<6||!in_array($r,['student','faculty','staff']))$err='Check all fields (valid email, 10-digit phone, password 6+ chars).';
 else{try{$pdo->prepare('INSERT INTO users(name,email,phone,password_hash,role)VALUES(?,?,?,?,?)')->execute([$n,$em,$ph,password_hash($p,PASSWORD_DEFAULT),$r]);header('Location: login.php');exit;}catch(PDOException $x){$err='Email already registered.';}}}
?>

<div style="max-width:500px;margin:60px auto;text-align:center">
  <span style="font-size:3rem;display:inline-block;margin-bottom:20px;animation:orbitFloat 4s ease-in-out infinite">✨</span>
  <h2 style="text-align:center">Join Campus L&F</h2>
  <p style="text-align:center;margin-bottom:30px">Create your account to start reporting and claiming items.</p>
  
  <?php if($err): ?><p class="err"><?=e($err)?></p><?php endif; ?>
  
  <form method="post" style="max-width:100%">
    <?=csrf()?>
    <div class="form-group">
      <input name="name" placeholder="Full name" required value="<?=e($_POST['name']??'')?>">
      <label>Full Name</label>
    </div>
    <div class="form-group">
      <input name="email" type="email" placeholder="Email address" required value="<?=e($_POST['email']??'')?>">
      <label>Email</label>
    </div>
    <div class="form-group">
      <input name="phone" placeholder="Phone (10 digits)" required value="<?=e($_POST['phone']??'')?>">
      <label>Phone</label>
    </div>
    <select name="role">
      <option value="student">👨‍🎓 Student</option>
      <option value="faculty">👨‍🏫 Faculty</option>
      <option value="staff">🛠️ Staff</option>
    </select>
    <div class="form-group">
      <input name="password" type="password" placeholder="Password (6+ characters)" required>
      <label>Password</label>
    </div>
    <button style="width:100%">✨ Create Account</button>
    <p style="text-align:center;margin-top:20px">Already have an account? <a href="<?=BASE?>/auth/login.php">Login →</a></p>
  </form>
</div>

<?php include '../includes/footer.php'; ?>
