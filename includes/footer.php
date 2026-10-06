</main>
<footer class="footer">
  <p>© <?=date('Y')?> <strong>Campus Lost & Found</strong> — Built for the community</p>
  <p>
    <a href="<?=BASE?>/index.php">Home</a>
    <a href="<?=BASE?>/items/list.php">Browse Items</a>
    <?php if(user()): ?>
    <a href="<?=BASE?>/items/report.php?type=lost">Report Lost</a>
    <a href="<?=BASE?>/items/report.php?type=found">Report Found</a>
    <?php endif; ?>
  </p>
</footer>
<script>const BASE='<?=BASE?>';</script><script src="<?=BASE?>/assets/js/app.js"></script></body></html>
