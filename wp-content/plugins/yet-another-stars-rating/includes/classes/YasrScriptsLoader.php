<?php

/**
 * @author Dario Curvino <@dudo>
 * @since  3.0.4
 * Class YasrScriptsLoader
 */
class YasrScriptsLoader {

    /**
     * This function enqueue the js scripts required on both admin and frontend
     *
     * @author Dario Curvino <@dudo>
     * @since 2.8.5
     */
    public static function loadRequiredJs() {
        wp_enqueue_script('jquery');

        wp_enqueue_script(
            'yasr-global-functions',
            YASR_JS_DIR_INCLUDES . 'yasr-globals.js',
            'yasr-global-data',
            YASR_VERSION_NUM,
            true
        );

        if (defined('YASR_CATCH_INFINITE_SCROLL_INSTALLED') && YASR_CATCH_INFINITE_SCROLL_INSTALLED === true) {
            $array_dep = array('jquery', 'yasr-global-functions', 'wp-i18n', 'yasr-global-data', 'wp-element');

            //laod tippy only if the shortcode has loaded it
            $tippy_loaded = wp_script_is('tippy');

            if ($tippy_loaded) {
                $array_dep[] = 'tippy';
            }

            wp_enqueue_script(
                'yasr_catch_infinite',
                YASR_JS_DIR_INCLUDES . 'catch-inifite-scroll.js',
                $array_dep,
                YASR_VERSION_NUM,
                true
            );
        }

    }

    /**
     * Enqueue visitorVotes.js file
     *
     * @author Dario Curvino <@dudo>
     * @since  2.8.5
     */
    public static function loadVVJs() {
        $array_dep    = array('jquery', 'yasr-global-functions', 'wp-i18n', 'yasr-global-data');
        $tippy_loaded = wp_script_is('tippy');

        if ($tippy_loaded) {
            $array_dep[] = 'tippy';
        }

        wp_enqueue_script(
            'yasr-front-vv',
            YASR_JS_DIR_INCLUDES . 'shortcodes/visitorVotes.js',
            $array_dep,
            YASR_VERSION_NUM,
            true
        );
    }

    /***
     * Enqueue overall-multiset.js file
     *
     * @author Dario Curvino <@dudo>
     * @since  2.8.8
     */
    public static function loadOVMultiJs() {
        wp_enqueue_script(
            'yasr-ov-multi',
            YASR_JS_DIR_INCLUDES . 'shortcodes/overall-multiset.js',
            array('jquery', 'yasr-global-functions', 'wp-i18n', 'yasr-global-data'),
            YASR_VERSION_NUM,
            true
        );
    }

    /**
     * Enqueue rankings.js file
     *
     * @author Dario Curvino <@dudo>
     * @since  2.8.8
     */
    public static function loadRankingsJs() {
        wp_enqueue_script(
            'yasr-rankings',
            YASR_JS_DIR_INCLUDES . 'shortcodes/rankings.js',
            array('jquery', 'yasr-global-functions', 'wp-i18n', 'wp-element', 'yasr-global-data'),
            YASR_VERSION_NUM,
            true
        );
    }

    /**
     * Load file yasr-log-users-fronted.js
     *
     * @author Dario Curvino <@dudo>
     * @since  3.0.4
     */
    public static function loadLogUsersFrontend() {
        wp_enqueue_script(
            'yasr-log-users-frontend',
            YASR_JS_DIR_INCLUDES . 'shortcodes/yasr-log-users-frontend.js',
            array('jquery'),
            YASR_VERSION_NUM,
            true
        );
    }

    /**
     * Load tippy if needed
     *
     * @author Dario Curvino <@dudo>
     * @since  2.8.5
     */
    public static function loadTippy() {
        wp_enqueue_script(
            'tippy',
            YASR_JS_DIR_INCLUDES . 'tippy.all.min.js',
            '',
            '3.6.0',
            true
        );
    }

    /******************* Admin methods *******************/

    /**
     * Load Yasr-admin.js
     *
     * @author Dario Curvino <@dudo>
     * @since 3.0.4
     */
    public static function loadYasrAdmin () {
        wp_enqueue_script(
            'yasradmin',
            YASR_JS_DIR_ADMIN . 'yasr-admin.js',
            array('jquery', 'tippy', 'yasr-global-functions'),
            YASR_VERSION_NUM,
            true
        );
    }

    /**
     * Load yasr-editor-screen.js
     *
     * @author Dario Curvino <@dudo>
     * @since 3.0.4
     */
    public static function loadClassicEditor() {
        wp_enqueue_script(
            'yasr-classic-editor',
            YASR_JS_DIR_ADMIN . 'yasr-editor-screen.js',
            array('jquery', 'yasr-global-functions'),
            YASR_VERSION_NUM,
            true
        );
    }

    /**
     * Enqueue the code editor
     *
     * @author Dario Curvino <@dudo>
     * @since  3.0.4
     */
    public static function loadCodeEditor() {
        $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
        wp_localize_script('jquery', 'yasr_cm_settings', $cm_settings);
    }

    /**
     * Load yasr-settings.js
     *
     * @author Dario Curvino <@dudo>
     * @since  3.0.4
     */
    public static function loadAdminSettings () {
        wp_enqueue_script(
            'yasradmin-settings',
            YASR_JS_DIR_ADMIN . 'yasr-settings.js',
            array('jquery', 'yasradmin', 'wp-element'),
            YASR_VERSION_NUM,
            true
        );
    }

    /**
     * Load yasr-pricing-page.js
     *
     * @author Dario Curvino <@dudo>
     * @since  3.0.4
     */
    public static function loadPrincingPage () {
        wp_enqueue_script(
            'yasrjs-pricing',
            YASR_JS_DIR_ADMIN . 'yasr-pricing-page.js',
            array('wp-element', 'yasradmin'),
            YASR_VERSION_NUM,
            true
        );
    }

}