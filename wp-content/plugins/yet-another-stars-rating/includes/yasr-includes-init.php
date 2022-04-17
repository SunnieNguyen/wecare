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

//e.g. http://localhost/plugin_development/wp-content/plugins/yet-another-stars-rating/includes/js/
define('YASR_JS_DIR_INCLUDES', plugins_url() . '/' . YASR_RELATIVE_PATH_INCLUDES . '/js/');
//CSS directory absolute URL
define('YASR_CSS_DIR_INCLUDES', plugins_url() . '/' . YASR_RELATIVE_PATH_INCLUDES . '/css/');

global $wpdb;
//defining tables names
define('YASR_LOG_TABLE',              $wpdb->prefix . 'yasr_log');
define('YASR_LOG_MULTI_SET',          $wpdb->prefix . 'yasr_log_multi_set');
define('YASR_MULTI_SET_NAME_TABLE',   $wpdb->prefix . 'yasr_multi_set');
define('YASR_MULTI_SET_FIELDS_TABLE', $wpdb->prefix . 'yasr_multi_set_fields');

require YASR_ABSOLUTE_PATH_INCLUDES . '/yasr-includes-functions.php';
require YASR_ABSOLUTE_PATH_INCLUDES . '/yasr-includes-db-functions.php';
require YASR_ABSOLUTE_PATH_INCLUDES . '/yasr-widgets.php';


/**
 * Callback function for the spl_autoload_register above.
 *
 * @param $class
 */
function yasr_autoload_includes_classes($class) {
    /**
     * If the class being requested does not start with 'Yasr' prefix,
     * it's not in Yasr Project
     */
    if (0 !== strpos($class, 'Yasr')) {
        return;
    }
    $file_name =  YASR_ABSOLUTE_PATH_INCLUDES . '/classes/' . $class . '.php';

    // check if file exists, just to be sure
    if (file_exists($file_name)) {
        require($file_name);
    }

}

//AutoLoad Yasr Classes, only when a object is created
spl_autoload_register('yasr_autoload_includes_classes');

require YASR_ABSOLUTE_PATH_INCLUDES . '/shortcodes/yasr-shortcode-functions.php';


/****** Getting options ******/
//Get general options

$settings               = new YasrSettingsValues();
$yasr_general_settings  = $settings->getGeneralSettings();

if(isset($yasr_general_settings['auto_insert_enabled'])) {
    define('YASR_AUTO_INSERT_ENABLED', (int)$yasr_general_settings['auto_insert_enabled']);
} else {
    define('YASR_AUTO_INSERT_ENABLED', 0);
}

if (YASR_AUTO_INSERT_ENABLED === 1) {
    define('YASR_AUTO_INSERT_WHAT',  $yasr_general_settings['auto_insert_what']);
    define('YASR_AUTO_INSERT_WHERE', $yasr_general_settings['auto_insert_where']);
    define('YASR_AUTO_INSERT_ALIGN', $yasr_general_settings['auto_insert_align']);
    define('YASR_AUTO_INSERT_SIZE',  $yasr_general_settings['auto_insert_size']);
    define('YASR_AUTO_INSERT_EXCLUDE_PAGES', $yasr_general_settings['auto_insert_exclude_pages']);
    define('YASR_AUTO_INSERT_CUSTOM_POST_ONLY', $yasr_general_settings['auto_insert_custom_post_only']);
}  else {
    define('YASR_AUTO_INSERT_WHAT', null);
    define('YASR_AUTO_INSERT_WHERE', null);
    define('YASR_AUTO_INSERT_ALIGN', null);
    define('YASR_AUTO_INSERT_SIZE', null);
    define('YASR_AUTO_INSERT_EXCLUDE_PAGES', null);
    define('YASR_AUTO_INSERT_CUSTOM_POST_ONLY', null);
}

define('YASR_STARS_TITLE', $yasr_general_settings['stars_title']);

if (YASR_STARS_TITLE === 'yes') {
    define('YASR_STARS_TITLE_WHAT',          $yasr_general_settings['stars_title_what']);
    define('YASR_STARS_TITLE_EXCLUDE_PAGES', $yasr_general_settings['stars_title_exclude_pages']);
    define('YASR_STARS_TITLE_WHERE',         $yasr_general_settings['stars_title_where']);
} else {
    define('YASR_STARS_TITLE_WHAT', null);
    define('YASR_STARS_TITLE_EXCLUDE_PAGES', null);
    define('YASR_STARS_TITLE_WHERE', null);
}

define('YASR_SHOW_OVERALL_IN_LOOP',       $yasr_general_settings['show_overall_in_loop']);
define('YASR_SHOW_VISITOR_VOTES_IN_LOOP', $yasr_general_settings['show_visitor_votes_in_loop']);
define('YASR_VISITORS_STATS',             $yasr_general_settings['visitors_stats']);
define('YASR_ALLOWED_USER',               $yasr_general_settings['allowed_user']);
define('YASR_ENABLE_IP',                  $yasr_general_settings['enable_ip']);
define('YASR_ITEMTYPE',                   $yasr_general_settings['snippet_itemtype']);
define('YASR_PUBLISHER_TYPE',             $yasr_general_settings['publisher']);
define('YASR_PUBLISHER_NAME',             $yasr_general_settings['publisher_name']);

