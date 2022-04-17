import {copyToClipboard} from "./yasr-admin-functions";
import {v4 as uuidv4}      from 'uuid';


// executes this when the DOM is ready
document.addEventListener('DOMContentLoaded', function(event) {

    //check if is gutenberg editor
    let yasrIsGutenbergEditor = document.body.classList.contains('block-editor-page');

    if(yasrIsGutenbergEditor !== true) {
        //show overall rating in the metabox
        yasrPrintMetaBoxOverall();

        //run shortcode creator
        shortcodeCreator();
    }

    //always show snippet or multi set
    yasrPrintMetaBoxBelowEditor();

}); //end document ready

document.getElementById('yasr-metabox-below-editor-select-schema').addEventListener('change',
    function() {
        let selectedItemtype = this.value;
        yasrSwitchItemTypeDiv(selectedItemtype);
    }
);


/**
 * Print the stars for top right metabox
 *
 * @return void;
 */
function yasrPrintMetaBoxOverall() {

    //Convert string to number
    let overallRating = parseFloat(document.getElementById('yasr-overall-rating-value').value);
    const copyOverall = document.getElementById('yasr-editor-copy-overall');

    if(copyOverall !== null) {
        copyOverall.onclick = function (event) {
            let el = document.getElementById(event.target.id);
            copyToClipboard(el.textContent.trim());
        }
    }

    raterJs({
        starSize: 32,
        step: 0.1,
        showToolTip: false,
        rating: overallRating,
        readOnly: false,
        element: document.getElementById("yasr-rater-overall"),
        rateCallback: function rateCallback(rating, done) {

            rating = rating.toFixed(1);
            rating = parseFloat(rating);

            //update hidden field
            document.getElementById('yasr-overall-rating-value').value = rating;

            this.setRating(rating);

            let yasrOverallString = 'You\'ve rated';

            document.getElementById('yasr_overall_text').textContent = yasrOverallString + ' ' + rating;

            done();
        }
    });

}

/**
 * Print metabox below editor
 * At the page load, show Schema.org option
 */
function yasrPrintMetaBoxBelowEditor () {
    // When click on main tab hide multi set content
    jQuery('#yasr-metabox-below-editor-structured-data-tab').on("click", function (e) {

        //prevent click on link jump to the top
        e.preventDefault();

        jQuery('.yasr-nav-tab').removeClass('nav-tab-active');
        jQuery('#yasr-metabox-below-editor-structured-data-tab').addClass('nav-tab-active');

        jQuery('.yasr-metabox-below-editor-content').hide();
        jQuery('#yasr-metabox-below-editor-structured-data').show();

    });

    //When click on multi set tab hide snippet content
    jQuery('#yasr-metabox-below-editor-multiset-tab').on("click", function (e) {

        //prevent click on link jump to the top
        e.preventDefault();

        jQuery('.yasr-nav-tab').removeClass('nav-tab-active');
        jQuery('#yasr-metabox-below-editor-multiset-tab').addClass('nav-tab-active');

        jQuery('.yasr-metabox-below-editor-content').hide();
        jQuery('#yasr-metabox-below-editor-multiset').show();

    });

    let selectedItemtype = document.getElementById('yasr-metabox-below-editor-select-schema').value;

    if(document.getElementById('yasr-editor-multiset-container') !== null) {
        yasrAdminMultiSet();
    }

    yasrSwitchItemTypeDiv(selectedItemtype);
}

