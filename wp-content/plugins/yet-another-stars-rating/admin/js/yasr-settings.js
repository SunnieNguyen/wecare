(()=>{var e;if(document.getElementsByClassName("nav-tab-active").length>0&&(e=document.getElementsByClassName("nav-tab-active")[0].id),"general_settings"===e){var t=document.getElementById("yasr_auto_insert_switch").checked,a=document.getElementById("yasr-general-options-stars-title-switch").checked;!1===t&&jQuery(".yasr-auto-insert-options-class").prop("disabled",!0),!1===a&&jQuery(".yasr-stars-title-options-class").prop("disabled",!0),document.getElementById("yasr_auto_insert_switch").addEventListener("change",(function(){this.checked?jQuery(".yasr-auto-insert-options-class").prop("disabled",!1):jQuery(".yasr-auto-insert-options-class").prop("disabled",!0)})),document.getElementById("yasr-general-options-stars-title-switch").addEventListener("change",(function(){this.checked?jQuery(".yasr-stars-title-options-class").prop("disabled",!1):jQuery(".yasr-stars-title-options-class").prop("disabled",!0)})),document.getElementById("yasr-settings-custom-texts").addEventListener("click",(function(){document.getElementById("yasr-settings-custom-text-before-overall").value="Our Score",document.getElementById("yasr-settings-custom-text-before-visitor").value="Click to rate this post!",document.getElementById("yasr-settings-custom-text-after-visitor").value="[Total: %total_count% Average: %average%]",document.getElementById("yasr-settings-custom-text-rating-saved").value="Rating saved!",document.getElementById("yasr-settings-custom-text-rating-updated").value="Rating updated!",document.getElementById("yasr-settings-custom-text-must-sign-in").value="You must sign in to vote",document.getElementById("yasr-settings-custom-text-already-rated").value="You have already voted for this article with %rating%"}))}if("style_options"===e&&(wp.codeEditor.initialize(document.getElementById("yasr_style_options_textarea"),yasr_cm_settings),jQuery("#yasr-color-scheme-preview-link").on("click",(function(){return jQuery("#yasr-color-scheme-preview").toggle("slow"),!1})),wp.hooks.doAction("yasrStyleOptions")),"manage_multi"===e){var r=parseInt(document.getElementById("n-multiset").value);if(jQuery("#yasr-multi-set-doc-link").on("click",(function(){jQuery("#yasr-multi-set-doc-box").toggle("slow")})),jQuery("#yasr-multi-set-doc-link-hide").on("click",(function(){jQuery("#yasr-multi-set-doc-box").toggle("slow")})),1===r){var n=jQuery("#yasr-edit-form-number-elements").attr("value");n++,jQuery("#yasr-add-field-edit-multiset").on("click",(function(){if(n>9)return jQuery("#yasr-element-limit").show(),jQuery("#yasr-add-field-edit-multiset").hide(),!1;var e=jQuery(document.createElement("tr"));e.html('<td colspan="2">Element #'+n+' <input type="text" name="edit-multi-set-element-'+n+'" value="" ></td>'),e.appendTo("#yasr-table-form-edit-multi-set"),n++}))}else r>1&&(jQuery("#yasr-button-select-set-edit-form").on("click",(function(){var e={action:"yasr_get_multi_set",set_id:jQuery("#yasr_select_edit_set").val()};return jQuery.post(ajaxurl,e,(function(e){jQuery("#yasr-multi-set-response").show(),jQuery("#yasr-multi-set-response").html(e)})),!1})),jQuery(document).ajaxComplete((function(){var e=jQuery("#yasr-edit-form-number-elements").attr("value");e++,jQuery("#yasr-add-field-edit-multiset").on("click",(function(){if(e>9)return jQuery("#yasr-element-limit").show(),jQuery("#yasr-add-field-edit-multiset").hide(),!1;var t=jQuery(document.createElement("tr"));t.html('<td colspan="2">Element #'+e+' <input type="text" name="edit-multi-set-element-'+e+'" value="" ></td>'),t.appendTo("#yasr-table-form-edit-multi-set"),e++}))})))}"migration_tools"===e&&(jQuery("#yasr-import-ratemypost-submit").on("click",(function(){document.getElementById("yasr-import-ratemypost-answer").innerHTML='<img src="'+yasrCommonData.loaderUrl+'" alt="yasr-loader" width="16" height="16">';var e={action:"yasr_import_ratemypost",nonce:document.getElementById("yasr-import-rmp-nonce").value};jQuery.post(ajaxurl,e,(function(e){e=JSON.parse(e),document.getElementById("yasr-import-ratemypost-answer").innerHTML=e}))})),jQuery("#yasr-import-wppr-submit").on("click",(function(){document.getElementById("yasr-import-wppr-answer").innerHTML='<img src="'+yasrCommonData.loaderUrl+'" alt="yasr-loader" width="16" height="16">';var e={action:"yasr_import_wppr",nonce:document.getElementById("yasr-import-wppr-nonce").value};jQuery.post(ajaxurl,e,(function(e){document.getElementById("yasr-import-wppr-answer").innerHTML=e}))})),jQuery("#yasr-import-kksr-submit").on("click",(function(){document.getElementById("yasr-import-kksr-answer").innerHTML='<img src="'+yasrCommonData.loaderUrl+'" alt="yasr-loader" width="16" height="16">';var e={action:"yasr_import_kksr",nonce:document.getElementById("yasr-import-kksr-nonce").value};jQuery.post(ajaxurl,e,(function(e){document.getElementById("yasr-import-kksr-answer").innerHTML=e}))})),jQuery("#yasr-import-mr-submit").on("click",(function(){document.getElementById("yasr-import-mr-answer").innerHTML='<img src="'+yasrCommonData.loaderUrl+'" alt="yasr-loader" width="16" height="16">';var e={action:"yasr_import_mr",nonce:document.getElementById("yasr-import-mr-nonce").value};jQuery.post(ajaxurl,e,(function(e){document.getElementById("yasr-import-mr-answer").innerHTML=e}))})),wp.hooks.doAction("yasr_migration_page_bottom")),"rankings"===e&&wp.hooks.doAction("yasr_ranking_page_top")})(),(()=>{"use strict";let e;var t,a=new Uint8Array(16);function r(){if(!t&&!(t="undefined"!=typeof crypto&&crypto.getRandomValues&&crypto.getRandomValues.bind(crypto)||"undefined"!=typeof msCrypto&&"function"==typeof msCrypto.getRandomValues&&msCrypto.getRandomValues.bind(msCrypto)))throw new Error("crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported");return t(a)}const n=/^(?:[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}|00000000-0000-0000-0000-000000000000)$/i,s=function(e){return"string"==typeof e&&n.test(e)};for(var o=[],i=0;i<256;++i)o.push((i+256).toString(16).substr(1));const l=function(e,t,a){var n=(e=e||{}).random||(e.rng||r)();if(n[6]=15&n[6]|64,n[8]=63&n[8]|128,t){a=a||0;for(var i=0;i<16;++i)t[a+i]=n[i];return t}return function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0,a=(o[e[t+0]]+o[e[t+1]]+o[e[t+2]]+o[e[t+3]]+"-"+o[e[t+4]]+o[e[t+5]]+"-"+o[e[t+6]]+o[e[t+7]]+"-"+o[e[t+8]]+o[e[t+9]]+"-"+o[e[t+10]]+o[e[t+11]]+o[e[t+12]]+o[e[t+13]]+o[e[t+14]]+o[e[t+15]]).toLowerCase();if(!s(a))throw TypeError("Stringified UUID is invalid");return a}(n)};function d(e){return d="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},d(e)}function c(e,t){for(var a=0;a<t.length;a++){var r=t[a];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}function u(e,t){return u=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e},u(e,t)}function y(e,t){if(t&&("object"===d(t)||"function"==typeof t))return t;if(void 0!==t)throw new TypeError("Derived constructors may only return object or undefined");return function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e)}function m(e){return m=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)},m(e)}var p=wp.i18n.__,g=wp.element.render;function h(e){var t="yasr-ranking-element-"+l(),a=document.getElementById(e.tableId).dataset.rankingSize;return React.createElement("div",{id:t,ref:function(){return yasrSetRaterValue(a,t,!1,.1,!0,e.rating)}})}function f(e){if(void 0!==e.post.number_of_votes)return React.createElement("span",{className:"yasr-most-rated-text"},"[",p("Total:","yet-another-stars-rating")," ",e.post.number_of_votes,"  ",p("Average:","yet-another-stars-rating")," ",e.post.rating,"]");var t=e.text;return React.createElement("span",{className:"yasr-highest-rated-text"},t," ",e.post.rating)}function v(t){return React.createElement("td",{className:t.colClass},React.createElement("a",{href:t.post.link},function(t){if("string"!=typeof t||-1===t.indexOf("&"))return t;void 0===e&&(e=document.implementation&&document.implementation.createHTMLDocument?document.implementation.createHTMLDocument("").createElement("textarea"):document.createElement("textarea")),e.innerHTML=t;const a=e.textContent;return e.innerHTML="",a}(t.post.title)))}function _(e){var t="after",a=p("Rating:","yet-another-stars-rating"),r=new URLSearchParams(e.rankingParams);return null!==r.get("text_position")&&(t=r.get("text_position")),null!==r.get("custom_txt")&&(a=r.get("custom_txt")),"before"===t?React.createElement("td",{className:e.colClass},React.createElement(f,{post:e.post,tableId:e.tableId,text:a}),React.createElement(h,{rating:e.post.rating,tableId:e.tableId})):React.createElement("td",{className:e.colClass},React.createElement(h,{rating:e.post.rating,tableId:e.tableId}),React.createElement(f,{post:e.post,tableId:e.tableId,text:a}))}function b(e){var t="",a="";return"author_ranking"===e.source?(t="yasr-top-10-overall-left",a="yasr-top-10-overall-right"):"visitor_votes"===e.source&&(t="yasr-top-10-most-highest-left",a="yasr-top-10-most-highest-right"),React.createElement("tr",{className:e.trClass},React.createElement(v,{colClass:t,post:e.post}),React.createElement(_,{colClass:a,post:e.post,tableId:e.tableId,rankingParams:e.rankingParams}))}function E(e){return React.createElement("tbody",{id:e.tBodyId,style:{display:e.show}},e.data.map((function(t,a){var r="yasr-rankings-td-colored";return"author_ranking"===e.source&&(r="yasr-rankings-td-white"),a%2==0&&(r="yasr-rankings-td-white","author_ranking"===e.source&&(r="yasr-rankings-td-colored")),React.createElement(b,{key:t.post_id,source:e.source,tableId:e.tableId,rankingParams:e.rankingParams,post:t,trClass:r})})))}var k=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),Object.defineProperty(e,"prototype",{writable:!1}),t&&u(e,t)}(o,React.Component);var t,a,r,n,s=(r=o,n=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}(),function(){var e,t=m(r);if(n){var a=m(this).constructor;e=Reflect.construct(t,arguments,a)}else e=t.apply(this,arguments);return y(this,e)});function o(e){var t;return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,o),(t=s.call(this,e)).state={error:null,isLoaded:!1,data:[],tableId:e.tableId,source:e.source,rankingParams:e.params,nonce:e.nonce},t}return t=o,a=[{key:"componentDidMount",value:function(){var e=this,t=JSON.parse(document.getElementById(this.state.tableId).dataset.rankingData),a={};if("yes"!==yasrCommonData.ajaxEnabled)console.info("Ajax Disabled, getting data from source"),this.setState({isLoaded:!0,data:t});else if(this.state.source){var r=this.returnRestUrl();Promise.all(r.map((function(e){return fetch(e).then((function(e){return!0===e.ok?e.json():(console.info("Ajax Call Failed. Getting data from source"),"KO")})).then((function(e){"KO"===e?a=t:"overall_rating"===e.source||"author_multi"===e.source?a="overall_rating"===e.source?e.data_overall:e.data_mv:a[e.show]=e.data_vv})).catch((function(e){a=t,console.info(e)}))}))).then((function(t){e.setState({isLoaded:!0,data:a})})).catch((function(t){console.info(t),e.setState({isLoaded:!0,data:a})}))}else this.setState({error:"Invalid Data Source"})}},{key:"returnRestUrl",value:function(){var e,t=""!==this.state.rankingParams?this.state.rankingParams:"",a=this.state.source,r="&nonce_rankings="+this.state.nonce,n="";if(""!==t&&!1!==t){var s=new URLSearchParams(t);null!==s.get("order_by")&&(n+="order_by="+s.get("order_by")),null!==s.get("limit")&&(n+="&limit="+s.get("limit")),null!==s.get("start_date")&&"0"!==s.get("start_date")&&(n+="&start_date="+s.get("start_date")),null!==s.get("end_date")&&"0"!==s.get("end_date")&&(n+="&end_date="+s.get("end_date")),null!==s.get("ctg")?n+="&ctg="+s.get("ctg"):null!==s.get("cpt")&&(n+="&cpt="+s.get("cpt")),""!==n&&(n="&"+(n=n.replace(/\s+/g,""))),"visitor_multi"!==a&&"author_multi"!==a||null!==s.get("setid")&&(n+="&setid="+s.get("setid"))}else n="";if("author_ranking"===a||"author_multi"===a)e=[yasrCommonData.ajaxurl+"?action=yasr_load_rankings&source="+a+n+r];else{var o="",i="";if(""!==t){var l=new URLSearchParams(t);null!==l.get("required_votes[most]")&&(o="&required_votes="+l.get("required_votes[most]")),null!==l.get("required_votes[highest]")&&(i="&required_votes="+l.get("required_votes[highest]"))}e=[yasrCommonData.ajaxurl+"?action=yasr_load_rankings&show=most&source="+a+n+o+r,yasrCommonData.ajaxurl+"?action=yasr_load_rankings&show=highest&source="+a+n+i+r]}return e}},{key:"rankingTableHead",value:function(e,t){var a=this.state.tableId,r="link-most-rated-posts-"+a,n="link-highest-rated-posts-"+a;if("author_ranking"!==e){var s=React.createElement("span",null,React.createElement("span",{id:r},p("Most Rated","yet-another-stars-rating"))," | ",React.createElement("a",{href:"#",id:n,onClick:this.switchTBody.bind(this)},p("Highest Rated","yet-another-stars-rating")));return"highest"===t&&(s=React.createElement("span",null,React.createElement("span",{id:n},p("Highest Rated","yet-another-stars-rating"))," | ",React.createElement("a",{href:"#",id:r,onClick:this.switchTBody.bind(this)},p("Most Rated","yet-another-stars-rating")))),React.createElement("thead",null,React.createElement("tr",{className:"yasr-rankings-td-colored yasr-rankings-heading"},React.createElement("th",null,p("Post","yet-another-stars-rating")),React.createElement("th",null,p("Order By","yet-another-stars-rating"),":  ",s)))}return React.createElement(React.Fragment,null)}},{key:"switchTBody",value:function(e){e.preventDefault();var t=e.target.id,a=this.state.tableId,r="link-most-rated-posts-"+a,n="link-highest-rated-posts-"+a,s="most-rated-posts-"+a,o="highest-rated-posts-"+a,i=document.getElementById(t),l=document.createElement("span");l.innerHTML=i.innerHTML,l.id=i.id,i.parentNode.replaceChild(l,i),t===r&&(document.getElementById(o).style.display="none",document.getElementById(s).style.display="",l=document.getElementById(n),i.innerHTML=l.innerHTML,i.id=l.id,l.parentNode.replaceChild(i,l)),t===n&&(document.getElementById(s).style.display="none",document.getElementById(o).style.display="",l=document.getElementById(r),i.innerHTML=l.innerHTML,i.id=l.id,l.parentNode.replaceChild(i,l))}},{key:"rankingTableBody",value:function(){var e=this.state,t=e.data,a=e.source,r=e.rankingParams;if("overall_rating"===a||"author_multi"===a)return React.createElement(E,{data:t,tableId:this.state.tableId,tBodyId:"overall_"+this.state.tableId,rankingParams:r,show:"table-row-group",source:a});var n=t.most,s=t.highest,o="table-row-group",i="none",l="most",d=o,c=i,u=new URLSearchParams(r);return null!==u.get("view")&&(l=u.get("view")),"highest"===l&&(d=i,c=o),React.createElement(React.Fragment,null,this.rankingTableHead(a,l),React.createElement(E,{data:n,tableId:this.state.tableId,tBodyId:"most-rated-posts-"+this.state.tableId,rankingParams:r,show:d,source:a}),React.createElement(E,{data:s,tableId:this.state.tableId,tBodyId:"highest-rated-posts-"+this.state.tableId,rankingParams:r,show:c,source:a}))}},{key:"render",value:function(){var e=this.state,t=e.error,a=e.isLoaded;return t?React.createElement("tbody",null,React.createElement("tr",null,React.createElement("td",null,console.log(t),"Error"))):!1===a?React.createElement("tbody",null,React.createElement("tr",null,React.createElement("td",null,p("Loading Charts","yet-another-stars-rating")))):React.createElement(React.Fragment,null,this.rankingTableBody())}}],a&&c(t.prototype,a),Object.defineProperty(t,"prototype",{writable:!1}),o}();function w(){var e=document.getElementsByClassName("yasr-stars-rankings");if(e.length>0)for(var t=0;t<e.length;t++){var a=e.item(t).id,r=JSON.parse(e.item(t).dataset.rankingSource),n=JSON.parse(e.item(t).dataset.rankingParams),s=JSON.parse(e.item(t).dataset.rankingNonce),o=document.getElementById(a);g(React.createElement(k,{source:r,tableId:a,params:n,nonce:s}),o)}}w(),tippy(document.querySelectorAll(".yasr-copy-shortcode"),{content:"Copied! Insert into your post!",theme:"yasr",arrow:"true",arrowType:"round",trigger:"click"});var I,B=wp.i18n.__;if(document.getElementsByClassName("nav-tab-active").length>0&&(I=document.getElementsByClassName("nav-tab-active")[0].id),"rankings"===I){var R,j=function(e){var t=D.value,a=document.getElementById("yasr-builder-shortcode").textContent,r=["yasr_ov_ranking","yasr_most_or_highest_rated_posts","yasr_multi_set_ranking","yasr_visitor_multi_set_ranking"];r=wp.hooks.applyFilters("yasrBuilderDrawRankingsShortcodes",r),fetch(ajaxurl+"?action=yasr_rankings_preview_shortcode&shortcode="+t+"&full_shortcode="+a).then((function(e){return!0===e.ok?e.json():(console.info(B("Ajax Call Failed. Shortcode preview can't be done","yet-another-stars-rating")),"KO")})).catch((function(e){console.info(e)})).then((function(e){if("KO"!==e){var t=document.createElement("div");t.innerHTML=e,M.childNodes.length>0?M.replaceChild(t,M.childNodes[0]):M.appendChild(t)}})).then((function(e){r.forEach((function(e){t===e&&w()}))}))},x=function(e,t,a,r,n,s,o,i,l){e.style.display="",t.style.display="",s.style.display="",l.style.display="",a.style.display="none",r.style.display="none",n.style.display="none",null!==o&&(o.style.display=""),null!==i&&(i.style.display="none")},C=function(e,t,a,r,n,s,o,i,l){a.style.display="",r.style.display="",t.style.display="",s.style.display="",l.style.display="",e.style.display="none",n.style.display="none",null!==o&&(o.style.display=""),null!==i&&(i.style.display="none")},Q=function(e,t,a,r,n,s,o,i,l){n.style.display="",l.style.display="none",e.style.display="none",a.style.display="none",r.style.display="none",t.style.display="none",s.style.display="none",null!==o&&(o.style.display="none"),null!==i&&(i.style.display="none")},L=function(e,t,a,r,n,s,o,i){var l=arguments.length>8&&void 0!==arguments[8]&&arguments[8],d=arguments.length>9?arguments[9]:void 0;!0===l?(F.className="",F.classList.add("yasr-settings-row-24"),a.style.display="",r.style.display="",e.style.display="none"):(a.style.display="none",r.style.display="none",e.style.display=""),s.style.display="",t.style.display="",d.style.display="",n.style.display="none",null!==o&&(o.style.display=""),null!==i&&(i.style.display="")},T=".yasr-builder-elements-parents",S=".yasr-builder-elements-childs";jQuery(T).prop("disabled",!0),jQuery(S).prop("disabled",!0),jQuery(T).find("input").each((function(){jQuery(this).prop("disabled",!0)})),wp.hooks.doAction("yasrBuilderBegin",T,S);var P=[],O={name:"yasr_most_or_highest_rated_posts",setid:"",rows:"",size:"",view:"",minvotesmost:"",minvoteshg:"",txtPosition:"",txt:"",display:"",start_date:"",end_date:"",category:"",cpt:""};R=O.name,document.getElementById("yasr-builder-shortcode").textContent="["+R+"]",document.getElementById("yasr-builder-copy-shortcode").setAttribute("data-shortcode","["+R+"]");var N=document.getElementById("yasr-builder-button-preview"),H=document.getElementById("yasr-builder-copy-shortcode"),M=document.getElementById("yasr-builder-preview"),D=document.getElementById("yasr-ranking-source"),A=document.getElementById("yasr-ranking-multiset-select"),U=document.getElementById("yasr-builder-datepicker-start"),q=document.getElementById("yasr-builder-datepicker-end"),F=document.getElementById("yasr-builder-params-container"),z=document.getElementById("builder-vv-default-view"),V=document.getElementById("builder-vv-required-votes"),J=document.getElementById("builder-stars-size"),K=document.getElementById("builder-overall-text"),Y=document.getElementById("builder-username-options"),G=document.getElementById("builder-category"),$=document.getElementById("builder-cpt"),W=document.getElementById("yasr-ranking-multiset"),X=document.getElementById("yasr-builder-datepicker"),Z=D.value,ee=!1;U.value="",q.value="","yasr_ov_ranking"===Z?x(K,J,z,V,Y,G,$,W,X):"yasr_most_active_users"===Z||"yasr_top_reviewers"===Z?Q(K,J,z,V,Y,G,$,W,X):"yasr_multi_set_ranking"===Z?L(K,J,z,V,Y,G,$,W,!1,X):"yasr_visitor_multi_set_ranking"===Z?L(K,J,z,V,Y,G,$,W,!0,X):C(K,J,z,V,Y,G,$,W,X),document.addEventListener("change",(function(e){if("yasr-ranking-source"===e.target.id)F.className="",F.classList.add("yasr-settings-row-33"),M.innerHTML="",U.value="",q.value="",O={name:"yasr_most_or_highest_rated_posts",setid:"",rows:"",size:"",view:"",minvotesmost:"",minvoteshg:"",txtPosition:"",txt:"",display:"",start_date:"",end_date:"",category:"",cpt:""},"yasr_ov_ranking"===e.target.value?x(K,J,z,V,Y,G,$,W,X):"yasr_most_active_users"===e.target.value||"yasr_top_reviewers"===e.target.value?Q(K,J,z,V,Y,G,$,W,X):"yasr_multi_set_ranking"===e.target.value?(L(K,J,z,V,Y,G,$,W,!1,X),O.setid=" setid="+A[0].value):"yasr_visitor_multi_set_ranking"===e.target.value?(L(K,J,z,V,Y,G,$,W,!0,X),O.setid=" setid="+A[0].value):C(K,J,z,V,Y,G,$,W,X),O.name=e.target.value,R=O.name+O.setid;else{"yasr-ranking-multiset-select"===e.target.id&&(O.setid=" setid="+e.target.value),P=wp.hooks.applyFilters("yasrBuilderFilterShortcode",O);for(var t=2;O.length;t++)P.hasOwnProperty(O[t])&&(O[t]=P[t]);R=O.name+O.setid+O.rows+O.view+O.minvotesmost+O.minvoteshg+O.size+O.txtPosition+O.txt+O.display+O.start_date+O.end_date+O.category+O.cpt}document.getElementById("yasr-builder-shortcode").textContent="["+R+"]",document.getElementById("yasr-builder-copy-shortcode").setAttribute("data-shortcode","["+R+"]"),!0===ee&&"yasr-ranking-source"!==e.target.id&&"yasr-builder-category-radio"!==e.target.name&&j()})),H.onclick=function(e){var t,a;t=document.getElementById(e.target.id).getAttribute("data-shortcode"),(a=document.createElement("textarea")).value=t,a.setAttribute("readonly",""),a.style.position="absolute",a.style.left="-9999px",document.body.appendChild(a),a.select(),document.execCommand("copy"),document.body.removeChild(a)},N.onclick=function(e){j(),ee=!0}}})();