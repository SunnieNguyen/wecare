<?php

/**
 * Post Meta
 */

/**
 * Retrieves and displays the post meta.
 *
 * If it's a single post, outputs the post meta values specified in the Customizer settings.
 *
 * @param int $post_id The ID of the post for which the post meta should be output.
 * @param string $location Which post meta location to output – single or preview.
 */
function healfio_the_post_meta($post_id = null, $location = 'single-top')
{
    echo healfio_get_post_meta($post_id, $location); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in healfio_get_post_meta().
}

/**
 * Retrieves the post meta.
 *
 * @param int $post_id The ID of the post.
 * @param string $location The location where the meta is shown.
 * @return string|void
 */
function healfio_get_post_meta($post_id = null, $location = 'single-top')
{

    // Require post ID.
    if (!$post_id) {
        return;
    }

    /**
     * Filters post types array.
     *
     * This filter can be used to hide post meta information of post, page or custom post type
     * registered by child themes or plugins.
     *
     * @param array Array of post types
     * @since Healfio 1.0
     *
     */
    $disallowed_post_types = apply_filters('healfio_disallowed_post_types_for_meta_output', array('page'));

    // Check whether the post type is allowed to output post meta.
    if (in_array(get_post_type($post_id), $disallowed_post_types, true)) {
        return;
    }

    $post_meta_wrapper_classes = '';
    $post_meta_classes = '';

    // Get the post meta settings for the location specified.
    if ('single-top' === $location) {
        /**
         * Filters post meta info visibility.
         *
         * Use this filter to hide post meta information like Author, Post date, Comments, Is sticky status.
         *
         * @param array $args {
         * @type string 'author'
         * @type string 'post-date'
         * @type string 'comments'
         * @type string 'sticky'
         * }
         * @since Healfio 1.0
         *
         */
        $post_meta = apply_filters(
            'healfio_post_meta_location_single_top',
            array(
                'author',
                'post-date',
                'comments',
                'sticky',
            )
        );

        $post_meta_wrapper_classes = ' post-meta-single post-meta-single-top';

    } elseif ('single-bottom' === $location) {

        /**
         * Filters post tags visibility.
         *
         * Use this filter to hide post tags.
         *
         * @param array $args {
         * @type string 'tags'
         * }
         * @since Healfio 1.0
         *
         */
        $post_meta = apply_filters(
            'healfio_post_meta_location_single_bottom',
            array(
                'tags',
            )
        );

        $post_meta_wrapper_classes = ' post-meta-single post-meta-single-bottom';

    }

    // If the post meta setting has the value 'empty', it's explicitly empty and the default post meta shouldn't be output.
    if ($post_meta && !in_array('empty', $post_meta, true)) {

        // Make sure we don't output an empty container.
        $has_meta = false;

        global $post;
        $the_post = get_post($post_id);
        setup_postdata($the_post);

        ob_start();

        ?>

        <div class="post-meta-wrapper<?php echo esc_attr($post_meta_wrapper_classes); ?>">

            <?php

            if ('single-bottom' === $location) {
                echo('<div class="blog-tile-wave"></div>');
            }

            ?>

            <ul class="post-meta<?php echo esc_attr($post_meta_classes); ?>">

                <?php

                /**
                 * Fires before post meta HTML display.
                 *
                 * Allow output of additional post meta info to be added by child themes and plugins.
                 *
                 * @param int $post_id Post ID.
                 * @param array $post_meta An array of post meta information.
                 * @param string $location The location where the meta is shown.
                 *                          Accepts 'single-top' or 'single-bottom'.
                 * @since Healfio 1.0
                 *
                 */
                do_action('healfio_start_of_post_meta_list', $post_id, $post_meta, $location);

                // Author.
                if (post_type_supports(get_post_type($post_id), 'author') && in_array('author', $post_meta, true)) {

                    $has_meta = true;
                    ?>
                    <li class="post-author meta-wrapper">
						<span class="meta-icon">
							<span class="screen-reader-text"><?php esc_html_e('Post author', 'healfio'); ?></span>
							<?php healfio_the_theme_svg('user'); ?>
						</span>
                        <span class="meta-text">
							<?php
                            printf(
                            /* translators: %s: Author name. */
                                esc_html__('%s', 'healfio'),
                                '<a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">'. esc_html__('By ','healfio') . esc_html(get_the_author_meta('display_name')) . '</a>'
                            );
                            ?>
						</span>
                    </li>
                    <?php

                }

                // Post date.
                if (in_array('post-date', $post_meta, true)) {

                    $has_meta = true;
                    ?>
                    <li class="post-date meta-wrapper">
						<span class="meta-icon">
							<span class="screen-reader-text"><?php esc_html__('Post date', 'healfio'); ?></span>
							<?php healfio_the_theme_svg('calendar'); ?>
						</span>
                        <span class="meta-text">
							<?php the_time(get_option('date_format')); ?>
						</span>
                    </li>
                    <?php

                }

                // Categories.
                if (in_array('categories', $post_meta, true) && has_category()) {

                    $has_meta = true;
                    ?>
                    <li class="post-categories meta-wrapper">
						<span class="meta-icon">
							<span class="screen-reader-text"><?php esc_html__('Categories', 'healfio'); ?></span>
							<?php healfio_the_theme_svg('folder'); ?>
						</span>
                        <span class="meta-text">
							<?php echo esc_html_x('In', 'A string that is output before one or more categories', 'healfio'); ?><?php the_category(', '); ?>
						</span>
                    </li>
                    <?php

                }

                // Tags.
                if (in_array('tags', $post_meta, true) && has_tag()) {

                    $has_meta = true;
                    ?>
                    <li class="post-tags meta-wrapper">
						<span class="meta-icon">
							<span class="screen-reader-text"><?php esc_html_e('Tags', 'healfio'); ?></span>
							<?php healfio_the_theme_svg('tag'); ?>
						</span>
                        <span class="meta-text">
							<?php the_tags('', ', ', ''); ?>
						</span>
                    </li>
                    <?php

                }

                // Comments link.
                if (in_array('comments', $post_meta, true) && !post_password_required() && (comments_open() || get_comments_number())) {

                    $has_meta = true;
                    ?>
                    <li class="post-comment-link meta-wrapper">
						<span class="meta-icon">
							<?php healfio_the_theme_svg('comment'); ?>
						</span>
                        <span class="meta-text">
							<?php comments_popup_link(); ?>
						</span>
                    </li>
                    <?php

                }

                // Sticky.
                if (in_array('sticky', $post_meta, true) && is_sticky()) {

                    $has_meta = true;
                    ?>
                    <li class="post-sticky meta-wrapper">
						<span class="meta-icon">
							<?php healfio_the_theme_svg('bookmark'); ?>
						</span>
                        <span class="meta-text">
							<?php esc_html_e('Sticky post', 'healfio'); ?>
						</span>
                    </li>
                    <?php

                }

                /**
                 * Fires after post meta HTML display.
                 *
                 * Allow output of additional post meta info to be added by child themes and plugins.
                 *
                 * @param int $post_id Post ID.
                 * @param array $post_meta An array of post meta information.
                 * @param string $location The location where the meta is shown.
                 *                          Accepts 'single-top' or 'single-bottom'.
                 * @since Healfio 1.0
                 *
                 */
                do_action('healfio_end_of_post_meta_list', $post_id, $post_meta, $location);

                ?>

            </ul><!-- .post-meta -->

        </div><!-- .post-meta-wrapper -->

        <?php

        wp_reset_postdata();

        $meta_output = ob_get_clean();

        // If there is meta to output, return it.
        if ($has_meta && $meta_output) {

            return $meta_output;

        }
    }

}