function yasrSwitchItemTypeDiv (itemType) {
    if(itemType === 'Product') {
        //show main div
        document.getElementById('yasr-metabox-info-snippet-container').style.display = '';
        //show product
        document.getElementById('yasr-metabox-info-snippet-container-product').style.display = '';

        //hide all child divs
        document.getElementById('yasr-metabox-info-snippet-container-localbusiness').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-recipe').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-software').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-book').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-movie').style.display = 'none';

    }
    else if(itemType === 'LocalBusiness') {
        //show main div
        document.getElementById('yasr-metabox-info-snippet-container').style.display = '';
        //show localbusiness
        document.getElementById('yasr-metabox-info-snippet-container-localbusiness').style.display = '';
        //hide all child
        document.getElementById('yasr-metabox-info-snippet-container-product').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-recipe').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-software').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-book').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-movie').style.display = 'none';

    }
    else if(itemType === 'Recipe') {
        //show main div
        document.getElementById('yasr-metabox-info-snippet-container').style.display = '';
        //show recipe
        document.getElementById('yasr-metabox-info-snippet-container-recipe').style.display = '';
        //hide all child
        document.getElementById('yasr-metabox-info-snippet-container-localbusiness').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-product').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-software').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-book').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-movie').style.display = 'none';
    }
    else if(itemType === 'SoftwareApplication') {
        //show main div
        document.getElementById('yasr-metabox-info-snippet-container').style.display = '';
        //show Software Application
        document.getElementById('yasr-metabox-info-snippet-container-software').style.display = '';

        //hide all childs
        document.getElementById('yasr-metabox-info-snippet-container-recipe').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-localbusiness').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-product').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-book').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-movie').style.display = 'none';
    }
    else if(itemType === 'Book') {
        //show main div
        document.getElementById('yasr-metabox-info-snippet-container').style.display = '';
        //show Book
        document.getElementById('yasr-metabox-info-snippet-container-book').style.display = '';

        //hide all childs
        document.getElementById('yasr-metabox-info-snippet-container-recipe').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-localbusiness').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-product').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-software').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-movie').style.display = 'none';

    }

    else if(itemType === 'Movie') {
        //show main div
        document.getElementById('yasr-metabox-info-snippet-container').style.display = '';
        //show Book
        document.getElementById('yasr-metabox-info-snippet-container-movie').style.display = '';

        //hide all childs
        document.getElementById('yasr-metabox-info-snippet-container-recipe').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-localbusiness').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-product').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-software').style.display = 'none';
        document.getElementById('yasr-metabox-info-snippet-container-book').style.display = 'none';

    }

    else {
        document.getElementById('yasr-metabox-info-snippet-container').style.display = 'none';
    }
}

/****** Yasr Metabox Multiple Rating ******/

function yasrAdminMultiSet() {

    let divContainer               = document.getElementById('yasr-editor-multiset-container');
    let nMultiSet                  = parseInt(divContainer.getAttribute('data-nmultiset'));
    let setId                      = parseInt(divContainer.getAttribute('data-setid'));
    let postId                     = parseInt(divContainer.getAttribute('data-postid'));
    //do not use parseInt here, or an empty value will be converted to 0
    const yasrProReviewSetid       = document.getElementById('yasr-pro-review-setid');
    const copyRoMultiset           = document.getElementById('yasr-editor-copy-readonly-multiset');
    const copyVisitorMultiset      = document.getElementById('yasr-editor-copy-visitor-multiset');
    const copyAverageMultiSet      = document.getElementById('yasr-editor-copy-average-multiset');
    const copyAverageVVMultiSet    = document.getElementById('yasr-editor-copy-average-vvmultiset');
    const copyAverageCommentsMulti = document.getElementById('yasr-editor-copy-comments-multiset');
    const reviewsEnabled           = document.getElementById('yasr-pro-comments-enabled-yes');
    const multiSetinReview         = document.getElementById('yasr-pro-multiset-review-switcher');

    yasrPrintAdminMultiSet(setId, postId, nMultiSet);

    copyRoMultiset.onclick = function (event) {
        let el = document.getElementById(event.target.id);
        copyToClipboard(el.textContent.trim());
    }

    copyVisitorMultiset.onclick = function (event) {
        let el = document.getElementById(event.target.id);
        copyToClipboard(el.textContent.trim());
    }

    copyAverageMultiSet.onclick = function (event) {
        let el = document.getElementById(event.target.id);
        copyToClipboard(el.textContent.trim());
    }

    copyAverageVVMultiSet.onclick = function (event) {
        let el = document.getElementById(event.target.id);
        copyToClipboard(el.textContent.trim());
    }

    copyAverageCommentsMulti.onclick = function (event) {
        let el = document.getElementById(event.target.id);
        copyToClipboard(el.textContent.trim());
    }

    //add event listener to synchronize switchers
    if(multiSetinReview !== null) {

        //this only works in classic editor
        if(reviewsEnabled !== null) {
            //when reviews in comment are disabled, disable also multiset switcher
            reviewsEnabled.addEventListener('change', (event) => {
                if (!event.currentTarget.checked) {
                    multiSetinReview.checked = false;
                }
            })
        }

        //when multiset switcher is enabled, enable also reviews in comment switcher
        multiSetinReview.addEventListener('change', (event) => {
            if (event.currentTarget.checked === true) {
                //if it is classic editor, check reviewsEnabled on true
                if(reviewsEnabled !== null) {
                    reviewsEnabled.checked = true;
                } else {
                    //if this is gutenberg, use document.getElementById on change to get the current state and check it
                    document.getElementById('yasr-comment-reviews-disabled-switch').checked = true;
                }

                //update the hidden field, if only one multiset is used
                if(yasrProReviewSetid !== null) {
                    yasrProReviewSetid.value = setId;
                }
            }
            else {
                //update the hidden field, if only one multiset is used
                if(yasrProReviewSetid !== null) {
                    yasrProReviewSetid.value = '';
                }
            }
        });

    }

    if (nMultiSet > 1) {
        jQuery('#yasr_select_set').on("change", function () {

            //get the multi data
            //overwrite setID
            setId = jQuery('#yasr_select_set').val();

            jQuery("#yasr-loader-select-multi-set").show();

            yasrPrintAdminMultiSet(setId, postId, nMultiSet);

            //update hidden field
            document.getElementById('yasr-multiset-id').value = setId;

            if(yasrProReviewSetid !== null && yasrProReviewSetid !== '') {
                if(yasrProReviewSetid.value === setId) {
                    //update hidden field
                    multiSetinReview.checked = true;
                } else {
                    multiSetinReview.checked = false;
                }
            }

            return false; // prevent default click action from happening!
        });

    }

}

