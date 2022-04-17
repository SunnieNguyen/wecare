<?php
/**
 * Customizer settings for this theme.
 * @since Healfio 1.0
 */
if (!class_exists('healfio_Customize')) {
    /**
     * CUSTOMIZER SETTINGS
     */
    class healfio_Customize
    {

        /**
         * Register customizer options.
         *
         * @param WP_Customize_Manager $wp_customize Theme Customizer object.
         */
        public static function register($wp_customize)
        {
            /* ========================================================================= */
            /*
             * COLORS
             */

            // Primary color
            $wp_customize->add_setting('pr_color', array(
                'default' => '#01785c',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'pr_color', array(
                'section' => 'colors',
                'label' => esc_html__('Primary color', 'healfio'),
                'description' => esc_html__('Sets main accent color.', 'healfio'),
            )));


            // Primary hover color
            $wp_customize->add_setting('pr_h_color', array(
                'default' => '#20292f',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'pr_h_color', array(
                'section' => 'colors',
                'label' => esc_html__('Primary hover color', 'healfio'),
                'description' => esc_html__('Sets link hover color.', 'healfio'),
            )));


            // Primary background color
            $wp_customize->add_setting('pr_bg_color', array(
                'default' => '#ebf4f2',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'pr_bg_color', array(
                'section' => 'colors',
                'label' => esc_html__('Primary background color', 'healfio'),
                'description' => esc_html__("Changes accent background color.", 'healfio'),
            )));


            // Header background color
            $wp_customize->add_setting('h_bg_color', array(
                'default' => '#f8f9fa',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'h_bg_color', array(
                'section' => 'colors',
                'label' => esc_html__('Header background color', 'healfio'),
                'description' => esc_html__("Changes header background color. If there is no changing of the header color that means the current page uses Elementor builder’s header instead of the site's global, so you need to change the color on the page.", 'healfio'),
            )));


            // Footer background color
            $wp_customize->add_setting('f_bg_color', array(
                'default' => '#f8f9fa',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'f_bg_color', array(
                'section' => 'colors',
                'label' => esc_html__('Footer background color', 'healfio'),
                'description' => esc_html__("Changes footer background color.", 'healfio'),
            )));


            // Primary dark color
            $wp_customize->add_setting('pr_d_color', array(
                'default' => '#20292f',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'pr_d_color', array(
                'section' => 'colors',
                'label' => esc_html__('Primary dark color', 'healfio'),
                'description' => esc_html__('Sets text color in paragraphs.', 'healfio'),
            )));


            // h1 title color
            $wp_customize->add_setting('title_color', array(
                'default' => '#20292f',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'title_color', array(
                'section' => 'colors',
                'label' => esc_html__('Title color', 'healfio'),
                'description' => esc_html__('Sets color for titles.', 'healfio'),
            )));


            // Footer widget title color
            $wp_customize->add_setting('fw_title_color', array(
                'default' => '#0a2540',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'fw_title_color', array(
                'section' => 'colors',
                'label' => esc_html__('Footer widget title color', 'healfio'),
            )));


            // Header links hover color
            $wp_customize->add_setting('header_h_color', array(
                'default' => '#ebf4f2',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_h_color', array(
                'section' => 'colors',
                'label' => esc_html__('Header links hover color', 'healfio'),
            )));


            // Button background color
            $wp_customize->add_setting('btn_bg_color', array(
                'default' => '#01785c',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'btn_bg_color', array(
                'section' => 'colors',
                'label' => esc_html__('Button background color', 'healfio'),
            )));


            // Button hover color
            $wp_customize->add_setting('btn_h_color', array(
                'default' => '#02664e',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'btn_h_color', array(
                'section' => 'colors',
                'label' => esc_html__('Button hover color', 'healfio'),
            )));


            // Social icon text color
            $wp_customize->add_setting('social_icon_txt_color', array(
                'default' => '#ffffff',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'social_icon_txt_color', array(
                'section' => 'colors',
                'label' => esc_html__('Social icon color', 'healfio'),
            )));


            // Social icon background color
            $wp_customize->add_setting('social_icon_color', array(
                'default' => '#01785c',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'social_icon_color', array(
                'section' => 'colors',
                'label' => esc_html__('Social icon circle background color', 'healfio'),
            )));


            // Social icon background hover color
            $wp_customize->add_setting('social_icon_h_color', array(
                'default' => '#02664e',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'social_icon_h_color', array(
                'section' => 'colors',
                'label' => esc_html__('Social icon circle background hover color', 'healfio'),
            )));


            // Woocommerce price filter widget color
            $wp_customize->add_setting('woo_pr_fil_bg_color', array(
                'default' => '#01785c',
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'woo_pr_fil_bg_color', array(
                'section' => 'colors',
                'label' => esc_html__('Woocommerce price filter widget color', 'healfio'),
            )));


            // Text selection background color
            $wp_customize->add_setting('txt_select_bg_color', array(
                'default' => '#cce7ea',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'txt_select_bg_color', array(
                'section' => 'colors',
                'label' => esc_html__('Text selection background color', 'healfio'),
                'description' => esc_html__('Changes text selection background color. Try to select text on a page.', 'healfio'),
            )));


            // Price color
            $wp_customize->add_setting('price_color', array(
                'default' => '#20292f',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'price_color', array(
                'section' => 'colors',
                'label' => esc_html__('Price color', 'healfio'),
                'description' => esc_html__('Sets color for price text on product tiles.', 'healfio'),
            )));


            // Color for title on product tile
            $wp_customize->add_setting('price_tile_color', array(
                'default' => '#20292f',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'price_tile_color', array(
                'section' => 'colors',
                'label' => esc_html__('Product title color', 'healfio'),
                'description' => esc_html__('Sets color for title on product tile.', 'healfio'),
            )));


            // Color of sale badge
            $wp_customize->add_setting('sale_badge_bg_color', array(
                'default' => '#01785c',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sale_badge_bg_color', array(
                'section' => 'colors',
                'label' => esc_html__('Color of sale badge', 'healfio'),
                'description' => esc_html__('Sets color of sale badge on product tile.', 'healfio'),
            )));


            // Cart count color
            $wp_customize->add_setting('cart_count_color', array(
                'default' => '#01785c',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'cart_count_color', array(
                'section' => 'colors',
                'label' => esc_html__('Cart count color', 'healfio'),
                'description' => esc_html__('Sets background color for count badge on cart icon on the header. Add product to cart to see the badge.', 'healfio'),
            )));


            // Success icon color
            $wp_customize->add_setting('success_icon_color', array(
                'default' => '#01785c',
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));

            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'success_icon_color', array(
                'section' => 'colors',
                'label' => esc_html__('Success icon color', 'healfio'),
                'description' => esc_html__('Sets icon color on success notice ✔', 'healfio'),
            )));


            /* end COLORS */
            /* ========================================================================= */
            /*
             * HEADER
             */

            $wp_customize->add_section('header', array(
                'title' => esc_html__('Header', 'healfio')
            ));


            // Switcher for enable address on the header
            $wp_customize->add_setting('h_address_switcher', array(
                'default' => false,
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_address_switcher', array(
                'section' => 'header',
                'label' => esc_html__('Enable address on the header?', 'healfio'),
                'type' => 'checkbox'
            ));


            // Address text
            $wp_customize->add_setting('h_address_text', array(
                'default' => esc_html__('202 Helga Springs Rd, Crawford, TN 38554', 'healfio'),
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_address_text', array(
                'section' => 'header',
                'label' => esc_html__('Address text.', 'healfio'),
                'description' => esc_html__('Field for business address.', 'healfio'),
                'type' => 'text'
            ));


            // Address link
            $wp_customize->add_setting('h_address_link', array(
                'default' => esc_html__('https://goo.gl/maps/XyANinc4EoxHZguc9', 'healfio'),
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_address_link', array(
                'section' => 'header',
                'label' => esc_html__('Address link.', 'healfio'),
                'description' => wp_kses((__('You can use any map service here, for example <a href="https://www.google.com/maps" target="_blank">Google Maps</a>, just find your place in Google Maps, copy link and put it to this field to set.', 'healfio')), 'link'),
                'type' => 'text'
            ));


            // Switcher for enable Call Us on the header
            $wp_customize->add_setting('h_call_switcher', array(
                'default' => false,
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_call_switcher', array(
                'section' => 'header',
                'label' => esc_html__('Enable Call Us on the header?', 'healfio'),
                'type' => 'checkbox'
            ));


            // Call phone number text
            $wp_customize->add_setting('h_call_number_txt', array(
                'default' => esc_html__('800.275.8777', 'healfio'),
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_call_number_txt', array(
                'section' => 'header',
                'label' => esc_html__('Phone number text.', 'healfio'),
                'description' => esc_html__('You can use any text here. For example: 123-AWESOME-BUSINESS, or +1 234 56 78, or 12345678, or 123.456.78', 'healfio'),
                'type' => 'text'
            ));


            // Call phone number
            $wp_customize->add_setting('h_call_number', array(
                'default' => esc_html__('tel:800.275.8777', 'healfio'),
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_call_number', array(
                'section' => 'header',
                'label' => esc_html__('Phone number link.', 'healfio'),
                'description' => esc_html__('You can use any phone number format here. For the regular phone add "tel:" before, for example - tel:123-AWESOME-BUSINESS, or tel:+1 234 56 78, or tel:12345678, or tel:123.456.78', 'healfio'),
                'type' => 'text'
            ));


            // Call text
            $wp_customize->add_setting('h_call_txt', array(
                'default' => esc_html__('Call Us', 'healfio'),
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_call_txt', array(
                'section' => 'header',
                'label' => esc_html__('Call Us text.', 'healfio'),
                'description' => esc_html__('Use any or default Call Us text (it also uses Call Us text if this field is blank).', 'healfio'),
                'type' => 'text'
            ));


            // Enable CTA button in the header
            $wp_customize->add_setting('h_cta_btn_switcher', array(
                'default' => false,
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_cta_btn_switcher', array(
                'section' => 'header',
                'label' => esc_html__('Enable CTA button (call-to-action).', 'healfio'),
                'type' => 'checkbox'
            ));


            // Header CTA button link
            $wp_customize->add_setting('h_cta_btn_link', array(
                'default' => esc_html__('/contact-us', 'healfio'),
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_cta_btn_link', array(
                'section' => 'header',
                'label' => esc_html__('CTA button link:', 'healfio'),
                'type' => 'text'
            ));


            // Header CTA button text
            $wp_customize->add_setting('h_cta_btn_txt', array(
                'default' => esc_html__('Buy Now', 'healfio'),
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('h_cta_btn_txt', array(
                'section' => 'header',
                'label' => esc_html__('CTA button text:', 'healfio'),
                'type' => 'text'
            ));


            /* end HEADER */
            /* ========================================================================= */
            /*
             * FOOTER
             */

            $wp_customize->add_section('footer', array(
                'title' => esc_html__('Footer', 'healfio')
            ));

            // Switcher for Copyright text
            $wp_customize->add_setting('copyright_text_switcher', array(
                'default' => false,
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('copyright_text_switcher', array(
                'section' => 'footer',
                'label' => esc_html__('Disable "Copyright" text before the year', 'healfio'),
                'type' => 'checkbox'
            ));

            // Custom copyright
            $wp_customize->add_setting('copyright_text', array(
                'default' => '',
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('copyright_text', array(
                'section' => 'footer',
                'label' => esc_html__('Custom copyright text.', 'healfio'),
                'description' => esc_html__('Leave blank to use default copyright.', 'healfio'),
                'type' => 'text'
            ));

            /* end FOOTER */
            /* ========================================================================= */
            /*
             * TWEAKS
             */

            $wp_customize->add_section('tweaks', array(
                'title' => esc_html__('Tweaks', 'healfio')
            ));


            // Show/hide
            $wp_customize->add_setting('add_to_cart_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('add_to_cart_switcher', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable "Add to cart" button on product tiles', 'healfio'),
                'type' => 'checkbox'
            ));


            // Disable post category meta text
            $wp_customize->add_setting('meta_cat_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('meta_cat_switcher', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable post category meta text', 'healfio'),
                'type' => 'checkbox'
            ));


            // Disable post author meta text
            $wp_customize->add_setting('meta_author_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('meta_author_switcher', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable post author meta text', 'healfio'),
                'type' => 'checkbox'
            ));


            // Disable post date meta text
            $wp_customize->add_setting('meta_date_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('meta_date_switcher', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable post date meta text', 'healfio'),
                'type' => 'checkbox'
            ));

            // Disable post comments meta text
            $wp_customize->add_setting('meta_comm_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('meta_comm_switcher', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable post comments meta text', 'healfio'),
                'type' => 'checkbox'
            ));


            // Disable product category meta text
            $wp_customize->add_setting('meta_pr_cat_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));


            // Enable search an entire website, not just products
            $wp_customize->add_setting('search_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('search_switcher', array(
                'section' => 'tweaks',
                'label' => esc_html__('Enable search an entire website, not just products', 'healfio'),
                'type' => 'checkbox'
            ));


            // Disable icon before title
            $wp_customize->add_setting('icon_before_title_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('icon_before_title_switcher', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable icon before title', 'healfio'),
                'type' => 'checkbox'
            ));


            // Disable header background
            $wp_customize->add_setting('header_background_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('header_background_switcher', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable header background', 'healfio'),
                'type' => 'checkbox'
            ));


            // Disable footer background
            $wp_customize->add_setting('footer_background_switcher', array(
                'default' => false,
                'transport' => 'postMessage',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('footer_background_switcher', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable footer background', 'healfio'),
                'type' => 'checkbox'
            ));


            // Disable sticky header on desktop
            $wp_customize->add_setting('disable_sticky_header_desktop', array(
                'default' => false,
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('disable_sticky_header_desktop', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable sticky header on desktop', 'healfio'),
                'type' => 'checkbox'
            ));


            // Disable header search and cart icons
            $wp_customize->add_setting('header_search_and_cart_icons', array(
                'default' => false,
                'transport' => 'refresh',
                'sanitize_callback' => 'sanitize_text_field'
            ));

            $wp_customize->add_control('header_search_and_cart_icons', array(
                'section' => 'tweaks',
                'label' => esc_html__('Disable header search and cart icons', 'healfio'),
                'type' => 'checkbox'
            ));


            /* end TWEAKS */
            /* ========================================================================= */


            /* -----------------------------*/
            /* end Customize Settings */
            /* -----------------------------*/
        }
    }


    // Setup the Theme Customizer settings and controls.
    add_action('customize_register', array('healfio_Customize', 'register'));

}