// Real Chrome screenshot of an isolated staging dashboard. No credentials are logged.
import fs from 'node:fs/promises';
import os from 'node:os';
import path from 'node:path';
import {spawn} from 'node:child_process';

if(process.env.RUN_OPERATIONS_TESTS!=='1'||!process.env.COMPOSE_PROJECT_NAME?.endsWith('_ci'))throw Error('Only disposable CI staging is supported.');
const label=process.argv[2];if(!['normal','firing','recovered'].includes(label))throw Error('Unknown screenshot label.');
const base=`http://127.0.0.1:${process.env.APP_PORT||8080}/`;
const password=(await fs.readFile('.secrets/admin_password','utf8')).trim();
let cookies=new Map();
async function request(route,options={}){
  const r=await fetch(base+route,{...options,redirect:'manual',headers:{...options.headers,Cookie:[...cookies].map(([k,v])=>`${k}=${v}`).join('; ')}});
  for(const c of r.headers.getSetCookie()) {const pair=c.split(';')[0],i=pair.indexOf('=');cookies.set(pair.slice(0,i),pair.slice(i+1));}
  return r;
}
const login=await request('login.php'),html=await login.text();
const csrf=html.match(/name="csrf" value="([a-f0-9]+)"/)?.[1];if(!csrf)throw Error('Login form unavailable.');
const authenticated=await request('login.php',{method:'POST',body:new URLSearchParams({csrf,username:'admin',password})});
if(authenticated.status!==303||!cookies.has('restaurant_menu_session'))throw Error('Staging login failed.');
const profile=await fs.mkdtemp(path.join(os.tmpdir(),'week3-chrome-'));
const chrome=spawn('google-chrome',['--headless=new','--no-sandbox','--disable-dev-shm-usage','--remote-debugging-port=0',`--user-data-dir=${profile}`,'about:blank'],{stdio:'ignore'});
const delay=ms=>new Promise(r=>setTimeout(r,ms));let ws;
try{
  let port;
  for(let i=0;i<100;i++){try{port=(await fs.readFile(path.join(profile,'DevToolsActivePort'),'utf8')).split('\n')[0];break;}catch{await delay(100);}}
  if(!port)throw Error('Chrome did not start.');
  const target=await(await fetch(`http://127.0.0.1:${port}/json/new?about:blank`,{method:'PUT'})).json();
  ws=new WebSocket(target.webSocketDebuggerUrl);await new Promise((resolve,reject)=>{ws.addEventListener('open',resolve,{once:true});ws.addEventListener('error',reject,{once:true});});
  let sequence=0;const waiting=new Map();
  ws.addEventListener('message',e=>{const d=JSON.parse(e.data);if(!d.id)return;const task=waiting.get(d.id);if(task){waiting.delete(d.id);d.error?task.reject(Error(d.error.message)):task.resolve(d.result);}});
  const call=(method,params={})=>new Promise((resolve,reject)=>{const id=++sequence;waiting.set(id,{resolve,reject});ws.send(JSON.stringify({id,method,params}));});
  await call('Network.enable');
  await call('Network.setCookie',{name:'restaurant_menu_session',value:cookies.get('restaurant_menu_session'),url:base,path:'/',httpOnly:true});
  await call('Page.enable');await call('Emulation.setDeviceMetricsOverride',{width:1440,height:1100,deviceScaleFactor:1,mobile:false});
  await call('Page.navigate',{url:base+'operations.php'});
  let ready=false;
  for(let i=0;i<100;i++){
    const r=await call('Runtime.evaluate',{expression:'document.documentElement.dataset.opsReady === "true"',returnByValue:true});
    if(r.result.value){ready=true;break;}await delay(150);
  }
  if(!ready)throw Error('Dashboard did not render live data.');
  const expected=label==='firing'?'firing':'ok';
  const state=await call('Runtime.evaluate',{expression:'snapshot.alerts.database_unavailable.state',returnByValue:true});
  if(state.result.value!==expected)throw Error('Dashboard does not match the recorded incident state.');
  await delay(300);
  const screenshot=await call('Page.captureScreenshot',{format:'png',captureBeyondViewport:false});
  await fs.mkdir('week3-results',{recursive:true});
  await fs.writeFile(`week3-results/dashboard-${label}.png`,Buffer.from(screenshot.data,'base64'));
  await fs.writeFile(`week3-results/dashboard-${label}.json`,JSON.stringify({label,scope:'isolated Docker staging, not cPanel',base_url:base,revision:process.env.APP_REVISION,database_alert:state.result.value,captured_at:new Date().toISOString(),width:1440,height:1100},null,2));
  console.log(`Captured real dashboard: ${label}; database alert ${state.result.value}.`);
}finally{if(ws)ws.close();chrome.kill('SIGTERM');await delay(300);await fs.rm(profile,{recursive:true,force:true});}
