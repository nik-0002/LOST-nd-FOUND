<?php include '../includes/header.php'; need(); $c=claimAccess($pdo,(int)$_GET['claim']); ?>

<div style="margin-bottom:20px">
  <a href="<?=BASE?>/claims/manage.php" style="font-family:'Syncopate',sans-serif;font-size:0.75rem;text-transform:uppercase;letter-spacing:1px">← Back to Claims</a>
</div>

<h2>💬 Chat: <?=e($c['item_name'])?></h2>

<div id="box" class="chat"></div>

<form id="cf" style="flex-direction:row;margin-top:15px">
  <?=csrf()?>
  <input type="hidden" name="claim_id" value="<?=$c['id']?>">
  <input name="body" placeholder="Type a message..." autocomplete="off" required style="flex:1">
  <button style="padding:16px 32px">Send ↗</button>
</form>

<script>
const cid=<?=$c['id']?>,me=<?=user()['id']?>;
let lastCount = 0;

function load() {
  fetch('fetch.php?claim='+cid)
    .then(r => r.json())
    .then(m => {
      // Only re-render if count changed
      if (m.length !== lastCount) {
        box.innerHTML = m.map((x, i) => {
          const isMe = x.sender_id == me;
          const time = x.time || '';
          return `<p class="${isMe ? 'me' : ''}" style="animation-delay:${Math.max(0, (i - lastCount + 3)) * 0.05}s">
            <b>${x.name}:</b> ${x.body}
            <br><small style="opacity:0.5;font-size:0.75rem">${time}</small>
          </p>`;
        }).join('');
        box.scrollTop = box.scrollHeight;
        lastCount = m.length;
      }
    });
}

cf.onsubmit = e => {
  e.preventDefault();
  const body = cf.body.value.trim();
  if (!body) return;
  
  // Optimistic UI
  const tempMsg = document.createElement('p');
  tempMsg.className = 'me';
  tempMsg.style.animation = 'fadeInUp 0.3s ease-out';
  tempMsg.innerHTML = `<b>You:</b> ${body}<br><small style="opacity:0.5;font-size:0.75rem">Sending...</small>`;
  box.appendChild(tempMsg);
  box.scrollTop = box.scrollHeight;
  cf.body.value = '';
  
  fetch('send.php', {method:'POST', body: new FormData(cf)})
    .then(() => { load(); })
    .catch(() => { tempMsg.innerHTML += ' <span style="color:var(--status-lost)">(failed)</span>'; });
};

load();
setInterval(load, 3000);

// Auto-resize input on focus
cf.body.addEventListener('keydown', (e) => {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    cf.dispatchEvent(new Event('submit'));
  }
});
</script>

<?php include '../includes/footer.php'; ?>
