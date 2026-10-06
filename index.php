<?php include 'includes/header.php';
$n=$pdo->query("SELECT status,COUNT(*) c FROM items GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$recent=$pdo->query("SELECT * FROM items ORDER BY created_at DESC LIMIT 6")->fetchAll();
$totalItems = array_sum($n ?: []);
$totalReturned = $n['returned'] ?? 0;
$successRate = $totalItems > 0 ? round(($totalReturned / $totalItems) * 100) : 0;
?>

<!-- Hero Section -->
<div class="hero">
  <h1>Intelligent Lost & Found</h1>
  <p>Seamlessly report, search, claim, and recover lost belongings across campus in real time.</p>
  
  <div class="hero-actions">
    <a href="<?=BASE?>/items/report.php?type=lost"><button style="background: var(--status-lost); border-color: transparent;">📢 Report Lost Item</button></a>
    <a href="<?=BASE?>/items/report.php?type=found"><button class="success" style="border-color: transparent;">🎉 Report Found Item</button></a>
    <a href="<?=BASE?>/items/list.php"><button class="secondary">🔍 Browse All Items</button></a>
  </div>
</div>

<!-- Quick Search -->
<div class="quick-search">
  <input type="text" id="quick-search" placeholder="Search for lost items... (⌘K)" autocomplete="off">
</div>

<!-- Campus Overview Stats -->
<h2>Campus Overview</h2>
<div class="stats">
  <?php 
  $statIcons = ['lost' => '📢', 'found' => '🎉', 'claimed' => '🤝', 'returned' => '✅'];
  foreach(['lost','found','claimed','returned'] as $s): ?>
    <div>
      <span style="font-size:1.5rem;display:block;margin-bottom:10px"><?=$statIcons[$s]?></span>
      <b class="stat-number"><?=$n[$s]??0?></b>
      <br><span style="font-family:'Syncopate',sans-serif;font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted)"><?=ucfirst($s)?></span>
    </div>
  <?php endforeach; ?>
</div>

<!-- How It Works -->
<h2>How It Works</h2>
<div class="how-it-works">
  <div class="how-step">
    <span class="step-number">01</span>
    <span class="step-icon">📝</span>
    <h3>Report</h3>
    <p>Lost something? Found an item? Report it in seconds with photos and location details.</p>
  </div>
  <div class="how-step">
    <span class="step-number">02</span>
    <span class="step-icon">🔍</span>
    <h3>Search & Match</h3>
    <p>Browse through reported items. Filter by category, date, location to find matches instantly.</p>
  </div>
  <div class="how-step">
    <span class="step-number">03</span>
    <span class="step-icon">✋</span>
    <h3>Claim & Verify</h3>
    <p>Submit a claim with proof of ownership. The finder verifies your identity and approves.</p>
  </div>
  <div class="how-step">
    <span class="step-number">04</span>
    <span class="step-icon">🤝</span>
    <h3>Handover</h3>
    <p>Meet up, scan the QR code, verify ID — and your item is returned. Simple and secure.</p>
  </div>
</div>

<!-- Success Rate Banner -->
<?php if($totalItems > 0): ?>
<div style="text-align:center;margin:60px 0 40px;animation:fadeInUp 0.8s ease-out both">
  <div style="display:inline-block;padding:30px 60px;background:rgba(0,255,204,0.05);border:1px solid rgba(0,255,204,0.2);border-radius:var(--radius);backdrop-filter:blur(10px)">
    <span style="font-family:'Syncopate',sans-serif;font-size:3rem;font-weight:700;color:var(--status-found);text-shadow:0 0 30px rgba(0,255,204,0.4)"><?=$successRate?>%</span>
    <br><span style="font-family:'Syncopate',sans-serif;font-size:0.8rem;text-transform:uppercase;letter-spacing:2px;color:var(--fg-muted)">Recovery Rate</span>
  </div>
</div>
<?php endif; ?>

<!-- Recent Reports -->
<div style="margin-top: 60px;">
  <div class="section-header">
    <h2>Recent Reports</h2>
    <a href="<?=BASE?>/items/list.php" class="view-all">View All →</a>
  </div>
  
  <div class="grid">
    <?php foreach($recent as $i): ?>
      <div class="card">
        <?php if($i['image_path']): ?>
          <img src="<?=BASE?>/assets/uploads/<?=e($i['image_path'])?>" alt="<?=e($i['name'])?>" loading="lazy">
        <?php else: ?>
          <div class="card-placeholder">📦</div>
        <?php endif; ?>
        <span class="badge <?=e($i['status'])?>"><?=e($i['status'])?></span>
        <h3><?=e($i['name'])?></h3>
        <p><?=e($i['category'])?> · <?=e($i['date_event'])?><br><?=e($i['location_text'])?></p>
        <a href="<?=BASE?>/items/view.php?id=<?=$i['id']?>">View Details →</a>
      </div>
    <?php endforeach; ?>
    <?php if(!$recent): ?>
      <div class="empty-state" style="grid-column:1/-1">
        <span class="icon">📭</span>
        <h3>No Items Yet</h3>
        <p>Be the first to report a lost or found item on campus!</p>
        <a href="<?=BASE?>/items/report.php?type=lost" style="margin-top:20px;display:inline-block"><button>Report Now</button></a>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
