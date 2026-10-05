const bell=document.getElementById('bell');
if(bell){const nl=document.getElementById('nl');
 const poll=()=>fetch(BASE+'/notifications/fetch.php').then(r=>r.json()).then(d=>{cnt.textContent=d.unread?' '+d.unread:'';nl.innerHTML=d.items.map(n=>'<a href="'+n.link+'">'+(n.is_read==1?'':'• ')+n.message+'</a>').join('')||'<i>No notifications</i>'});
 bell.onclick=e=>{e.preventDefault();nl.style.display=nl.style.display==='block'?'none':'block';fetch(BASE+'/notifications/fetch.php?read=1').then(()=>setTimeout(poll,500))};
 poll();setInterval(poll,10000);}
