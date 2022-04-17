<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php bloginfo('charset'); ?>">
    <meta id="siteViewport" name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<nav id="pr-nav" class="primary-menu navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid primary-menu-inner px-0">
        <div class="top-wrap">
            <?php if (function_exists('the_custom_logo')) {
                if (has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<a class="custom-logo-link" href="' . esc_url(home_url()) . '"><h5 class="m-0 text-dark">' . get_bloginfo('name') . '</h5></a>';
                }
            } else {
                echo '<a class="custom-logo-link" href="' . esc_url(home_url()) . '"><h5 class="m-0 text-dark">' . get_bloginfo('name') . '</h5></a>';
            } ?>
            <button id="mobile-toggle" class="navbar-toggler animate-button collapsed" type="button"
                    data-toggle="collapse" data-target="#navbarColor01"
                    aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span id="m-tgl-icon" class="animated-icon1"><span></span><span></span></span>
            </button>
        </div>
        <div class="collapse navbar-collapse justify-content-end" id="navbarColor01">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id' => 'primary-menu',
                'depth' => 2,
                'container' => false,
                'menu_class' => 'navbar-nav pl-3 pr-3',
                'fallback_cb' => 'WP_Bootstrap_Navwalker::fallback',
                'walker' => new WP_Bootstrap_Navwalker()
            ));

            $h_addr_sw = get_theme_mod('h_address_switcher', false);
            $h_call_sw = get_theme_mod('h_call_switcher', false);

            if ($h_addr_sw || $h_call_sw) {
                ?>
                <div class="header-info">
                    <?php if ($h_addr_sw) {
                        $h_addr_link = get_theme_mod('h_address_link');
                        $h_addr_txt = get_theme_mod('h_address_text');

                        ?>
                        <div class="header-address">
                            <a href="<?php if ('' == $h_addr_link) {
                                echo esc_html__('https://goo.gl/maps/XyANinc4EoxHZguc9', 'healfio');
                            } else {
                                echo esc_html($h_addr_link);
                            } ?>" target="_blank">
                                <?php get_template_part('template-parts/header-address-icon'); ?>
                                <p><?php if ('' == $h_addr_txt) {
                                        echo esc_html__('202 Helga Springs Rd, Crawford, TN 38554', 'healfio');
                                    } else {
                                        echo esc_html($h_addr_txt);
                                    } ?></p>
                            </a>
                        </div>
                    <?php } ?>
                    <?php if ($h_call_sw) {

                        $call_num = get_theme_mod('h_call_number');
                        $call_num_txt = get_theme_mod('h_call_number_txt');
                        $call_txt = get_theme_mod('h_call_txt');
                        ?>
                        <div class="header-phone">
                            <a href="<?php if ('' == $call_num) {
                                echo esc_html__('tel:800.275.8777', 'healfio');
                            } else {
                                echo esc_html($call_num);
                            } ?>">
                                <p class="font-weight-600"><?php if ('' == $call_num_txt) {
                                        echo esc_html__('800.275.8777', 'healfio');
                                    } else {
                                        echo esc_html($call_num_txt);
                                    } ?></p>
                                <p class="h-call-us"><?php if ('' == $call_txt) {
                                        echo esc_html__('Call Us', 'healfio');
                                    } else {
                                        echo esc_html($call_txt);
                                    } ?></p>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { echo '<div class="header-info-empty"></div>';} ?>
            <div class="header-icons">
                <?php
                set_query_var('header_search', true);
                get_search_form();
                set_query_var('header_search', false);
                ?>
                <div class="header-cart-icon"><?php woo_cart_but(); ?></div>
            </div>
            <?php

            $h_cta_btn_switcher = get_theme_mod('h_cta_btn_switcher', false);

            if ($h_cta_btn_switcher) {

                $h_cta_btn_link = get_theme_mod('h_cta_btn_link');
                $h_cta_btn_txt = get_theme_mod('h_cta_btn_txt');

                ?><div class="header-cta"><a href="<?php if ('' == $h_cta_btn_link) {
                    echo esc_html__('/contact-us', 'healfio');
                } else {
                    echo esc_html($h_cta_btn_link);
                } ?>" target="_blank"><div class="d-inline-block elementor-button-link elementor-button elementor-size-md"><?php
                if ('' == $h_cta_btn_txt) {
                    echo esc_html__('Buy Now', 'healfio');
                } else {
                    echo esc_html($h_cta_btn_txt);
                } ?></div></a></div><?php

            }
            ?>
        </div>

    </div>
</nav>
<?php
if (!is_page_template('page-templates/template-full-width-page-without-header-title.php')) { ?>
<header id="main-header">
    <?php get_template_part('template-parts/bg-header'); ?>
    <div class="container inner-header">
        <div class="title-wrap">
            <h1 class="header-title"><?php
                if (healfio_is_product()) {
                    the_title();
                } elseif (healfio_is_shop()) {
                    woocommerce_page_title();
                } elseif (is_singular()) {
                    single_post_title();
                } elseif (is_404()) {
                    esc_html_e('404 NOT FOUND', 'healfio');
                } elseif (is_search()) {
                    esc_html_e('Search', 'healfio');
                } elseif (is_archive() && !have_posts()) {
                    esc_html_e('Nothing Found', 'healfio');
                } elseif (is_archive()) {
                    the_archive_title();
                } elseif (is_tax()) {
                    single_term_title();
                } else {
                    $site_description = get_bloginfo('description', 'display');
                    $site_name = get_bloginfo('name');
                    //for home page
                    if ($site_description && (is_home() || is_front_page())):
                        echo esc_html($site_name);
                        echo '<span class="h-site-description">';
                        echo esc_html($site_description);
                        echo '</span>';
                    endif;
                    // for other post pages
                    if (!(is_home()) && !is_404()):
                        the_title();
                        echo ' | ';
                        echo esc_html($site_name);
                    endif;
                } ?></h1><?php
            if (function_exists('bcn_display')) { ?>
                <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
                    <?php bcn_display(); ?>
                </div>
                <?php
            } ?>
        </div>
    </div>
    <div id="header-wave"></div>
</header>
<?php
}