!function(){"use strict";var e,t,o,n,l,s;e=window.wp.blocks,t=JSON.parse('{"$schema":"https://json.schemastore.org/block.json","apiVersion":2,"name":"create-block/content-hub-block","version":"0.1.0","title":"Content Hub Block","category":"widgets","icon":"smiley","description":"","supports":{"html":false},"attributes":{"id":{"type":"number","default":4},"message":{"type":"string","default":"Hello World"}},"textdomain":"content-hub-block","editorScript":"file:./build/index.js","editorStyle":"file:./build/index.css","style":"file:./build/style-index.css"}'),o=window.wp.element,window.wp.i18n,n=window.wp.blockEditor,l=window.wp.components,s=window.wp.data,(0,e.registerBlockType)(t,{edit:function(e){const{attributes:{id:t},setAttributes:i,className:c}=e;let r=[];r.push({value:"",label:""});const a=(0,s.useSelect)((e=>e("core").getEntityRecords("postType","chps-content-hub")));return(0,s.useSelect)((e=>e("core/data").isResolving("core","getEntityRecords",["postType","chps-content-hub"])))?(0,o.createElement)("div",(0,n.useBlockProps)(),(0,o.createElement)("h3",null,"Loading...")):(null!=a&&a.forEach((e=>{r.push({value:e.post_meta_fields.ch_id,label:e.title.rendered})})),(0,o.createElement)("div",(0,n.useBlockProps)(),(0,o.createElement)("div",{class:"chps_logo"}),(0,o.createElement)(l.SelectControl,{label:"Select a content hub",value:t,options:r,onChange:e=>{i({id:Number(e)})}})))},save:function(){return null}})}();