<?php
/*
 * Generates inline CSS from Customizer settings.
 */
if (!function_exists('healfio_get_customizer_css')) {
    function healfio_get_customizer_css()
    {
        ob_start();

        get_template_part('template-parts/header-variables');

        echo '#main-header {position: relative; margin-bottom: 32px;}';
        echo '#site-footer {position: relative;}';
        echo '@media (min-width: 1200px) {#site-footer {margin-top: 20px;}}';
        echo '@media (min-width: 576px) and (max-width: 1199px) {#site-footer {margin-top: 70px;}}';
        echo '@media (max-width: 575px) {#site-footer {margin-top: 30px;}}';
        echo '@media (min-width: 1200px) and (max-width: 1490px) {:root {zoom: 0.85;}}'; // Fix MacBook 13-inch viewport size.
        echo '#bg-header, #bg-footer {position: absolute; width: 100%; height: 100%; top: 0;}';
        echo '#bg-header {z-index: -1; height: 120%;}';
        echo '#bg-footer {z-index: 1; margin-top: 40px;}';
        echo '#header-wave {margin-bottom: -1px; width: 100%;}';
        echo '#magic-search .search-submit {display: none;}';
        echo '.onsale .onsale-svg {height: 100%;}';
        echo '#bg-header * {fill: #e4e4e4;}';
        echo '#bg-footer * {fill: #e7e7e7;}';
        echo '#header-wave * {fill: transparent;} @media (max-width: 1199px){#header-wave {height: 40px;} :root #main-header{margin-bottom: 32px;}} @media (min-width: 1200px){#header-wave {height: 55px;} :root #main-header{margin-top: 50px; margin-bottom: 50px;}}';

        if (get_theme_mod('meta_cat_switcher', false)) {
            echo '.entry-categories {display: none;}';
        }

        if (get_theme_mod('meta_author_switcher', false)) {
            echo '.post-author {display: none;}';
        }

        if (get_theme_mod('meta_date_switcher', false)) {
            echo '.post-date {display: none;}';
        }

        if (get_theme_mod('meta_comm_switcher', false)) {
            echo '.post-comment-link {display: none;}';
        }

        if (get_theme_mod('meta_pr_cat_switcher', false)) {
            echo '.product_meta .posted_in {display: none;}';
        }

        // Disable icon before title
        if (get_theme_mod('icon_before_title_switcher', false)) {
            echo '.wrap-entry-categories-inner:before, .widget-title:before, .single-product .product_meta > span:before, form[name="checkout"] h4:before, .elementor-accordion .elementor-accordion-title:before, .ngg-album-compact h4 .ngg-album-desc:before, .wpcf7-form .theme-contact-form h6:before, .blog-tile .entry-categories-inner:before, .related.products h6:before, .upsells.products h6:before, .woocommerce div.product .woocommerce-tabs ul.tabs li.active a:before, .woocommerce div.product .woocommerce-tabs ul.tabs li a:before, .woocommerce div.product form.cart .variations label:before, #review_form .comment-reply-title:before, .woocommerce ul.product_list_widget li .reviewer:before, .woocommerce-result-count:before, .cart_totals h4:before, .woocommerce-MyAccount-navigation li a:before, .h5-styled:before {display: none;}';
        }

        // Disable header background
        if (get_theme_mod('header_background_switcher', false)) {
            echo '#bg-header * {stroke: transparent; fill: transparent;}';
        }

        // Disable footer background
        if (get_theme_mod('footer_background_switcher', false)) {
            echo '#bg-footer * {stroke: transparent; fill: transparent;}';
        }

        // Disable header search and cart icons
        if (get_theme_mod('header_search_and_cart_icons', false)) {
            echo '.header-icons {display: none;} @media (max-width: 1199px) {.header-info {margin-top: 1rem;}}';
        }

        // Disable sticky header on desktop
        if (get_theme_mod('disable_sticky_header_desktop', false)) {
            echo '@media (min-width: 1200px) {#pr-nav {position: absolute;}}';
        }

        return ob_get_clean();
    }
}