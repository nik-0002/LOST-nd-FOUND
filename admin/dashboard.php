<?php include '../includes/header.php'; need(true);
if($_SERVER['REQUEST_METHOD']==='POST'){check();$id=(int)$_POST['id'];
 if($_POST['act']==='block'&&$id!=user()['id'])$pdo->prepare("UPDATE users SET status=IF(status='active','blocked','active') WHERE id=?")->execute([$id]);
 if($_POST['act']==='deluser'&&$id!=user()['id'])$pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
 if($_POST['act']==='delitem')$pdo->prepare('DELETE FROM items WHERE id=?')->execute([$id]);
 if($_POST['act']==='setstatus'&&in_array($_POST['status'],['lost','found','claimed','returned']))$pdo->prepare('UPDATE items SET status=? WHERE id=?')->execute([$_POST['status'],$id]);
 if($_POST['act']==='claim'&&in_array($_POST['status'],['pending','approved','rejected']))$pdo->prepare('UPDATE claims SET status=? WHERE id=?')->execute([$_POST['status'],$id]);
 header('Location: dashboard.php');exit;}
$cnt=fn($t)=>$pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn(); ?>

<h2>⚙️ Admin Dashboard</h2>

<div class="stats">
  <div>
    <span style="font-size:1.5rem;display:block;margin-bottom:10px">👥</span>
    <b class="stat-number"><?=$cnt('users')?></b>
    <br><span style="font-family:'Syncopate',sans-serif;font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted)">Users</span>
  </div>
  <div>
    <span style="font-size:1.5rem;display:block;margin-bottom:10px">📦</span>
    <b class="stat-number"><?=$cnt('items')?></b>
    <br><span style="font-family:'Syncopate',sans-serif;font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted)">Items</span>
  </div>
  <div>
    <span style="font-size:1.5rem;display:block;margin-bottom:10px">✋</span>
    <b class="stat-number"><?=$cnt('claims')?></b>
    <br><span style="font-family:'Syncopate',sans-serif;font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted)">Claims</span>
  </div>
  <div>
    <span style="font-size:1.5rem;display:block;margin-bottom:10px">🤝</span>
    <b class="stat-number"><?=$cnt('handovers')?></b>
    <br><span style="font-family:'Syncopate',sans-serif;font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted)">Handovers</span>
  </div>
</div>

<h3>👥 Users</h3>
<table>
  <thead>
    <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach($pdo->query('SELECT * FROM users') as $x): ?>
    <tr>
      <td><?=e($x['name'])?></td>
      <td><?=e($x['email'])?></td>
      <td><span class="badge" style="position:relative;top:auto;right:auto;display:inline-block;background:rgba(255,255,255,0.1);font-size:0.55rem"><?=e($x['role'])?></span></td>
      <td><span style="color:<?=$x['status']==='active'?'var(--status-found)':'var(--status-lost)'?>"><?=e($x['status'])?></span></td>
      <td>
        <form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$x['id']?>">
          <button name="act" value="block" style="font-size:0.6rem;padding:6px 12px"><?=$x['status']==='active'?'🚫 Block':'✅ Unblock'?></button>
          <button name="act" value="deluser" class="danger" style="font-size:0.6rem;padding:6px 12px" onclick="return confirm('Delete this user?')">🗑️ Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<hr>

<h3>📦 Items</h3>
<table>
  <thead>
    <tr><th>Name</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach($pdo->query('SELECT * FROM items ORDER BY id DESC') as $x): ?>
    <tr>
      <td><a href="<?=BASE?>/items/view.php?id=<?=$x['id']?>"><?=e($x['name'])?></a></td>
      <td><span class="badge <?=e($x['status'])?>" style="position:relative;top:auto;right:auto;display:inline-block"><?=e($x['status'])?></span></td>
      <td>
        <form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$x['id']?>">
          <select name="status" style="padding:8px;font-size:0.8rem"><?php foreach(['lost','found','claimed','returned'] as $s)echo "<option".($s===$x['status']?' selected':'').">$s</option>"; ?></select>
          <button name="act" value="setstatus" style="font-size:0.6rem;padding:6px 12px">Set</button>
          <button name="act" value="delitem" class="danger" style="font-size:0.6rem;padding:6px 12px" onclick="return confirm('Delete this item?')">🗑️</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<hr>

<h3>✋ Claims (Override)</h3>
<table>
  <thead>
    <tr><th>Item</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach($pdo->query('SELECT c.id,c.status,i.name FROM claims c JOIN items i ON i.id=c.item_id ORDER BY c.id DESC') as $x): ?>
    <tr>
      <td><?=e($x['name'])?></td>
      <td><span class="badge <?=e($x['status'])?>" style="position:relative;top:auto;right:auto;display:inline-block"><?=e($x['status'])?></span></td>
      <td>
        <form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$x['id']?>">
          <select name="status" style="padding:8px;font-size:0.8rem"><option>pending</option><option>approved</option><option>rejected</option></select>
          <button name="act" value="claim" style="font-size:0.6rem;padding:6px 12px">Set</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include '../includes/footer.php'; ?>
