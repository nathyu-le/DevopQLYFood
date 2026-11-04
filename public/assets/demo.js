'use strict';
const $=id=>document.getElementById(id);
const stamp=x=>new Date(x).toLocaleString('vi-VN',{timeZone:'Asia/Ho_Chi_Minh',hour12:false});
let busy=false, current=null;
const names={demo_data_created:'Đã tạo món thử',demo_backup_created:'Đã tạo backup',demo_data_deleted:'Đã xóa dữ liệu thử',demo_restore_verified:'Restore khớp backup',demo_database_disconnected:'Đã ngắt kết nối DB demo',demo_database_reconnected:'Đã phục hồi kết nối',demo_auto_reconnected:'Đã tự phục hồi kết nối',demo_alert_transition:'Cảnh báo đổi trạng thái'};
function row(body,values){const tr=document.createElement('tr');for(const value of values){const td=document.createElement('td');td.textContent=String(value);tr.append(td);}body.append(tr);}
function empty(body,text){const tr=document.createElement('tr'),td=document.createElement('td');td.colSpan=4;td.className='empty';td.textContent=text;tr.append(td);body.append(tr);}
function render(data){
 current=data;const ready=data.initialized,online=ready&&data.probe.health==='ok',state=data.alert.state;
 $('demo-db').textContent=!ready?'Chưa chuẩn bị':online?'Hoạt động':'Mất kết nối';
 $('demo-time').textContent='Kiểm tra: '+new Date().toLocaleTimeString('vi-VN');
 $('demo-alert').textContent=ready?state.toUpperCase():'Chưa bắt đầu';$('demo-alert').className=state;
 $('demo-counts').textContent=ready?`Mẫu lỗi: ${data.alert.failures} · Mẫu tốt: ${data.alert.successes}`:'Cập nhật mỗi 5 giây';
 $('demo-match').textContent=data.restored?'KHỚP BACKUP':'Chưa restore';
 const table=$('demo-rows');table.replaceChildren();
 if(!ready)empty(table,'Bấm 1 Tạo dữ liệu thử để bắt đầu.');
 else if(!online)empty(table,'Database demo đang mất kết nối.');
 else if(!data.probe.rows.length)empty(table,'Dữ liệu thử đã bị xóa. Bấm 4 Restore để phục hồi.');
 else for(const x of data.probe.rows)row(table,[x.id,x.name,Number(x.price).toLocaleString('vi-VN')+' đ',x.is_available?'Đang phục vụ':'Tạm ngừng']);
 const b=data.backup;$('demo-backup').textContent=b?`Backup: ${stamp(b.created_at)} · ${b.row_count} bản ghi · SHA256: ${b.sha256}`:'Chưa có backup của lượt demo này.';
 const log=$('demo-events');log.replaceChildren();for(const e of (data.events||[]).slice().reverse())row(log,[stamp(e.time),names[e.event]||e.event,e.state?e.state.toUpperCase():e.row_count!==undefined?e.row_count+' bản ghi':'Đã thực hiện']);
 if(!data.events.length)empty(log,'Chưa có thao tác demo.');
 for(const btn of document.querySelectorAll('[data-action]')){const a=btn.dataset.action;btn.disabled=busy||!data.driver_available||(a==='prepare'&&ready&&!online)||(['backup','delete','restore','disconnect'].includes(a)&&!online)||(['delete','restore','download'].includes(a)&&!b)||(a==='reconnect'&&(!ready||online));}
 if(!data.driver_available){$('message').className='banner bad';$('message').textContent='Hosting cần bật extension pdo_sqlite. Vào Select PHP Version → Extensions để bật, rồi tải lại trang.';}
 else if(state==='firing'){$('message').className='banner bad';$('message').textContent=online?'Kết nối đã phục hồi. Chờ mẫu tốt tiếp theo để cảnh báo về OK.':'FIRING · Database demo mất kết nối. Bấm 6 Phục hồi kết nối.';}
 else if(state==='pending'){$('message').className='banner bad';$('message').textContent='Đã phát hiện lỗi. Chờ mẫu lỗi tiếp theo để cảnh báo chuyển sang FIRING.';}
 else if(online){$('message').className='banner';$('message').textContent=data.restored?'Dữ liệu restore đã khớp backup. Tiếp tục thử tạo lỗi và phục hồi kết nối.':'Database demo đang hoạt động. Thực hiện lần lượt các nút theo số.';}
}
async function refresh(){if(busy)return;busy=true;try{const response=await fetch('demo-api.php',{cache:'no-store'});if(response.status===401){location.href='login.php';return;}if(!response.ok)throw Error('Chưa lấy được trạng thái demo.');const data=await response.json();const health=await fetch('demo-health.php',{cache:'no-store'});$('demo-http').textContent=String(health.status);busy=false;render(data);}catch(e){$('message').className='banner bad';$('message').textContent=e.message;}finally{busy=false;}}
for(const button of document.querySelectorAll('[data-action]'))button.addEventListener('click',async()=>{
 if(busy)return;busy=true;for(const b of document.querySelectorAll('[data-action]'))b.disabled=true;
 try{const action=button.dataset.action,response=await fetch('demo-api.php',{method:'POST',body:new URLSearchParams({action,csrf:$('demo-csrf').value})});if(response.status===401){location.href='login.php';return;}if(!response.ok){const err=await response.json();throw Error(err.error||'Thao tác chưa hoàn tất.');}
 if(action==='download'){const blob=await response.blob(),url=URL.createObjectURL(blob),a=document.createElement('a');a.href=url;a.download='week3-demo-backup.json';a.click();URL.revokeObjectURL(url);}else await response.json();
 busy=false;await refresh();
 }catch(e){$('message').className='banner bad';$('message').textContent=e.message;if(current){busy=false;for(const b of document.querySelectorAll('[data-action]'))b.disabled=false;}}finally{busy=false;}
});
refresh();setInterval(refresh,5000);
