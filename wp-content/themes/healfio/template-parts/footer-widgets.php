<?php
/**
 * Displays widgets at the end of the main element.
 * Visually, this output is presented as part of the footer element.
 * @since Healfio 1.0
 */

$has_social_menu = has_nav_menu('social');

$has_sidebar_1 = is_active_sidebar('sidebar-1');
$has_sidebar_2 = is_active_sidebar('sidebar-2');
$has_sidebar_3 = is_active_sidebar('sidebar-3');

// Only output the container if there are elements to display.
if ($has_sidebar_1 || $has_sidebar_2 || $has_sidebar_3) {
    ?>

    <div class="footer-top">
        <div class="row">

            <div class="col-sm-6 col-lg-4 pb-3">
                <?php if ($has_sidebar_1) { ?>
                    <?php dynamic_sidebar('sidebar-1'); ?>
                <?php } ?>
                <?php if ($has_social_menu) { ?>
                    <nav aria-label="<?php esc_attr_e('Social Menu', 'healfio'); ?>" class="footer-social-wrapper">
                        <h4 class="widget-title"><?php echo wp_get_nav_menu_name('social'); ?></h4>
                        <ul class="social-menu footer-social reset-list-style social-icons fill-children-current-color">

                            <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'social',
                                    'container' => '',
                                    'container_class' => '',
                                    'items_wrap' => '%3$s',
                                    'menu_id' => '',
                                    'menu_class' => '',
                                    'depth' => 1,
                                    'link_before' => '<span class="screen-reader-text">',
                                    'link_after' => '</span>',
                                    'fallback_cb' => '',
                                )
                            );
                            ?>
                        </ul><!-- .footer-social -->
                    </nav><!-- .footer-social-wrapper -->
                <?php } ?>
            </div>

            <div class="col-sm-6 col-lg-4 pb-3">
                <?php if ($has_sidebar_2) { ?>
                    <?php dynamic_sidebar('sidebar-2'); ?>
                <?php } ?>
            </div>

            <div class="col-lg-4 pb-3">
                <?php if ($has_sidebar_3) { ?>
                    <?php dynamic_sidebar('sidebar-3'); ?>
                <?php } ?>
            </div>

        </div>
    </div>

<?php } ?>

