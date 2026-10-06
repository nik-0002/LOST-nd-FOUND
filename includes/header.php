<?php require_once __DIR__.'/../config/db.php'; ?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="Campus Lost &amp; Found — Report, search, claim, and recover lost belongings in real time."><title>Campus Lost &amp; Found</title><link rel="stylesheet" href="<?=BASE?>/assets/css/style.css"><link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔍</text></svg>"></head><body>
<canvas id="particles"></canvas>
<nav><a href="<?=BASE?>/index.php"><b>Lost&amp;Found</b></a><a href="<?=BASE?>/items/list.php">Browse</a>
<?php if(user()): ?><a href="<?=BASE?>/items/report.php?type=lost">Report Lost</a><a href="<?=BASE?>/items/report.php?type=found">Report Found</a><a href="<?=BASE?>/claims/manage.php">Claims</a>
<?php if(user()['role']==='admin'): ?><a href="<?=BASE?>/admin/dashboard.php">Admin</a><?php endif; ?>
<span class="r"><a href="#" id="bell">🔔<span id="cnt"></span></a><a href="<?=BASE?>/auth/logout.php">Logout (<?=e(user()['name'])?>)</a></span>
<div id="nl"></div><?php else: ?><span class="r"><a href="<?=BASE?>/auth/login.php">Login</a><a href="<?=BASE?>/auth/register.php">Register</a></span><?php endif; ?></nav><main>