//print the table
function yasrPrintAdminMultiSet(setId, postid, nMultiSet) {

    const data_id = {
        action: 'yasr_send_id_nameset',
        set_id:  setId,
        post_id: postid
    };

    jQuery.post(ajaxurl, data_id, function (response) {
        //Hide the loader near the select only if more multiset are used
        if (nMultiSet > 1) {
            document.getElementById('yasr-loader-select-multi-set').style.display = 'none';
        }

        let yasrMultiSetValue   = JSON.parse(response);
        let tableAuthorMulti    = document.getElementById('yasr-table-multi-set-admin');
        let tableAuthorVisitor  = document.getElementById('yasr-table-multi-set-admin-visitor');

        yasrReturnTableMultiset(yasrMultiSetValue, tableAuthorMulti);
        yasrReturnTableMultiset(yasrMultiSetValue, tableAuthorVisitor, false);

        //Set rater for divs
        yasrSetRaterAdminMulti();
        yasrSetRaterAdminMulti(false);

        let spanWithSetID = document.getElementsByClassName('yasr-editor-multiset-id');

        for (let i = 0; i < spanWithSetID.length; i++) {
            spanWithSetID[i].innerText = setId;
        }

    });

    return false; // prevent default click action from happening!

}

/**
 *
 * @param yasrMultiSetValue
 * @param table
 * @param authorMultiset
 */
function yasrReturnTableMultiset (yasrMultiSetValue, table, authorMultiset=true) {

    let content = '';
    let divClass   = 'yasr-multiset-admin'

    for (let i = 0; i < yasrMultiSetValue.length; i++) {
        let valueName = yasrMultiSetValue[i]['name'];
        let valueRating = 0;
        let readonly    = true;

        if(authorMultiset !== false) {
            valueRating = yasrMultiSetValue[i]['average_rating'];
            readonly    = false;
            divClass    = 'yasr-multiset-admin-author'
        }

        let valueID = yasrMultiSetValue[i]['id'];

        content += '<tr>';
        content += '<td>' + valueName + '</td>';
        content += '<td><div class='+divClass+' id="yasr-multiset-admin-' + uuidv4() + '" data-rating="'
            + valueRating + '" data-multi-idfield="' + valueID + '" data-readonly="'+ readonly +'"></div>';
        content += '</td>';
        content += '</tr>';
    }

    if(authorMultiset === false) {
        let button = '<tr><td colspan="2"><input type="submit" class="button button-primary" value="Submit!" disabled></td></tr>';
        content += button;
    }

    table.innerHTML = content;

}

/**
 *
 * @param authorMultiset
 */
function yasrSetRaterAdminMulti(authorMultiset=true) {

    let divClass;
    if(authorMultiset !== false) {
        divClass = 'yasr-multiset-admin-author';
    } else {
        divClass = 'yasr-multiset-admin';
    }

    const yasrMultiSetAdmin = document.getElementsByClassName(divClass);

    //an array with all the ratings objects
    let ratingArray = [];
    let ratingValue = false;

    for (let i = 0; i < yasrMultiSetAdmin.length; i++) {

        (function (i) {

            let htmlId = yasrMultiSetAdmin.item(i).id;
            let elem = document.getElementById(htmlId);

            let setIdField       = parseInt(elem.getAttribute('data-multi-idfield'));
            let ratingOnLoad     = parseInt(elem.getAttribute('data-rating'));
            //convert into boolean https://stackoverflow.com/a/264037/3472877
            let readOnly         = (elem.getAttribute('data-readonly') === 'true');

            let ratingObjectOnLoad = {
                field: setIdField,
                rating: ratingOnLoad
            };

            //creating rating array
            ratingArray.push(ratingObjectOnLoad);

            const rateCallback =  function (rating, done) {
                rating = rating.toFixed(1);
                //Be sure is a number and not a string
                rating = parseFloat(rating);
                this.setRating(rating); //Set the rating

                //loop the array with existing rates
                for (let j = 0; j < ratingArray.length; j++) {
                    //if the field of the array is the same of the rated field, get the rating
                    if(ratingArray[j].field === setIdField) {
                        //the selected rating overwrite the existing one
                        ratingArray[j].rating = rating;
                    }
                }

                ratingValue = JSON.stringify(ratingArray);

                //update hidden field
                document.getElementById('yasr-multiset-author-votes').value = ratingValue;

                done();
            }

            yasrSetRaterValue(32, htmlId, false, 0.5, readOnly, false, rateCallback);

        })(i);

    } //End for
}//End function

