(()=>{"use strict";const{__:t}=wp.i18n,e=["yasr-rater-stars","yasr-multiset-visitors-rater"];for(let t=0;t<e.length;t++)r(e[t]);function r(e){const r=document.getElementsByClassName(e);r.length>0&&("yasr-rater-stars"===e&&function(t){for(let e=0;e<t.length;e++)if(!1===t.item(e).classList.contains("yasr-star-rating")){const r=t.item(e),s=r.id,a=r.getAttribute("data-rater-starsize");yasrSetRaterValue(a,s,r)}}(r),"yasr-multiset-visitors-rater"===e&&function(e){var r="",s=[];const a=document.getElementById("yasr-pro-multiset-review-rating");for(let t=0;t<e.length;t++)!function(t){if(!1!==e.item(t).classList.contains("yasr-star-rating"))return;let i=e.item(t),n=i.id,o=i.getAttribute("data-rater-readonly"),l=i.getAttribute("data-rater-starsize");l||(l=16),o=yasrTrueFalseStringConvertion(o);yasrSetRaterValue(l,n,i,1,o,!1,(function(t,e){const n=i.getAttribute("data-rater-postid"),o=i.getAttribute("data-rater-setid"),l=i.getAttribute("data-rater-set-field-id");t=t.toFixed(1);const u=parseInt(t);this.setRating(u),r={postid:n,setid:o,field:l,rating:u},s.push(r),a&&(a.value=JSON.stringify(s)),e()}))}(t);jQuery(".yasr-send-visitor-multiset").on("click",(function(){const e=this.getAttribute("data-postid"),r=this.getAttribute("data-setid"),a=this.getAttribute("data-nonce");jQuery("#yasr-send-visitor-multiset-"+e+"-"+r).hide(),jQuery("#yasr-loader-multiset-visitor-"+e+"-"+r).show();const i={action:"yasr_visitor_multiset_field_vote",nonce:a,post_id:e,rating:s,set_id:r};jQuery.post(yasrCommonData.ajaxurl,i).done((function(t){let s;s=(t=JSON.parse(t)).text,jQuery("#yasr-loader-multiset-visitor-"+e+"-"+r).text(s)})).fail((function(e,r,s,a){console.error(t("YASR ajax call failed. Can't save data","yet-another-stars-rating")),console.log(e)}))}))}(r))}})();