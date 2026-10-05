<?php include '../includes/header.php'; need(); $c=claimAccess($pdo,(int)$_GET['claim']); ?>
<h2>Chat: <?=e($c['item_name'])?></h2><div id="box" class="chat"></div>
<form id="cf"><?=csrf()?><input type="hidden" name="claim_id" value="<?=$c['id']?>"><input name="body" placeholder="Type a message" autocomplete="off" required><button>Send</button></form>
<script>const cid=<?=$c['id']?>,me=<?=user()['id']?>;
function load(){fetch('fetch.php?claim='+cid).then(r=>r.json()).then(m=>{box.innerHTML=m.map(x=>'<p class="'+(x.sender_id==me?'me':'')+'"><b>'+x.name+':</b> '+x.body+'</p>').join('');box.scrollTop=box.scrollHeight})}
cf.onsubmit=e=>{e.preventDefault();fetch('send.php',{method:'POST',body:new FormData(cf)}).then(()=>{cf.body.value='';load()})};load();setInterval(load,3000);</script>
<?php include '../includes/footer.php'; ?>
