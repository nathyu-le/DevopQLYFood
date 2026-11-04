'use strict';
let snapshot = null;
let logFilters = {query: '', since: 0};
const byId = id => document.getElementById(id);
const time = value => new Date(value).toLocaleString('vi-VN', {timeZone: 'Asia/Ho_Chi_Minh', hour12: false});
const number = (value, unit) => Number.isFinite(value) ? `${value.toFixed(1)}${unit}` : 'Chưa có';
function chart(id, history, key, color) {
  const svg = byId(id), ns = 'http://www.w3.org/2000/svg';
  svg.replaceChildren();
  for (const y of [15,75,135]) {
    const line = document.createElementNS(ns, 'line');
    for (const [k,v] of Object.entries({x1:0,x2:600,y1:y,y2:y,stroke:'#edf1ee'})) line.setAttribute(k,v);
    svg.append(line);
  }
  const points = history.slice(-60); let part = [];
  function draw() {
    if (part.length) { const line = document.createElementNS(ns,'polyline'); line.setAttribute('points',part.join(' ')); line.setAttribute('fill','none');line.setAttribute('stroke',color);line.setAttribute('stroke-width','2.5');svg.append(line); }
    part=[];
  }
  points.forEach((p,i) => { if (!Number.isFinite(p[key])) {draw();return;} part.push(`${i*600/Math.max(1,points.length-1)},${135-Math.max(0,Math.min(100,p[key]))*1.2}`); });draw();
}
function logs() {
  if (!snapshot) return;
  const {query, since}=logFilters;
  const body=byId('logs'); body.replaceChildren();
  const rows=snapshot.logs.slice().reverse().filter(row => JSON.stringify(row).toLowerCase().includes(query) && new Date(row.time).getTime()>=since);
  if (!rows.length) {
    const tr=document.createElement('tr'), td=document.createElement('td');
    td.colSpan=3;td.className='empty-log';td.textContent='Không có nhật ký phù hợp với bộ lọc.';tr.append(td);body.append(tr);
  }
  rows.forEach(row => {
    const tr=document.createElement('tr');const {time:stamp,event,...fields}=row;
    for (const text of [time(stamp),event,JSON.stringify(fields)]) { const td=document.createElement('td');td.textContent=text;tr.append(td); }
    body.append(tr);
  });
}
function render(data) {
  snapshot=data; const x=data.latest;
  byId('cpu').textContent=number(x.cpu_percent,'%');byId('cpu-source').textContent=x.cpu_source;
  byId('memory').textContent=number(x.memory_mb,' MB');byId('memory-source').textContent=x.memory_source;
  byId('health').textContent=x.health==='ok'?'Hoạt động':'Gián đoạn';byId('health').style.color=x.health==='ok'?'#276347':'#ad3630';
  byId('latency').textContent=number(x.db_ms,' ms');byId('updated').textContent='Mẫu gần nhất: '+time(x.time);
  byId('revision').textContent='Revision: '+(data.revision||'local').slice(0,12);
  chart('cpu-chart',data.history,'cpu_percent','#2a785e');chart('memory-chart',data.history,'memory_percent','#c78d38');
  const names={database_unavailable:'Database không khả dụng',cpu_high:'CPU vượt 85%',memory_high:'RAM vượt 85%'};
  const active=Object.entries(data.alerts).filter(([,a])=>a.state==='firing');
  byId('alert-banner').className='banner'+(active.length?' bad':'');
  byId('alert-banner').textContent=active.length?'CẢNH BÁO ĐANG HOẠT ĐỘNG · '+active.map(([name])=>names[name]).join(' · '):'Hệ thống đang được theo dõi · Không có cảnh báo firing';
  byId('alert-state').textContent=active.length+' FIRING';
  const alerts=byId('alerts');alerts.replaceChildren();
  for (const [name,a] of Object.entries(data.alerts)) {
    const row=document.createElement('div');row.className='alert-row';
    const title=document.createElement('span');title.textContent=names[name];
    const state=document.createElement('span');state.className='pill '+a.state;state.textContent=a.state.toUpperCase();state.title='Đổi trạng thái: '+time(a.changed_at);
    row.append(title,state);alerts.append(row);
  }
  const b=data.last_backup;
  byId('backup-status').textContent=b?`${time(b.created_at)} · ${b.row_count} bản ghi\n${b.file}\nSHA256: ${b.sha256.slice(0,20)}…`:'Chưa có snapshot trong bộ lưu trữ này.';
  logs();document.documentElement.dataset.opsReady='true';document.documentElement.dataset.alertState=active.length?'firing':'ok';
}
let busy=false;
async function refresh() {
  if(busy)return;busy=true;
  try { const response=await fetch('ops-api.php',{cache:'no-store'});if(response.status===401){location.href='login.php';return;}if(!response.ok)throw Error('Không lấy được dữ liệu giám sát.');render(await response.json()); }
  catch(error){byId('alert-banner').className='banner bad';byId('alert-banner').textContent=error.message;}
  finally{busy=false;}
}
byId('log-filters').addEventListener('submit',event=>{
  event.preventDefault();
  const value=byId('log-since').value;
  logFilters={query:byId('log-filter').value.trim().toLowerCase(),since:value ? new Date(value+'+07:00').getTime() : 0};
  logs();
});
byId('log-filters').addEventListener('reset',()=>{
  logFilters={query:'',since:0};
  logs();
});
byId('backup-button').addEventListener('click',async()=>{
  const button=byId('backup-button');button.disabled=true;
  try {
    const body=new URLSearchParams({action:'backup',csrf:byId('ops-csrf').value});
    const response=await fetch('ops-api.php',{method:'POST',body});
    if(!response.ok)throw Error(response.status===429?'Vui lòng chờ 60 giây giữa hai lần backup.':'Backup chưa thành công; kiểm tra log và cấu hình riêng.');
    await response.json();await refresh();
  }catch(error){byId('backup-status').textContent=error.message;}
  finally{button.disabled=false;}
});
refresh();setInterval(refresh,5000);