/**
 * Displays SVG icons in social links menu.
 *
 * @param string $item_output The menu item's starting HTML output.
 * @param WP_Post $item Menu item data object.
 * @param int $depth Depth of the menu. Used for padding.
 * @param stdClass $args An object of wp_nav_menu() arguments.
 * @return string The menu item output with social icon.
 */
function healfio_nav_menu_social_icons($item_output, $item, $depth, $args)
{
    // Change SVG icon inside social links menu if there is supported URL.
    if ('social' === $args->theme_location) {
        $svg = healfio_SVG_Icons::get_social_link_svg($item->url);
        if (empty($svg)) {
            $svg = healfio_get_theme_svg('link');
        }
        $item_output = str_replace($args->link_after, '</span>' . $svg, $item_output);
    }

    return $item_output;
}

add_filter('walker_nav_menu_start_el', 'healfio_nav_menu_social_icons', 10, 4);


/**
 * Comments
 */

/**
 * Checks if the specified comment is written by the author of the post commented on.
 *
 * @param object $comment Comment data.
 * @return bool
 * @noinspection DuplicatedCode
 */
function healfio_is_comment_by_post_author($comment = null)
{

    if (is_object($comment) && $comment->user_id > 0) {

        $user = get_userdata($comment->user_id);
        $post = get_post($comment->comment_post_ID);

        if (!empty($user) && !empty($post)) {

            return $comment->user_id === $post->post_author;

        }
    }
    return false;

}

///**
// * Filters comment reply link to not JS scroll.
// *
// * Filter the comment reply link to add a class indicating it should not use JS slow-scroll, as it
// * makes it scroll to the wrong position on the page.
// *
// * @param string $link Link to the top of the page.
// * @return string Link to the top of the page.
// */
//function healfio_filter_comment_reply_link( $link ) {
//
//    $link = str_replace( 'class=\'', 'class=\'do-not-scroll ', $link );
//    return $link;
//
//}
//
//add_filter( 'comment_reply_link', 'healfio_filter_comment_reply_link' );