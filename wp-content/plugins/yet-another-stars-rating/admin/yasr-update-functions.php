<?php

if (!defined('ABSPATH')) {
    exit('You\'re not allowed to see this page');
} // Exit if accessed directly

//Update version number and backward compatibility functions
//declared on yasr-update-functions.php
add_action('plugins_loaded', 'yasr_update_version');

function yasr_update_version() {
    //do only in admin
    if (is_admin()) {
        global $wpdb;
        $yasr_stored_options = get_option('yasr_general_options');

        if (YASR_VERSION_INSTALLED !== false) {
            //In version 2.6.6 %overall_rating% pattern is replaced with %rating%
            //Remove March 2023
            if (version_compare(YASR_VERSION_INSTALLED, '2.6.6') === -1) {
                if(array_key_exists('text_before_overall', $yasr_stored_options)) {
                    $yasr_stored_options['text_before_overall'] =
                        str_replace('%overall_rating%', '%rating%', $yasr_stored_options['text_before_overall']);

                    update_option('yasr_general_options', $yasr_stored_options);
                }
            }

            //In version 2.7.4 option "text_before_stars" is removed.
            //if it was set to 0, be sure that text before overall is empty
            //Remove May 2023
            if (version_compare(YASR_VERSION_INSTALLED, '2.7.4') === -1) {
                if (array_key_exists('text_before_stars', $yasr_stored_options)) {
                    if($yasr_stored_options['text_before_stars'] === 0) {
                        $yasr_stored_options['text_before_overall']  = '';

                        update_option('yasr_general_options', $yasr_stored_options);
                    }
                }
            }

            //In version 2.9.7 the column comment_id is added
            //Remove Dec 2023
            if (version_compare(YASR_VERSION_INSTALLED, '2.9.7') === -1) {
                $wpdb->query("ALTER TABLE " . YASR_LOG_MULTI_SET . " ADD comment_id bigint(20) NOT NULL AFTER post_id");
            }

        } //Endif yasr_version_installed !== false


        /****** End backward compatibility functions ******/
        if (YASR_VERSION_INSTALLED !== YASR_VERSION_NUM) {
            update_option('yasr-version', YASR_VERSION_NUM);
        }

    }

}
