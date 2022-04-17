<?php
/**
 * Displays the post header
 * @since Healfio 1.2
 */
?>

<header class="entry-header">

    <a href="<?php echo esc_url(get_the_permalink()) ?>" class="entry-title-link"><?php
        the_title('<h5 class="entry-title">', '</h5>');
        echo '</a>';

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
                <div class="entry-categories-inner">
                    <?php the_category(', '); ?>
                </div>
            </div>

            <?php
        }
        ?>
        <div class="entry-excerpt"><?php the_excerpt(); ?></div>
        <div class="blog-tile-wave"></div><?php
        // Default to displaying the post meta.
        healfio_the_post_meta(get_the_ID(), 'single-top');
        ?>

</header>
