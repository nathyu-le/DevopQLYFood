// Reproducible recording: public live endpoints and disposable synthetic demo only.
// Never records a login form with credentials, OS metrics, config files or real DB rows.
import fs from 'node:fs/promises';
import os from 'node:os';
import path from 'node:path';
import {spawn,spawnSync} from 'node:child_process';
if(process.env.GITHUB_ACTIONS!=='true'||!process.env.COMPOSE_PROJECT_NAME?.endsWith('_ci'))throw Error('Disposable CI recording only.');
const out='week4-video';await fs.mkdir(out,{recursive:true});
const base='http://127.0.0.1:8080/',live='https://khotrick.org/DevopQLYFood/public/';
const chapters=JSON.parse(await fs.readFile('docs/week4-video-narration.json','utf8'));
const delay=ms=>new Promise(r=>setTimeout(r,ms));
const run=(cmd,args)=>{const p=spawnSync(cmd,args,{stdio:'pipe'});if(p.status!==0)throw Error(cmd+' failed; private output omitted.');return p.stdout.toString();};
for(let i=0;i<chapters.length;i++){
 const f=`${out}/speech-${i}.txt`,wav=`${out}/speech-${i}.wav`,audio=`${out}/audio-${i}.wav`;
 await fs.writeFile(f,chapters[i].speech);run('espeak-ng',['-v','vi','-s','148','-f',f,'-w',wav]);
 const seconds=Number(run('ffprobe',['-v','error','-show_entries','format=duration','-of','csv=p=0',wav]).trim());
 if(!Number.isFinite(seconds)||seconds>38)throw Error('Narration exceeds its 40 second chapter.');
 run('ffmpeg',['-y','-v','error','-i',wav,'-af','apad','-t','40','-ar','22050','-ac','1',audio]);
}
await fs.writeFile(`${out}/audio-list.txt`,chapters.map((_,i)=>`file 'audio-${i}.wav'`).join('\n'));
run('ffmpeg',['-y','-v','error','-f','concat','-safe','0','-i',`${out}/audio-list.txt`,'-c','copy',`${out}/narration.wav`]);
const pass=(await fs.readFile('.secrets/admin_password','utf8')).trim();
const cookies=new Map();
async function request(route,fields){const r=await fetch(base+route,{redirect:'manual',method:fields?'POST':'GET',body:fields?new URLSearchParams(fields):undefined,headers:{Cookie:[...cookies].map(([k,v])=>`${k}=${v}`).join('; ')}});for(const c of r.headers.getSetCookie()){const p=c.split(';')[0],i=p.indexOf('=');cookies.set(p.slice(0,i),p.slice(i+1));}return r;}
let r=await request('login.php'),html=await r.text(),csrf=html.match(/name="csrf" value="([a-f0-9]+)"/)?.[1];
if(!csrf||(await request('login.php',{csrf,username:'admin',password:pass})).status!==303)throw Error('Disposable login failed.');
r=await request('demo.php');csrf=(await r.text()).match(/id="demo-csrf" value="([a-f0-9]+)"/)?.[1];
async function action(action){const r=await request('demo-api.php',{csrf,action});if(r.status!==200)throw Error('Demo action failed: '+action);return await r.json();}
await action('prepare');
const profile=await fs.mkdtemp(path.join(os.tmpdir(),'final-video-'));
const chrome=spawn('google-chrome',['--headless=new','--no-sandbox','--disable-dev-shm-usage','--remote-debugging-port=0',`--user-data-dir=${profile}`,'about:blank'],{stdio:'ignore'});
let ws,encoder;let recording=false,captureTask;const events=[];
try{
 let port;for(let i=0;i<100;i++){try{port=(await fs.readFile(path.join(profile,'DevToolsActivePort'),'utf8')).split('\n')[0];break;}catch{await delay(100);}}
 if(!port)throw Error('Chrome unavailable.');
 const t=await(await fetch(`http://127.0.0.1:${port}/json/new?about:blank`,{method:'PUT'})).json();ws=new WebSocket(t.webSocketDebuggerUrl);await new Promise((ok,no)=>{ws.addEventListener('open',ok,{once:true});ws.addEventListener('error',no,{once:true});});
 let seq=0;const waiting=new Map();ws.addEventListener('message',e=>{const d=JSON.parse(e.data),p=waiting.get(d.id);if(p){waiting.delete(d.id);d.error?p.reject(Error(d.error.message)):p.resolve(d.result);}});
 const call=(method,params={})=>new Promise((resolve,reject)=>{const id=++seq;waiting.set(id,{resolve,reject});ws.send(JSON.stringify({id,method,params}));});
 const evaluate=async expression=>(await call('Runtime.evaluate',{expression,returnByValue:true,awaitPromise:true})).result?.value;
 await call('Network.enable');await call('Network.setCookie',{name:'restaurant_menu_session',value:cookies.get('restaurant_menu_session'),url:base,path:'/',httpOnly:true});
 await call('Page.enable');await call('Emulation.setDeviceMetricsOverride',{width:1440,height:1000,deviceScaleFactor:1,mobile:false});
 const navigate=async url=>{await call('Page.navigate',{url});await delay(1500);};
 const click=async id=>{if(!await evaluate(`(()=>{let b=document.getElementById(${JSON.stringify(id)});if(!b||b.disabled)return false;b.scrollIntoView({block:'center'});b.click();return true;})()`))throw Error('Demo button unavailable: '+id);await delay(1200);};
 const assertText=async(id,value)=>{for(let i=0;i<30;i++){if((await evaluate(`document.getElementById(${JSON.stringify(id)})?.textContent`))?.includes(value))return;await delay(500);}throw Error('Expected demo result not observed: '+value);};
 // One 40-second chapter at a time. Overlays explicitly identify the environment and synthetic speech.
 encoder=spawn('ffmpeg',['-y','-v','error','-f','image2pipe','-framerate','2','-i','pipe:0','-c:v','libx264','-preset','veryfast','-pix_fmt','yuv420p',`${out}/silent.mp4`],{stdio:['pipe','ignore','pipe']});
 let encoderError='';encoder.stderr.on('data',b=>encoderError+=b.toString().slice(0,500));
 recording=true;captureTask=(async()=>{while(recording){const start=Date.now();const frame=await call('Page.captureScreenshot',{format:'jpeg',quality:72,captureBeyondViewport:false});if(!encoder.stdin.write(Buffer.from(frame.data,'base64')))await new Promise(ok=>encoder.stdin.once('drain',ok));await delay(Math.max(0,500-(Date.now()-start)));}})();
 for(let i=0;i<chapters.length;i++){
  const start=Date.now();
  if(i===0)await navigate(live+'login.php');
  if(i===1)await navigate(live+'version.php');
  if(i===2)await navigate(base+'week4.php');
  if(i===3){await navigate(base+'demo.php');await assertText('demo-db','Hoạt động');}
  if(i===4){await click('backup');await assertText('demo-backup','1 bản ghi');}
  if(i===5){await click('delete');await assertText('demo-rows','đã bị xóa');}
  if(i===6){await click('restore');await assertText('demo-match','KHỚP BACKUP');}
  if(i===7){await click('disconnect');await assertText('demo-alert','FIRING');await assertText('demo-http','503');}
  if(i===8){await click('reconnect');await assertText('demo-alert','OK');await assertText('demo-http','200');}
  if(i===9){await evaluate("document.getElementById('demo-events').scrollIntoView({block:'center'})");}
  const title=chapters[i].title,scope=i<2?'Website cPanel thật · chỉ đọc thông tin công khai':'Docker staging tạm · chỉ dữ liệu thử · không phải database cPanel';
  await evaluate(`(()=>{document.getElementById('video-caption')?.remove();let c=document.createElement('div');c.id='video-caption';c.textContent=${JSON.stringify(title+' | '+scope+' | Thuyết minh giọng máy')};Object.assign(c.style,{position:'fixed',bottom:'0',left:'0',right:'0',padding:'12px',background:'#101820',color:'white',font:'18px Arial',zIndex:'99999'});document.body.append(c);return true;})()`);
  events.push({chapter:i+1,title,scope,elapsed_seconds:Math.round((Date.now()-start)/1000)});
  await delay(Math.max(0,40000-(Date.now()-start)));
 }
 recording=false;await captureTask;encoder.stdin.end();const code=await new Promise(ok=>encoder.on('close',ok));if(code!==0)throw Error('Video encoder failed.');
 run('ffmpeg',['-y','-v','error','-i',`${out}/silent.mp4`,'-i',`${out}/narration.wav`,'-c:v','copy','-c:a','aac','-b:a','96k','-af','apad','-t','400','-movflags','+faststart',`${out}/Week4_Demo_ThuyetMinh.mp4`]);
 await fs.writeFile(`${out}/recording-metadata.json`,JSON.stringify({revision:process.env.APP_REVISION,created_at:new Date().toISOString(),narration:'Vietnamese synthetic voice espeak-ng; not a student voice',scope:'Public live login/revision then synthetic disposable Docker demo; no OS metrics or production DB data',chapters:events,duration_seconds:400},null,2));
 console.log('Recorded 400 seconds of verified demo, synthetic narration and explicit environment labels.');
}finally{
 recording=false;try{await action('reconnect');}catch{}if(ws)ws.close();if(encoder&&!encoder.killed)encoder.kill('SIGTERM');chrome.kill('SIGTERM');await delay(300);await fs.rm(profile,{recursive:true,force:true,maxRetries:5,retryDelay:200});
}
