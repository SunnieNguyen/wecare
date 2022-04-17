<?php
/**
 * The default template for displaying content.
 * @since Healfio 1.0
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <div class="post-inner">

        <?php
        get_template_part('template-parts/entry-header');
        ?>

        <div class="entry-content clearfix">
            <?php
            if (is_search() || !is_singular() || is_archive() && 'summary' === get_theme_mod('blog_content', 'full')) {
                the_excerpt();
            } else {
                the_content(esc_html__('Continue reading', 'healfio'));
            }
            ?>
        </div><!-- .entry-content -->

        <?php
        // Single bottom post meta.
        healfio_the_post_meta(get_the_ID(), 'single-bottom');
        ?>

    </div><!-- .post-inner -->

    <div class="section-inner clearfix"><?php
        wp_link_pages(
            array(
                'before' => '<div class="splitting-nav"><nav class="post-nav-links bg-light-background" aria-label="' . esc_attr__('Page', 'healfio') . '"><span class="label">' . esc_html__('Pages:', 'healfio') . '</span>',
                'after' => '</nav></div>',
                'link_before' => '<span class="page-number">',
                'link_after' => '</span>',
            )
        );

        if (post_type_supports(get_post_type(get_the_ID()), 'author') && is_single()) {
            get_template_part('template-parts/entry-author-bio');
        }
        ?></div><!-- .section-inner -->

    <?php

    if (is_single()) {

        get_template_part('template-parts/navigation');

    }

    /**
     * Output comments wrapper if it's a post, or if comments are open,
     * or if there's a comment number – and check for password.
     * */
    if ((is_single() || is_page()) && (comments_open() || get_comments_number()) && !post_password_required()) {
        ?>

        <div class="comments-wrapper section-inner">

            <?php comments_template(); ?>

        </div><!-- .comments-wrapper -->

        <?php
    }
    ?>

</article><!-- .post -->