/****** End Yasr Metabox Multple Rating  ******/

function shortcodeCreator () {
    var data = {
        action: 'yasr_create_shortcode'
    };

    jQuery.get(ajaxurl, data, function (button_content) {
        jQuery(button_content).appendTo('body').hide();
        yasrShortcodeCreator();
    });
}

/****** Yasr Ajax Page ******/
// When click on chart hide tab-main and show tab-charts

function yasrShortcodeCreator() {

    let nMultiSet = false

    if(document.getElementById('yasr-editor-multiset-container') !== null) {
        nMultiSet = true;
    }

    const linkDoc = document.getElementById('yasr-tinypopup-link-doc');

    // When click on main tab hide tab-main and show tab-charts
    jQuery('#yasr-link-tab-main').on("click", function () {
        jQuery('.yasr-nav-tab').removeClass('nav-tab-active');
        jQuery('#yasr-link-tab-main').addClass('nav-tab-active');

        jQuery('.yasr-content-tab-tinymce').hide();
        jQuery('#yasr-content-tab-main').show();

        linkDoc.setAttribute('href', 'https://yetanotherstarsrating.com/yasr-basics-shortcode/?utm_source=wp-plugin&utm_medium=tinymce-popup&utm_campaign=yasr_editor_screen');
    });

    jQuery('#yasr-link-tab-charts').on("click", function () {

        jQuery('.yasr-nav-tab').removeClass('nav-tab-active');
        jQuery('#yasr-link-tab-charts').addClass('nav-tab-active');

        jQuery('.yasr-content-tab-tinymce').hide();
        jQuery('#yasr-content-tab-charts').show();

        linkDoc.setAttribute('href', 'https://yetanotherstarsrating.com/yasr-rankings/?utm_source=wp-plugin&utm_medium=tinymce-popup&utm_campaign=yasr_editor_screen');

    });

    // Add shortcode for overall rating
    jQuery('#yasr-overall').on("click", function () {
        jQuery('#yasr-overall-choose-size').toggle('slow');
    });

    //Add shortcode for visitors rating
    jQuery('#yasr-visitor-votes').on("click", function () {
        jQuery('#yasr-visitor-choose-size').toggle('slow');
    });

    jQuery('.yasr-tinymce-shortcode-buttons').on("click", function () {
        let shortcode = this.getAttribute('data-shortcode');

        if (tinyMCE.activeEditor == null) {
            //this is for tinymce used in text mode
            jQuery("#content").append(shortcode);
        } else {
            // inserts the shortcode into the active editor
            tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        }

        // close
        tb_remove();
        //jQuery('#yasr-tinypopup-form').dialog('close');

    });

    if (nMultiSet === true) {
        //Add shortcode for multiple set
        jQuery('#yasr-insert-multiset-select').on("change", function () {
            var setType     = jQuery("input:radio[name=yasr_tinymce_pick_set]:checked").val();
            var visitorSet  = jQuery("#yasr-allow-vote-multiset").is(':checked');
            var showAverage = jQuery("#yasr-hide-average-multiset").is(':checked');
            let shortcode;

            if (!visitorSet) {
                shortcode = '[yasr_visitor_multiset setid=';
            } else {
                shortcode = '[yasr_multiset setid=';
            }

            shortcode += setType;

            if (showAverage) {
                shortcode += ' show_average=\'no\'';
            }

            shortcode += ']';

            // inserts the shortcode into the active editor
            if (tinyMCE.activeEditor == null) {
                //this is for tinymce used in text mode
                jQuery("#content").append(shortcode);
            } else {
                // inserts the shortcode into the active editor
                tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
            }

            // close
            tb_remove();
        });

    } //End if

} //End function

/****** End YAsr Ajax page ******/