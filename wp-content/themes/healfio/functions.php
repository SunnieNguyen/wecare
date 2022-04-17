<?php
/**
 * Healfio functions and definitions.
 */

/**
 * Table of Contents:
 * Theme Support
 * Required Files
 * Register Styles
 * Register Scripts
 * Register Menus
 * Register Sidebars
 * WP Body Open
 */


/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function healfio_theme_support()
{
    /*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
    add_theme_support('title-tag');

    // Register logo.
    $defaults = array(
        'flex-height' => true,
        'flex-width' => true,
        'header-text' => false,
        'unlink-homepage-logo' => false,
    );
    add_theme_support('custom-logo', $defaults);

    /*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
    add_theme_support(
        'html5',
        array(
            'comment-form',
            'comment-list',
        )
    );

    // Woocommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // RSS
    add_theme_support('automatic-feed-links');

    // Post thumbnails
    add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'healfio_theme_support');


/**
 * Add SVG support for the logo.
 * @param $html
 * @return string|string[]
 */

function healfio_change_logo_class($html)
{

    $html = str_replace('class="custom-logo"', 'class="custom-logo style-svg"', $html);

    return $html;
}

add_filter('get_custom_logo', 'healfio_change_logo_class');


/**
 * Required Files. Include required files.
 */
require get_template_directory() . '/inc/template-tags.php';

// Handle Customizer settings.
require get_template_directory() . '/classes/class-theme-customize.php';
require get_template_directory() . '/inc/get-customizer-css.php';
require get_template_directory() . '/inc/get-viewport-script.php';

// Handle SVG icons.
require get_template_directory() . '/classes/class-theme-svg-icons.php';
require get_template_directory() . '/inc/svg-icons.php';

// Custom comment walker.
require get_template_directory() . '/classes/class-theme-walker-comment.php';

// Cart icon.
require get_template_directory() . '/inc/cart-icon.php';

// Elementor - Recent Posts.
require get_template_directory() . '/classes/class-elementor-widgets.php';

// Kses allowed HTML tags.
require get_template_directory() . '/inc/kses-allowed-html.php';


/**
 * Register and Enqueue Styles.
 */
function healfio_register_styles()
{
    $theme_version = wp_get_theme()->get('Version');
    wp_enqueue_style('inter', get_template_directory_uri() . '/assets/fonts/inter.css', array(), $theme_version);
    wp_enqueue_style('bootstrap', get_template_directory_uri() . '/assets/css/bootstrap.css', array(), $theme_version);
    wp_enqueue_style('healfio', get_stylesheet_uri(), array(), $theme_version);
    wp_add_inline_style('healfio', healfio_get_customizer_css());
}

add_action('wp_enqueue_scripts', 'healfio_register_styles');


/**
 * Register and Enqueue Scripts.
 */
function healfio_register_scripts()
{
    $theme_version = wp_get_theme()->get('Version');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/bootstrap.bundle.min.js', array('jquery'), $theme_version);
    wp_enqueue_script('healfio', get_template_directory_uri() . '/assets/js/index.js', array('jquery'), $theme_version);
    wp_script_add_data('healfio', 'async', true);
    wp_add_inline_script('healfio', healfio_get_viewport_script(), 'before'); // Uses inline method to escape layout shift
}

add_action('wp_enqueue_scripts', 'healfio_register_scripts');


/**
 * Register navigation menus.
 */
function healfio_menus()
{
    $locations = array(
        'primary' => esc_html__('Header Menu', 'healfio'),
        'footer' => esc_html__('Footer Menu', 'healfio'),
        'social' => esc_html__('Social Menu', 'healfio'),
    );
    register_nav_menus($locations);
}

add_action('init', 'healfio_menus');


/**
 * Register widget areas.
 */
function healfio_sidebar_registration()
{

    // Arguments used in all register_sidebar() calls.
    $shared_args = array(
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
        'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
        'after_widget' => '</div></div>',
    );

    // Footer #1.
    register_sidebar(
        array_merge(
            $shared_args,
            array(
                'name' => esc_html__('Footer #1', 'healfio'),
                'id' => 'sidebar-1',
                'description' => esc_html__('Widgets in this area will be displayed in the first column in the footer.', 'healfio'),
            )
        )
    );

    // Footer #2.
    register_sidebar(
        array_merge(
            $shared_args,
            array(
                'name' => esc_html__('Footer #2', 'healfio'),
                'id' => 'sidebar-2',
                'description' => esc_html__('Widgets in this area will be displayed in the second column in the footer.', 'healfio'),
            )
        )
    );

    // Footer #3.
    register_sidebar(
        array_merge(
            $shared_args,
            array(
                'name' => esc_html__('Footer #3', 'healfio'),
                'id' => 'sidebar-3',
                'description' => esc_html__('Widgets in this area will be displayed in the third column in the footer.', 'healfio'),
            )
        )
    );

    // Blog sidebar
    register_sidebar(
        array_merge(
            $shared_args,
            array(
                'name' => esc_html__('Blog sidebar', 'healfio'),
                'id' => 'blog-sidebar',
                'description' => esc_html__('Widgets in this area will be displayed in the second column on blog pages.', 'healfio'),
            )
        )
    );

    // Single product sidebar
    register_sidebar(
        array_merge(
            $shared_args,
            array(
                'name' => esc_html__('Single product sidebar', 'healfio'),
                'id' => 'sidebar-single-product',
                'description' => esc_html__('Widgets in this area will be displayed in the second column on single product pages.', 'healfio'),
            )
        )
    );

    // Shop sidebar
    register_sidebar(
        array_merge(
            $shared_args,
            array(
                'name' => esc_html__('Shop sidebar', 'healfio'),
                'id' => 'sidebar-shop',
                'description' => esc_html__('Widgets in this area will be displayed in the second column on the shop page.', 'healfio'),
            )
        )
    );

}

add_action('widgets_init', 'healfio_sidebar_registration');


/**
 * Shim for wp_body_open, ensuring backward compatibility with versions of WordPress older than 5.2.
 */
if (!function_exists('wp_body_open')) {
    function wp_body_open()
    {
        do_action('wp_body_open');
    }
}


/**
 * Allow to add class to menu's link.
 */
function healfio_add_menu_link_class($atts, $item, $args)
{
    if (property_exists($args, 'link_class')) {
        $atts['class'] = $args->link_class;
    }
    return $atts;
}

add_filter('nav_menu_link_attributes', 'healfio_add_menu_link_class', 1, 3);


/**
 * Allow to add class to menu's <li> item.
 */
function healfio_add_additional_class_on_li($classes, $item, $args)
{
    if (isset($args->add_li_class)) {
        $classes[] = $args->add_li_class;
    }
    return $classes;
}

add_filter('nav_menu_css_class', 'healfio_add_additional_class_on_li', 1, 3);


/**
 * Register Custom Navigation Walker.
 */
function healfio_register_navwalker()
{
    require_once get_template_directory() . '/classes/class-wp-bootstrap-navwalker.php';
}

add_action('after_setup_theme', 'healfio_register_navwalker');


/**
 * Change widget titles to h4.
 * @param $params
 * @return mixed
 */
function healfio_filter_widget_title_tag($params)
{
    $params[0]['before_title'] = '<h4 class="widget-title">';
    $params[0]['after_title'] = '</h4>';
    return $params;
}

add_filter('dynamic_sidebar_params', 'healfio_filter_widget_title_tag');


/**
 * Hide Add to cart button from non-product pages.
 */
if (get_theme_mod('add_to_cart_switcher')) {
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
}


/**
 * Enqueue comment-reply script.
 */
function healfio_enqueue_comments_reply()
{
    if (get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

add_action('comment_form_before', 'healfio_enqueue_comments_reply');


/**
 * Check Woocommerce is active for cart functions.
 * @return bool
 */
function healfio_is_product(): bool
{
    if (class_exists('woocommerce')) {
        return is_product();
    } else {
        return false;
    }
}


/**
 * Check Woocommerce is active for cart functions.
 * @return bool
 */
function healfio_is_shop(): bool
{
    if (class_exists('woocommerce')) {
        return is_shop();
    } else {
        return false;
    }
}


/**
 * Remove breadcrumbs from pages.
 */
add_filter('woocommerce_before_main_content', 'healfio_remove_breadcrumbs');
function healfio_remove_breadcrumbs()
{
    if (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy()) {
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    }
}


/**
 * Use comment's author display name for comments.
 */
add_filter('get_comment_author', 'healfio_comment_author_display_name');
function healfio_comment_author_display_name($author): string
{
    global $comment;
    if (!empty($comment->user_id)) {
        $user = get_userdata($comment->user_id);
        $author = $user->display_name;
    }
    return $author;
}


/**
 * Set up the content width value based on the theme's design.
 *
 * @see _action_theme_content_width()
 */
if (!isset($content_width)) {
    $content_width = 1199;
}


/**
 * Change number of products that are displayed per page (shop page).
 *
 * @see https://docs.woocommerce.com/document/change-number-of-products-displayed-per-page
 */
add_filter( 'loop_shop_per_page', 'healfio_loop_shop_per_page', 20 );
function healfio_loop_shop_per_page( $cols ): int
{
    // $cols contains the current number of products per page based on the value stored on Options –> Reading
    // Return the number of products you wanna show per page.
    $cols = 12;
    return $cols;
}


/**
 * Change number of products per row to 2.
 */
add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
    function loop_columns(): int
    {
        return 3;
    }
}


/**
 * Change number of related products output.
 */
add_filter('woocommerce_output_related_products_args', 'healfio_related_products_args', 20);
function healfio_related_products_args($args)
{
    $args['posts_per_page'] = 3;
    $args['columns'] = 3;
    return $args;
}


/**
 * Change number of upsell products output.
 */
add_filter('woocommerce_upsell_display_args', 'healfio_upsell_products_args', 20);
function healfio_upsell_products_args($args)
{
    $args['posts_per_page'] = 3;
    $args['columns'] = 3;
    return $args;
}


/**
 * TGM Plugin Activation.
 */
require_once get_template_directory() . '/classes/class-tgm-plugin-activation.php';

/** @internal */
function healfio_action_theme_register_required_plugins()
{

    $config = array(
        'id' => 'healfio',
        'menu' => 'healfio-install-plugins',
        'parent_slug' => 'themes.php',
        'capability' => 'edit_theme_options',
        'has_notices' => true,
        'dismissable' => true,
        'is_automatic' => true,
    );

    tgmpa(array(
        array(
            'name' => esc_html__('Breadcrumb NavXT', 'healfio'),
            'slug' => 'breadcrumb-navxt',
            'required' => false,
        ),
        array(
            'name' => esc_html__('Contact Form 7', 'healfio'),
            'slug' => 'contact-form-7',
            'required' => false,
        ),
        array(
            'name' => esc_html__('NextGEN Gallery', 'healfio'),
            'slug' => 'nextgen-gallery',
            'required' => false,
        ),
        array(
            'name' => esc_html__('MailChimp for WordPress', 'healfio'),
            'slug' => 'mailchimp-for-wp',
            'required' => false,
        ),
        array(
            'name' => esc_html__('Unyson', 'healfio'),
            'slug' => 'unyson',
            'source' => 'https://peerduck.com/wp-content/uploads/themes/Plugins/Unyson/jV8rfn8z/unyson.zip',
            'required' => false,
        ),
        array(
            'name' => esc_html__('WooCommerce', 'healfio'),
            'slug' => 'woocommerce',
            'required' => true,
        ),
        array(
            'name' => esc_html__('Envato Market', 'healfio'),
            'slug' => 'envato-market',
            'source' => get_template_directory() . '/inc/plugins/envato-market.zip',
            'required' => false,
        ),
        array(
            'name' => esc_html__('Recent Posts Widget with Thumbnails', 'healfio'),
            'slug' => 'peerduck-recent-posts-thumbnails',
            'source' => get_template_directory() . '/inc/plugins/peerduck-recent-posts-thumbnails.zip',
            'required' => false,
        ),
        array(
            'name' => esc_html__('Elementor', 'healfio'),
            'slug' => 'elementor',
            'required' => true,
        ),
        array(
            'name' => esc_html__('Classic Editor', 'healfio'),
            'slug' => 'classic-editor',
            'required' => false,
        ),
        array(
            'name' => esc_html__('Unlimited Elements for Elementor', 'healfio'),
            'slug' => 'unlimited-elements-for-elementor',
            'required' => false,
        ),
        array(
            'name' => esc_html__('Essential Addons for Elementor', 'healfio'),
            'slug' => 'essential-addons-for-elementor-lite',
            'required' => false,
        ),
        array(
            'name' => esc_html__('Elements kit Elementor addons', 'healfio'),
            'slug' => 'elementskit-lite',
            'required' => false,
        ),
    ), $config);

}

add_action('tgmpa_register', 'healfio_action_theme_register_required_plugins');


/**
 * @param FW_Ext_Backups_Demo[] $demos
 * @return FW_Ext_Backups_Demo[]
 */
function healfio_filter_theme_fw_ext_backups_demos($demos)
{
    $demos_array = array(
        'healfio-demo' => array(
            'title' => esc_html__('Healfio Demo Content', 'healfio'),
            'screenshot' => 'https://peerduck.com/wp-content/uploads/themes/healfio/helgamegazordwefwpfe/screenshot.png',
            'preview_link' => 'https://healfio.peerduck.com',
        ),
    );

    $download_url = 'http://peerduck.com/wp-content/uploads/themes/healfio/helgamegazordwefwpfe/';

    foreach ($demos_array as $id => $data) {
        $demo = new FW_Ext_Backups_Demo($id, 'piecemeal', array(
            'url' => $download_url,
            'file_id' => $id,
        ));
        $demo->set_title($data['title']);
        $demo->set_screenshot($data['screenshot']);
        $demo->set_preview_link($data['preview_link']);

        $demos[$demo->get_id()] = $demo;

        unset($demo);
    }

    return $demos;
}

add_filter('fw:ext:backups-demo:demos', 'healfio_filter_theme_fw_ext_backups_demos');