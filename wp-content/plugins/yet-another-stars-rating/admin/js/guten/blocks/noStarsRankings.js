(()=>{"use strict";var e,t,a,r,n,s,l,c={534:(e,t,a)=>{a.r(t),a.d(t,{YasrBlocksPanel:()=>b,YasrDivRatingOverall:()=>S,YasrNoSettingsPanel:()=>h,YasrPrintInputId:()=>R,YasrPrintSelectSize:()=>d,YasrProText:()=>E,yasrLabelSelectSize:()=>o,yasrLeaveThisBlankText:()=>y,yasrOptionalText:()=>c,yasrOverallDescription:()=>v,yasrSelectSizeChoose:()=>i,yasrSelectSizeLarge:()=>p,yasrSelectSizeMedium:()=>m,yasrSelectSizeSmall:()=>u,yasrVisitorVotesDescription:()=>g});var r=a(534),n=wp.i18n.__,s=wp.components.PanelBody,l=wp.blockEditor.InspectorControls,c=n("All these settings are optional","yet-another-stars-rating"),o=n("Choose Size","yet-another-stars-rating"),i=n("Choose stars size","yet-another-stars-rating"),u=n("Small","yet-another-stars-rating"),m=n("Medium","yet-another-stars-rating"),p=n("Large","yet-another-stars-rating"),y=n("Leave this blank if you don't know what you're doing.","yet-another-stars-rating"),v=n("Remember: only the post author can rate here.","yet-another-stars-rating"),g=n("This is the star set where your users will be able to vote","yet-another-stars-rating");function d(e){return React.createElement("form",null,React.createElement("select",{value:e.size,onChange:function(t){return(0,e.setAttributes)({size:(a=t).target.querySelector("option:checked").value}),void a.preventDefault();var a}},React.createElement("option",{value:"--"},r.yasrSelectSizeChoose),React.createElement("option",{value:"small"},r.yasrSelectSizeSmall),React.createElement("option",{value:"medium"},r.yasrSelectSizeMedium),React.createElement("option",{value:"large"},r.yasrSelectSizeLarge)))}function R(e){var t;return!1!==e.postId&&(t=e.postId),React.createElement("div",null,React.createElement("input",{type:"text",size:"4",defaultValue:t,onKeyPress:function(t){return function(e,t){if("Enter"===t.key){var a=t.target.value;!0!==/^\d+$/.test(a)&&""!==a||e({postId:a}),t.preventDefault()}}(e.setAttributes,t)}}))}function E(){var e=n("To be able to customize this ranking, you need","yet-another-stars-rating"),t=n("You can buy the plugin, including support, updates and upgrades, on","yet-another-stars-rating");return React.createElement("h3",null,e," ",React.createElement("a",{href:"https://yetanotherstarsrating.com/?utm_source=wp-plugin&utm_medium=gutenberg_panel&utm_campaign=yasr_editor_screen&utm_content=rankings#yasr-pro"},"Yasr Pro."),React.createElement("br",null),t," ",React.createElement("a",{href:"https://yetanotherstarsrating.com/?utm_source=wp-plugin&utm_medium=gutenberg_panel&utm_campaign=yasr_editor_screen&utm_content=rankings"},"yetanotherstarsrating.com"))}function h(e){return React.createElement("div",null,React.createElement(E,null))}function b(e){var t;return"visitors"===e.block&&(t=g),"overall"===e.block&&(t=v),React.createElement(l,null,"overall"===e.block&&React.createElement(S,null),React.createElement(s,{title:"Settings"},React.createElement("h3",null,c),React.createElement("div",{className:"yasr-guten-block-panel"},React.createElement("label",null,o),React.createElement("div",null,React.createElement(d,{size:e.size,setAttributes:e.setAttributes}))),React.createElement("div",{className:"yasr-guten-block-panel"},React.createElement("label",null,"Post ID"),React.createElement(R,{postId:e.postId,setAttributes:e.setAttributes}),React.createElement("div",{className:"yasr-guten-block-explain"},y)),React.createElement("div",{className:"yasr-guten-block-panel"},t)))}function S(e){if(!0===JSON.parse(yasrConstantGutenberg.isFseElement))return React.createElement("div",{className:"yasr-guten-block-panel yasr-guten-block-panel-center"},React.createElement("div",null,n("This is a template file, you can't rate here. You need to insert the rating inside the single post or page","yet-another-stars-rating")),React.createElement("br",null));var t=n("Rate this article / item","yet-another-stars-rating"),a=wp.data.select("core/editor").getCurrentPost().meta.yasr_overall_rating,r=function(e,t){e=e.toFixed(1),e=parseFloat(e),wp.data.dispatch("core/editor").editPost({meta:{yasr_overall_rating:e}}),this.setRating(e),t()};return React.createElement("div",{className:"yasr-guten-block-panel yasr-guten-block-panel-center"},t,React.createElement("div",{id:"overall-rater",ref:function(){return yasrSetRaterValue(32,"overall-rater",!1,.1,!1,a,r)}}))}}},o={};function i(e){var t=o[e];if(void 0!==t)return t.exports;var a=o[e]={exports:{}};return c[e](a,a.exports,i),a.exports}i.d=(e,t)=>{for(var a in t)i.o(t,a)&&!i.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},i.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),i.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},e=i(534),t=wp.blocks.registerBlockType,a=wp.components.PanelBody,r=wp.element.Fragment,n=wp.blockEditor,s=n.useBlockProps,l=n.InspectorControls,t("yet-another-stars-rating/most-active-users",{edit:function(t){var n=s({className:"yasr-active-users-block"}),c=[React.createElement(e.YasrNoSettingsPanel,{key:0})];function o(e){return React.createElement(l,null,React.createElement(a,{title:"Settings"},React.createElement("div",{className:"yasr-guten-block-panel"},React.createElement("div",null,c))))}return wp.hooks.doAction("yasr_top_visitor_setting",c),React.createElement(r,null,React.createElement(o,null),React.createElement("div",n,"[yasr_most_active_users]"))},save:function(e){var t=s.save({className:"yasr-active-users-block"});return React.createElement("div",t,"[yasr_most_active_users]")}}),t("yet-another-stars-rating/most-active-reviewers",{edit:function(t){var n=s({className:"yasr-reviewers-block"}),c=[React.createElement(e.YasrNoSettingsPanel,{key:0})];function o(e){return React.createElement(l,null,React.createElement(a,{title:"Settings"},React.createElement("div",{className:"yasr-guten-block-panel"},React.createElement("div",null,c))))}return wp.hooks.doAction("yasr_top_reviewers_setting",c),React.createElement(r,null,React.createElement(o,null),React.createElement("div",n,"[yasr_top_reviewers]"))},save:function(e){var t=s.save({className:"yasr-reviewers-block"});return React.createElement("div",t,"[yasr_top_reviewers]")}})})();