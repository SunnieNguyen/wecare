<?php
/**
 * Displays the post header
 * @since Healfio 1.0
 */

$entry_header_classes = '';

if (is_singular()) {
    $entry_header_classes .= ' header-group';
}

?>

<header class="entry-header<?php echo esc_attr($entry_header_classes); ?>">

    <div class="entry-header-inner">

        <?php

        // Title
        get_template_part('template-parts/entry-title');

        ?>

        <?php

        /**
         * Allow child themes and plugins to filter the display of the categories in the entry header.
         *
         * @param bool   Whether to show the categories in header, Default true.
         * @since Healfio 1.0
         *
         */
        $show_categories = apply_filters('healfio_show_categories_in_entry_header', true);

        if (true === $show_categories && has_category()) {
            ?>

            <div class="entry-categories">
                <span class="screen-reader-text"><?php esc_html_e('Categories', 'healfio'); ?></span>
                <div class="entry-categories-inner h6">
                    <div class="wrap-entry-categories-inner">
                        <?php the_category(', '); ?>
                    </div>
                </div><!-- .entry-categories-inner -->
            </div><!-- .entry-categories -->

            <?php
        }

        // Default to displaying the post meta.
        healfio_the_post_meta(get_the_ID(), 'single-top');

        $intro_text_width = '';

        if (is_singular()) {
            $intro_text_width = ' regular';
        } else {
            $intro_text_width = ' thin';
        }

        if (has_excerpt() && is_singular() && !healfio_is_product()) {
            ?>

            <div class="intro-text<?php echo esc_attr($intro_text_width); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?>">
                <?php the_excerpt(); ?>
            </div>

            <?php
        }

        if (is_single()) {
            echo ' <div class="blog-tile-wave"></div>';
        }
        ?>
    </div><!-- .entry-header-inner -->
</header><!-- .entry-header -->
