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

if (!defined('ABSPATH')) {
    exit('You\'re not allowed to see this page');
} // Exit if accessed directly

//action is in the main file
function yasr_on_create_blog(WP_Site $new_site) {
    if (is_plugin_active_for_network( 'yet-another-stars-rating/yet-another-stars-rating.php' )) {
        switch_to_blog($new_site->blog_id);
        YasrOnInstall::createTables();
        restore_current_blog();
    }
}

// Deleting the table whenever a blog is deleted
function yasr_on_delete_blog($tables) {
    global $wpdb;

    $prefix = $wpdb->prefix . 'yasr_';  //Table prefix

    $yasr_multi_set_table    = $prefix . 'multi_set';
    $yasr_multi_set_fields   = $prefix . 'multi_set_fields';
    $yasr_log_multi_set      = $prefix . 'log_multi_set';
    $yasr_log_table          = $prefix . 'log';

    $tables[] = $yasr_multi_set_table;
    $tables[] = $yasr_multi_set_fields;
    $tables[] = $yasr_log_multi_set;
    $tables[] = $yasr_log_table;

    return $tables;
}

add_action('admin_enqueue_scripts', 'yasr_add_admin_scripts');

//$hook contain the current page in the admin side
function yasr_add_admin_scripts($hook) {
    global $yasr_settings_page;

    if ($hook === 'yet-another-stars-rating_page_yasr_pricing_page'
        || $hook === 'yet-another-stars-rating_page_yasr_settings_page-pricing') {

        if(!isset($_GET['trial'])) {
            wp_enqueue_style(
                'yasrcss-pricing',
                YASR_CSS_DIR_ADMIN . 'yasr-pricing-page.css',
                false,
                YASR_VERSION_NUM
            );

            YasrScriptsLoader::loadPrincingPage();
        }
    }

    if ($hook === 'index.php'
        || $hook === 'edit.php'
        || $hook === 'post.php'
        || $hook === 'post-new.php'
        || $hook === 'edit-comments.php'
        || $hook === 'term.php'
        || $hook === 'appearance_page_gutenberg-edit-site'
        || $hook === $yasr_settings_page
        || $hook === 'yet-another-stars-rating_page_yasr_stats_page'
        || $hook === 'yet-another-stars-rating_page_yasr_pricing_page'
        || $hook === 'yet-another-stars-rating_page_yasr_settings_page-pricing'
    ) {
        YasrScriptsLoader::loadRequiredJs();

        do_action('yasr_add_admin_scripts_begin', $hook);

        YasrScriptsLoader::loadTippy();
        YasrScriptsLoader::loadYasrAdmin();

        wp_enqueue_style(
            'yasrcss',
            YASR_CSS_DIR_ADMIN . 'yasr-admin.css',
            false,
            YASR_VERSION_NUM
        );

        do_action('yasr_add_admin_scripts_end', $hook);
    }

    if ($hook === 'post.php' || $hook === 'post-new.php') {
        YasrScriptsLoader::loadClassicEditor();
    }

    //add this only in yasr setting page (admin.php?page=yasr_settings_page)
    if ($hook === $yasr_settings_page) {
        YasrScriptsLoader::loadCodeEditor();
        YasrScriptsLoader::loadAdminSettings();
    }

}

/* Hook to admin_menu the yasr_add_pages function above */
add_action('admin_menu', 'yasr_add_pages');

function yasr_add_pages() {

    if (!current_user_can('manage_options')) {
        return;
    }

    global $yasr_settings_page;

    $settings_menu_title =  '<span class="dashicons dashicons-admin-settings"></span>&nbsp;' .
        esc_html__('Settings', 'yet-another-stars-rating');

    $stats_menu_title    = '<span class="dashicons dashicons-chart-line"></span>&nbsp;' .
        esc_html__('Manage Ratings', 'yet-another-stars-rating');

    //Add Settings Page
    $yasr_settings_page = add_menu_page(
        'Yet Another Stars Rating: settings',
        'Yet Another Stars Rating', //Menu Title
        'manage_options', //capability
        'yasr_settings_page', //menu slug
        'yasr_settings_page_callback', //The function to be called to output the content for this page.
        'dashicons-star-half'
    );

    add_submenu_page(
        'yasr_settings_page',
        'Yet Another Stars Rating: settings',
        $settings_menu_title,
        'manage_options',
        'yasr_settings_page'
    );

    add_submenu_page(
        'yasr_settings_page',
        'Yet Another Stars Rating: All Ratings',
        $stats_menu_title,
        'manage_options',
        'yasr_stats_page',
        'yasr_stats_page_callback'
    );

    //Filter the pricing page only if trial is not set
    if(isset($_GET['page']) && $_GET['page'] === 'yasr_settings_page-pricing' && !isset($_GET['trial'])) {
        yasr_fs()->add_filter( 'templates/pricing.php', 'yasr_pricing_page_callback' );
    }

    if (yasr_fs()->is_free_plan() && !yasr_fs()->is_trial()) {
        global $submenu;
        $permalink                       = '#';
        $contact_us_string               = YASR_LOCKED_FEATURE . '&nbsp;' .
            esc_html__('Contact Us', 'yet-another-stars-rating');

        $submenu['yasr_settings_page'][] = array($contact_us_string, 'manage_options', $permalink);
    }

}

// Settings Page Content
function yasr_settings_page_callback() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'yet-another-stars-rating'));
    }

    include(YASR_ABSOLUTE_PATH_ADMIN . '/settings/yasr-settings-page.php');
} //End yasr_settings_page_content


function yasr_stats_page_callback() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'yet-another-stars-rating'));
    }

    include(YASR_ABSOLUTE_PATH_ADMIN . '/settings/yasr-stats-page.php');
}

function yasr_pricing_page_callback() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'yet-another-stars-rating'));
    }

    include(YASR_ABSOLUTE_PATH_ADMIN . '/settings/yasr-pricing-page.php');
}


/**
 * Check if the current page is the Gutenberg block editor.
 *
 * @since  2.2.3
 *
 * @return bool
 */
function yasr_is_gutenberg_page() {
    if (function_exists('is_gutenberg_page') && is_gutenberg_page() ) {
        // The Gutenberg plugin is on.
        return true;
    }

    $current_screen = get_current_screen();

    if ($current_screen !== null
        && method_exists($current_screen, 'is_block_editor')
        && $current_screen->is_block_editor() ) {
        // Gutenberg page on 5+.
        return true;
    }

    return false;
}