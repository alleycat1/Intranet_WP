var rml_gutenberg;(()=>{"use strict";var e={n:t=>{var r=t&&t.__esModule?()=>t.default:()=>t;return e.d(r,{a:r}),r},d:(t,r)=>{for(var n in r)e.o(r,n)&&!e.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:r[n]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r:e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},t={};e.r(t);const r=React;var n=e.n(r);const l=wp;function a(){return a=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var n in r)Object.prototype.hasOwnProperty.call(r,n)&&(e[n]=r[n])}return e},a.apply(this,arguments)}const s="real-media-library/gallery",{registerBlockType:o}=l.blocks,{G:i,SVG:c,Path:u,ServerSideRender:p,PanelBody:m,RangeControl:d,ToggleControl:h,SelectControl:g,TreeSelect:b,Notice:y,Spinner:v,Button:f,withNotices:C}=l.components,{Component:E,Fragment:S}=l.element,{InspectorControls:w,ServerSideRender:k}=l.editor,{__:O}=l.i18n,T=p||k,M=[{value:"attachment",label:O("Attachment Page")},{value:"media",label:O("Media File")},{value:"none",label:O("None")}];class P extends E{constructor(){super(...arguments),this.state={$busy:!0,tree:[]}}async componentDidMount(){const{tree:e}=await window.rml.request({location:{path:"/tree"}});e.unshift({id:-1,name:rmlOpts.others.lang.unorganized}),e.unshift({id:void 0,name:"-"}),this.setState({tree:e,$busy:!1})}render(){const{...e}=this.props,{$busy:t,tree:r}=this.state;return t?n().createElement(v,null):n().createElement(b,a({label:rmlOpts.others.lang.folder},e,{tree:r}))}}o(s,{title:"Real Media Library Gallery",description:"Display folder images in a rich gallery.",icon:n().createElement(c,{viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg"},n().createElement(u,{fill:"none",d:"M0 0h24v24H0V0z"}),n().createElement(i,null,n().createElement(u,{d:"M20 4v12H8V4h12m0-2H8L6 4v12l2 2h12l2-2V4l-2-2z"}),n().createElement(u,{d:"M12 12l1 2 3-3 3 4H9z"}),n().createElement(u,{d:"M2 6v14l2 2h14v-2H4V6H2z"}))),category:"common",supports:{align:!0},attributes:{fid:{type:"number",default:0},columns:{type:"number",default:3},imageCrop:{type:"boolean",default:!0},captions:{type:"boolean",default:!0},linkTo:{type:"string",default:"none"},lastEditReload:{type:"number",default:0}},edit:C(class extends E{constructor(){super(...arguments),this.setFid=e=>this.props.setAttributes({fid:+e}),this.setLinkTo=e=>this.props.setAttributes({linkTo:e}),this.setColumnsNumber=e=>this.props.setAttributes({columns:e}),this.toggleImageCrop=()=>this.props.setAttributes({imageCrop:!this.props.attributes.imageCrop}),this.toggleCaptions=()=>this.props.setAttributes({captions:!this.props.attributes.captions}),this.handleReload=()=>this.props.setAttributes({lastEditReload:(new Date).getTime()}),this.render=()=>{const{attributes:e}=this.props,{fid:t,columns:r=3,imageCrop:l,captions:a,linkTo:o}=e;return n().createElement(S,null,n().createElement(w,null,n().createElement(m,{title:O("Gallery Settings")},n().createElement(P,{value:t,onChange:this.setFid}),n().createElement(d,{label:O("Columns"),value:r,onChange:this.setColumnsNumber,min:"1",max:"8"}),n().createElement(h,{label:O("Crop Images"),checked:!!l,onChange:this.toggleImageCrop}),n().createElement(h,{label:O("Caption"),checked:!!a,onChange:this.toggleCaptions}),n().createElement(g,{label:O("Link To"),value:o,onChange:this.setLinkTo,options:M}),n().createElement(f,{isPrimary:!0,onClick:this.handleReload},rmlOpts.others.lang.reloadContent))),n().createElement(T,{block:s,attributes:e}),!t&&n().createElement(y,{status:"error",isDismissible:!1},n().createElement("p",null,rmlOpts.others.lang.gutenBergBlockSelect)))},this.state={refresh:(new Date).getTime()}}}),save:()=>null}),rml_gutenberg=t})();
//# sourceMappingURL=https://sourcemap.devowl.io/real-media-library/4.21.11/7976a4c0d8fa1cb4f75a5be2108fcba7/rml_gutenberg.lite.js.map
