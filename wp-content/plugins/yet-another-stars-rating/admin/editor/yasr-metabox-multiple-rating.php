<?php

/*

Copyright 2014 Dario Curvino (email : d.curvino@tiscali.it)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

if (!defined( 'ABSPATH')) {
    exit('You\'re not allowed to see this page');
} // Exit if accessed directly

$multi_set = YasrMultiSetData::returnMultiSetNames();
$post_id = get_the_ID();
wp_nonce_field('yasr_nonce_save_multi_values_action',      'yasr_nonce_save_multi_values');
wp_nonce_field('yasr_nonce_multiset_review_enabled_action','yasr_nonce_multiset_review_enabled');

global $wpdb;

$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the count number of rows of the last query

//this is always the first set id
$set_id = $multi_set[0]->set_id;
$set_id = (int)$set_id;

if ($n_multi_set > 1) {
    ?>
    <div style="margin-bottom: 15px">
        <?php esc_html_e("Choose which set you want to use", 'yet-another-stars-rating'); ?>
        <br />
        <label for="yasr_select_set">
            <select id="yasr_select_set" autocomplete="off">
                <?php
                    foreach ($multi_set as $name) {
                        echo "<option value='".esc_attr($name->set_id)."'>".esc_attr($name->set_name)."</option>";
                    } //End foreach
                ?>
            </select>
        </label>

        <span id="yasr-loader-select-multi-set" style="display:none;" >&nbsp;
            <img src="<?php echo esc_url(YASR_IMG_DIR . "/loader.gif") ?>" alt="yasr-loader">
        </span>
    </div>

    <?php 

} //End if ($n_multi_set>1)

?>

<div class="yasr-settings-row-48" style="justify-content: left;">
    <div id="yasr-editor-multiset-container"
        data-nmultiset="<?php echo esc_attr($n_multi_set) ?>"
        data-setid="<?php echo esc_attr($set_id) ?>"
        data-postid="<?php echo esc_attr($post_id) ?>">

        <input type="hidden" name="yasr_multiset_author_votes" id="yasr-multiset-author-votes" value="">
        <input type="hidden" name="yasr_multiset_id" id="yasr-multiset-id" value="<?php echo esc_attr($set_id) ?>">
        <input type="hidden" name="yasr_pro_review_setid" id="yasr-pro-review-setid"
               value="<?php esc_attr_e($post->yasr_pro_review_setid) ?>">

        <table class="yasr_table_multi_set_admin" id="yasr-table-multi-set-admin">
        </table>

        <div class="yasr-multi-set-admin-explain">
            <div>
                <?php
                    $span = '<span title="'.esc_attr__('Copy Shortcode', 'yet-another-stars-rating').'">
                                <code id="yasr-editor-copy-readonly-multiset"
                                      class="yasr-copy-shortcode">[yasr_multiset setid=<span class="yasr-editor-multiset-id"></span>]</code>
                             </span>';

                    $text_multiset = sprintf(esc_html__( 'Rate each element, and copy this shortcode %s where you want to display this rating.',
                        'yet-another-stars-rating'), $span);

                    echo wp_kses_post($text_multiset);
                ?>
            </div>
        </div>

    </div>

    <div id="yasr-visitor-multiset-container">
        <table class="yasr_table_multi_set_admin" id="yasr-table-multi-set-admin-visitor">

        </table>

        <div class="yasr-multi-set-admin-explain">
            <?php
                esc_html_e( 'If, you want allow your visitor to vote on this multiset, use this shortcode',
                'yet-another-stars-rating' );
            ?>
            <span title="<?php esc_attr_e('Copy Shortcode', 'yet-another-stars-rating') ?>">
                <code id="yasr-editor-copy-visitor-multiset"
                      class="yasr-copy-shortcode">[yasr_visitor_multiset setid=<span class="yasr-editor-multiset-id"></span>]</code>
            </span>

            <br />
            <?php esc_html_e('This is just a preview, you can\'t vote here.', 'yet-another-stars-rating');?>
        </div>
    </div>

</div>

<p></p>

<div style="width: 98%">
    <div class="yasr-metabox-editor-pro-only-box-padding">
        <div class="yasr-metabox-editor-title-pro-only">
            <?php
                esc_html_e('Pro Only features', 'yet-another-stars-rating');
                echo '&nbsp;'.YASR_LOCKED_FEATURE;
            ?>
        </div>

        <div class="yasr-settings-row">
            <div class="yasr-settings-col-30">
                <div class="yasr-metabox-editor-title">
                    <?php
                        esc_html_e('Insert this Multi Set in the comment form?', 'yet-another-stars-rating');
                    ?>
                </div>
                <div class="yasr-onoffswitch-big" id="yasr-pro-multiset-review-switcher-container">
                    <input type="checkbox"
                           name="yasr_pro_multiset_review_enabled"
                           class="yasr-onoffswitch-checkbox"
                           value='yes'
                           id="yasr-pro-multiset-review-switcher"
                        <?php
                            //required to check !== otherwise setid=0 is checked even if not enabled
                            if ($post->yasr_pro_review_setid !== '' && (int)$post->yasr_pro_review_setid === $set_id) {
                                echo " checked='checked' ";
                            }
                            echo YASR_LOCKED_FEATURE_HTML_ATTRIBUTE;
                        ?>
                    >
                    <label class="yasr-onoffswitch-label" for="yasr-pro-multiset-review-switcher">
                        <span class="yasr-onoffswitch-inner"></span>
                        <span class="yasr-onoffswitch-switch"></span>
                    </label>
                </div>
                <div class="yasr-element-row-container-description">
                    <?php
                        esc_html_e('By enabling this, all ratings fields will be mandatory.',
                            'yet-another-stars-rating')
                    ?>
                </div>
            </div>

            <div class="yasr-settings-col-65">
                <div class="yasr-metabox-editor-title">
                    <?php esc_html_e('Shortcodes', 'yet-another-stars-rating'); ?>
                </div>

                <div>
                    <span title="<?php esc_attr_e('Copy Shortcode', 'yet-another-stars-rating') ?>">
                        <code id="yasr-editor-copy-average-multiset" class="yasr-copy-shortcode">
                            [yasr_pro_average_multiset setid=<span class="yasr-editor-multiset-id"></span>]</code>
                    </span>

                    <span>
                        <?php esc_html_e('Use this shortcode to print only the average of '); ?>
                        [yasr_multiset setid=<span class="yasr-editor-multiset-id"></span>]
                    </span>
                </div>

                <p></p>

                <div>
                    <span title="<?php esc_attr_e('Copy Shortcode', 'yet-another-stars-rating') ?>">
                        <code id="yasr-editor-copy-average-vvmultiset" class="yasr-copy-shortcode">
                        [yasr_pro_average_visitor_multiset setid=<span class="yasr-editor-multiset-id"></span>]</code>
                    </span>

                    <span>
                        <?php esc_html_e('Use this shortcode to print only the average of '); ?>
                        [yasr_visitor_multiset setid=<span class="yasr-editor-multiset-id"></span>]
                    </span>
                </div>

                <p></p>

                <div>
                    <span title="<?php esc_attr_e('Copy Shortcode', 'yet-another-stars-rating') ?>">
                        <code id="yasr-editor-copy-comments-multiset" class="yasr-copy-shortcode">
                            [yasr_pro_average_comments_multiset setid=<span class="yasr-editor-multiset-id"></span>]</code>
                    </span>

                    <span>
                        <?php esc_html_e('This shortcode will print a Multi Set with all the ratings given through the comment form '); ?>
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>