if (isset($yasr_general_settings['publisher_logo'])
    && (filter_var($yasr_general_settings['publisher_logo'], FILTER_VALIDATE_URL) !== false)) {
    define('YASR_PUBLISHER_LOGO', $yasr_general_settings['publisher_logo']);
} else {
    define('YASR_PUBLISHER_LOGO', get_site_icon_url());
}

define('YASR_ENABLE_AJAX', $yasr_general_settings['enable_ajax']);

$style_options = $settings->getStyleSettings();

//Get stored style options
//To better support php version < 7, I can't use an array into define
//Also, I can't use const here, because it only works with primitive values
//https://stackoverflow.com/questions/2447791/php-define-vs-const
define('YASR_STYLE_OPTIONS', json_encode($style_options));

define('YASR_STARS_SET',        $style_options['stars_set_free']);
define('YASR_SCHEME_COLOR',     $style_options['scheme_color_multiset']);
define('YASR_CUSTOM_CSS_RULES', $style_options['textarea']);

//Multi set options
$multi_set_options               = $settings->getMultiSettings();

define('YASR_MULTI_SHOW_AVERAGE', $multi_set_options['show_average']);

/****** End Getting options ******/

define('YASR_LOADER_IMAGE', YASR_IMG_DIR . '/loader.gif');

//Text for button in settings pages
$save_settings_text = __('Save All Settings', 'yet-another-stars-rating');
define('YASR_SAVE_All_SETTINGS_TEXT', $save_settings_text);

//To better support php version < 7, I can't use an array into define
//I can use const here, because it is a primitive value
//https://stackoverflow.com/questions/1290318/php-constants-containing-arrays
//https://stackoverflow.com/questions/2447791/php-define-vs-const
const YASR_SUPPORTED_SCHEMA_TYPES =
    array (
        'BlogPosting',
        'Book',
        'Course',
        'CreativeWorkSeason',
        'CreativeWorkSeries',
        'Episode',
        'Event',
        'Game',
        'LocalBusiness',
        'MediaObject',
        'Movie',
        'MusicPlaylist',
        'MusicRecording',
        'Organization',
        'Product',
        'Recipe',
        'SoftwareApplication'
    );

//here the array member must contain main itemtype name
//e.g. yasr_softwareapplication contain 'SoftwareApplication'
const YASR_SUPPORTED_SCHEMA_TYPES_ADDITIONAL_FIELDS =
    array(
        'yasr_schema_title',
        'yasr_book_author',
        'yasr_book_bookedition',
        'yasr_book_bookformat',
        'yasr_book_isbn',
        'yasr_book_number_of_pages',
        'yasr_localbusiness_address',
        'yasr_localbusiness_pricerange',
        'yasr_localbusiness_telephone',
        'yasr_movie_actor',
        'yasr_movie_datecreated',
        'yasr_movie_director',
        'yasr_movie_duration',
        'yasr_product_brand',
        'yasr_product_global_identifier_select',
        'yasr_product_global_identifier_value',
        'yasr_product_price',
        'yasr_product_price_availability',
        'yasr_product_price_currency',
        'yasr_product_price_url',
        'yasr_product_price_valid_until',
        'yasr_product_sku',
        'yasr_recipe_cooktime',
        'yasr_recipe_description',
        'yasr_recipe_keywords',
        'yasr_recipe_nutrition',
        'yasr_recipe_preptime',
        'yasr_recipe_recipecategory',
        'yasr_recipe_recipecuisine',
        'yasr_recipe_recipeingredient',
        'yasr_recipe_recipeinstructions',
        'yasr_recipe_video',
        'yasr_softwareapplication_category',
        'yasr_softwareapplication_os',
        'yasr_softwareapplication_price',
        'yasr_softwareapplication_price_availability',
        'yasr_softwareapplication_price_currency',
        'yasr_softwareapplication_price_url',
        'yasr_softwareapplication_price_valid_until',
    );

//run includes filters
$yasr_includes_filter = new YasrIncludesFilters();
$yasr_includes_filter->filterCustomTexts($yasr_general_settings);

//support for caching plugins
$yasr_includes_filter->cachingPluginSupport();

$init_ajax = new YasrShortcodesAjax();
$init_ajax->init();

add_action('plugins_loaded', static function () {
    define('YASR_FIRST_SETID', YasrMultiSetData::returnFirstSetId());
    define ('YASR_CATCH_INFINITE_SCROLL_INSTALLED', yasr_is_catch_infinite_sroll_installed());
});

//Load rest API
require YASR_ABSOLUTE_PATH_INCLUDES . '/rest/yasr-rest.php';
