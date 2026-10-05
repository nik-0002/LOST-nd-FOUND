<?php include '../includes/header.php'; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){check();
 $n=trim($_POST['name']);$em=trim($_POST['email']);$ph=trim($_POST['phone']);$r=$_POST['role'];$p=$_POST['password'];
 if(!$n||!filter_var($em,FILTER_VALIDATE_EMAIL)||!preg_match('/^\d{10}$/',$ph)||strlen($p)<6||!in_array($r,['student','faculty','staff']))$err='Check all fields (valid email, 10-digit phone, password 6+ chars).';
 else{try{$pdo->prepare('INSERT INTO users(name,email,phone,password_hash,role)VALUES(?,?,?,?,?)')->execute([$n,$em,$ph,password_hash($p,PASSWORD_DEFAULT),$r]);header('Location: login.php');exit;}catch(PDOException $x){$err='Email already registered.';}}}
?><h2>Register</h2><p class="err"><?=e($err)?></p><form method="post"><?=csrf()?>
<input name="name" placeholder="Full name" required><input name="email" type="email" placeholder="Email" required><input name="phone" placeholder="Phone (10 digits)" required>
<select name="role"><option>student</option><option>faculty</option><option>staff</option></select><input name="password" type="password" placeholder="Password" required><button>Register</button></form>
<?php include '../includes/footer.php'; ?>
