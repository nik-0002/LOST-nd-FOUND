<?php require_once __DIR__.'/../config/db.php'; ?><!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Campus Lost &amp; Found</title><link rel="stylesheet" href="<?=BASE?>/assets/css/style.css"></head><body>
<nav><a href="<?=BASE?>/index.php"><b>Lost&amp;Found</b></a><a href="<?=BASE?>/items/list.php">Browse</a>
<?php if(user()): ?><a href="<?=BASE?>/items/report.php?type=lost">Report Lost</a><a href="<?=BASE?>/items/report.php?type=found">Report Found</a><a href="<?=BASE?>/claims/manage.php">Claims</a>
<?php if(user()['role']==='admin'): ?><a href="<?=BASE?>/admin/dashboard.php">Admin</a><?php endif; ?>
<span class="r"><a href="#" id="bell">🔔<span id="cnt"></span></a><a href="<?=BASE?>/auth/logout.php">Logout (<?=e(user()['name'])?>)</a></span>
<div id="nl"></div><?php else: ?><span class="r"><a href="<?=BASE?>/auth/login.php">Login</a><a href="<?=BASE?>/auth/register.php">Register</a></span><?php endif; ?></nav><main>
