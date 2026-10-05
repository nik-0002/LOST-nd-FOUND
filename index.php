<?php include 'includes/header.php';
$n=$pdo->query("SELECT status,COUNT(*) c FROM items GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR); ?>
<h1>Intelligent Lost and Found Management System</h1><p>Report, search, claim and recover belongings on campus.</p>
<div class="stats"><?php foreach(['lost','found','claimed','returned'] as $s): ?><div><b><?=$n[$s]??0?></b><br><?=ucfirst($s)?></div><?php endforeach; ?></div>
<?php include 'includes/footer.php'; ?>
