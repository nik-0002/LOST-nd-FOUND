<?php include '../includes/header.php'; need(true);
if($_SERVER['REQUEST_METHOD']==='POST'){check();$id=(int)$_POST['id'];
 if($_POST['act']==='block'&&$id!=user()['id'])$pdo->prepare("UPDATE users SET status=IF(status='active','blocked','active') WHERE id=?")->execute([$id]);
 if($_POST['act']==='deluser'&&$id!=user()['id'])$pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
 if($_POST['act']==='delitem')$pdo->prepare('DELETE FROM items WHERE id=?')->execute([$id]);
 if($_POST['act']==='setstatus'&&in_array($_POST['status'],['lost','found','claimed','returned']))$pdo->prepare('UPDATE items SET status=? WHERE id=?')->execute([$_POST['status'],$id]);
 if($_POST['act']==='claim'&&in_array($_POST['status'],['pending','approved','rejected']))$pdo->prepare('UPDATE claims SET status=? WHERE id=?')->execute([$_POST['status'],$id]);
 header('Location: dashboard.php');exit;}
$cnt=fn($t)=>$pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn(); ?>
<h2>Admin Dashboard</h2><div class="stats"><div><b><?=$cnt('users')?></b><br>Users</div><div><b><?=$cnt('items')?></b><br>Items</div><div><b><?=$cnt('claims')?></b><br>Claims</div><div><b><?=$cnt('handovers')?></b><br>Handovers</div></div>
<h3>Users</h3><table><?php foreach($pdo->query('SELECT * FROM users') as $x): ?><tr><td><?=e($x['name'])?></td><td><?=e($x['email'])?></td><td><?=e($x['role'])?></td><td><?=e($x['status'])?></td>
<td><form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$x['id']?>"><button name="act" value="block">Block/Unblock</button> <button name="act" value="deluser" onclick="return confirm('Delete?')">Delete</button></form></td></tr><?php endforeach; ?></table>
<h3>Items</h3><table><?php foreach($pdo->query('SELECT * FROM items ORDER BY id DESC') as $x): ?><tr><td><?=e($x['name'])?></td><td><?=e($x['status'])?></td>
<td><form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$x['id']?>"><select name="status"><?php foreach(['lost','found','claimed','returned'] as $s)echo "<option>$s</option>"; ?></select><button name="act" value="setstatus">Set</button> <button name="act" value="delitem" onclick="return confirm('Delete?')">Delete</button></form></td></tr><?php endforeach; ?></table>
<h3>Claims (override)</h3><table><?php foreach($pdo->query('SELECT c.id,c.status,i.name FROM claims c JOIN items i ON i.id=c.item_id ORDER BY c.id DESC') as $x): ?><tr><td><?=e($x['name'])?></td><td><?=e($x['status'])?></td>
<td><form method="post"><?=csrf()?><input type="hidden" name="id" value="<?=$x['id']?>"><select name="status"><option>pending</option><option>approved</option><option>rejected</option></select><button name="act" value="claim">Set</button></form></td></tr><?php endforeach; ?></table>
<?php include '../includes/footer.php'; ?>
