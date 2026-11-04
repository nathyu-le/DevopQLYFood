import fs from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
const ids=new Map();
function element(id=''){return {id,textContent:'',className:'',value:'fixture-csrf',disabled:false,dataset:{action:id},listeners:{},children:[],append(...x){this.children.push(...x);},replaceChildren(...x){this.children=x;},addEventListener(name,fn){this.listeners[name]=fn;}};}
const buttons=['prepare','backup','delete','restore','download','disconnect','reconnect'].map(id=>{const el=element(id);ids.set(id,el);return el;});
const get=id=>{if(!ids.has(id))ids.set(id,element(id));return ids.get(id);};
const fixture={initialized:true,driver_available:true,probe:{health:'ok',rows:[]},alert:{state:'ok',failures:0,successes:1},events:[],restored:false};
const stale={...fixture,initialized:false};
let finishOldGet,posts=0;
const firstGet=new Promise(resolve=>{finishOldGet=resolve;});
const response=(data,status=200)=>({status,ok:status===200,json:async()=>data});
let reads=0;
const context=vm.createContext({document:{getElementById:get,createElement:()=>element(),querySelectorAll:s=>s.includes(':not')?buttons.filter(b=>b.id!=='download'):buttons},Date,URLSearchParams,location:{href:''},setInterval(){},fetch:async(url,options={})=>{
 if(url==='demo-health.php')return response({},200);
 if(options.method==='POST'){posts++;return response(fixture);}
 if(reads++===0)return firstGet;
 return response(fixture);
}});
vm.runInContext(fs.readFileSync('public/assets/demo.js','utf8'),context);
await get('prepare').listeners.click();
assert.equal(posts,1,'clicks during a pending background read must send their action');
assert.equal(get('demo-db').textContent,'Hoạt động');
finishOldGet(response(stale));
await new Promise(resolve=>setImmediate(resolve));
assert.equal(get('demo-db').textContent,'Hoạt động','an older background response must not replace an action result');
console.log('PASS: demo clicks work during background refresh and stale reads cannot overwrite results.');
