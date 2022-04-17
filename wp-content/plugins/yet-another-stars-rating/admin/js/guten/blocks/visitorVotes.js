(()=>{"use strict";var e,t,a,r,n={534:(e,t,a)=>{a.r(t),a.d(t,{YasrBlocksPanel:()=>E,YasrDivRatingOverall:()=>S,YasrNoSettingsPanel:()=>R,YasrPrintInputId:()=>b,YasrPrintSelectSize:()=>v,YasrProText:()=>h,yasrLabelSelectSize:()=>o,yasrLeaveThisBlankText:()=>y,yasrOptionalText:()=>i,yasrOverallDescription:()=>d,yasrSelectSizeChoose:()=>c,yasrSelectSizeLarge:()=>p,yasrSelectSizeMedium:()=>m,yasrSelectSizeSmall:()=>u,yasrVisitorVotesDescription:()=>g});var r=a(534),n=wp.i18n.__,s=wp.components.PanelBody,l=wp.blockEditor.InspectorControls,i=n("All these settings are optional","yet-another-stars-rating"),o=n("Choose Size","yet-another-stars-rating"),c=n("Choose stars size","yet-another-stars-rating"),u=n("Small","yet-another-stars-rating"),m=n("Medium","yet-another-stars-rating"),p=n("Large","yet-another-stars-rating"),y=n("Leave this blank if you don't know what you're doing.","yet-another-stars-rating"),d=n("Remember: only the post author can rate here.","yet-another-stars-rating"),g=n("This is the star set where your users will be able to vote","yet-another-stars-rating");function v(e){return React.createElement("form",null,React.createElement("select",{value:e.size,onChange:function(t){return(0,e.setAttributes)({size:(a=t).target.querySelector("option:checked").value}),void a.preventDefault();var a}},React.createElement("option",{value:"--"},r.yasrSelectSizeChoose),React.createElement("option",{value:"small"},r.yasrSelectSizeSmall),React.createElement("option",{value:"medium"},r.yasrSelectSizeMedium),React.createElement("option",{value:"large"},r.yasrSelectSizeLarge)))}function b(e){var t;return!1!==e.postId&&(t=e.postId),React.createElement("div",null,React.createElement("input",{type:"text",size:"4",defaultValue:t,onKeyPress:function(t){return function(e,t){if("Enter"===t.key){var a=t.target.value;!0!==/^\d+$/.test(a)&&""!==a||e({postId:a}),t.preventDefault()}}(e.setAttributes,t)}}))}function h(){var e=n("To be able to customize this ranking, you need","yet-another-stars-rating"),t=n("You can buy the plugin, including support, updates and upgrades, on","yet-another-stars-rating");return React.createElement("h3",null,e," ",React.createElement("a",{href:"https://yetanotherstarsrating.com/?utm_source=wp-plugin&utm_medium=gutenberg_panel&utm_campaign=yasr_editor_screen&utm_content=rankings#yasr-pro"},"Yasr Pro."),React.createElement("br",null),t," ",React.createElement("a",{href:"https://yetanotherstarsrating.com/?utm_source=wp-plugin&utm_medium=gutenberg_panel&utm_campaign=yasr_editor_screen&utm_content=rankings"},"yetanotherstarsrating.com"))}function R(e){return React.createElement("div",null,React.createElement(h,null))}function E(e){var t;return"visitors"===e.block&&(t=g),"overall"===e.block&&(t=d),React.createElement(l,null,"overall"===e.block&&React.createElement(S,null),React.createElement(s,{title:"Settings"},React.createElement("h3",null,i),React.createElement("div",{className:"yasr-guten-block-panel"},React.createElement("label",null,o),React.createElement("div",null,React.createElement(v,{size:e.size,setAttributes:e.setAttributes}))),React.createElement("div",{className:"yasr-guten-block-panel"},React.createElement("label",null,"Post ID"),React.createElement(b,{postId:e.postId,setAttributes:e.setAttributes}),React.createElement("div",{className:"yasr-guten-block-explain"},y)),React.createElement("div",{className:"yasr-guten-block-panel"},t)))}function S(e){if(!0===JSON.parse(yasrConstantGutenberg.isFseElement))return React.createElement("div",{className:"yasr-guten-block-panel yasr-guten-block-panel-center"},React.createElement("div",null,n("This is a template file, you can't rate here. You need to insert the rating inside the single post or page","yet-another-stars-rating")),React.createElement("br",null));var t=n("Rate this article / item","yet-another-stars-rating"),a=wp.data.select("core/editor").getCurrentPost().meta.yasr_overall_rating,r=function(e,t){e=e.toFixed(1),e=parseFloat(e),wp.data.dispatch("core/editor").editPost({meta:{yasr_overall_rating:e}}),this.setRating(e),t()};return React.createElement("div",{className:"yasr-guten-block-panel yasr-guten-block-panel-center"},t,React.createElement("div",{id:"overall-rater",ref:function(){return yasrSetRaterValue(32,"overall-rater",!1,.1,!1,a,r)}}))}}},s={};function l(e){var t=s[e];if(void 0!==t)return t.exports;var a=s[e]={exports:{}};return n[e](a,a.exports,l),a.exports}l.d=(e,t)=>{for(var a in t)l.o(t,a)&&!l.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},l.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),l.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},e=l(534),wp.i18n.__,t=wp.blocks.registerBlockType,a=wp.element.Fragment,r=wp.blockEditor.useBlockProps,t("yet-another-stars-rating/visitor-votes",{edit:function(t){var n=r({className:"yasr-vv-block"}),s=t.attributes,l=s.size,i=s.postId,o=t.setAttributes,c=t.isSelected,u=null,m=null;return"large"!==l&&(u=' size="'+l+'"'),!0===/^\d+$/.test(i)&&(m=' postid="'+i+'"'),React.createElement(a,null,React.createElement(e.YasrBlocksPanel,{block:"visitors",size:l,setAttributes:o,postId:i}),React.createElement("div",n,"[yasr_visitor_votes",u,m,"]",c&&React.createElement(e.YasrPrintSelectSize,{size:l,setAttributes:o})))},save:function(e){var t=r.save({className:"yasr-vv-block"}),a=e.attributes,n=a.size,s=a.postId,l="";return n&&(l+='size="'+n+'"'),s&&(l+=' postid="'+s+'"'),React.createElement("div",t,"[yasr_visitor_votes ",l,"]")}})})();