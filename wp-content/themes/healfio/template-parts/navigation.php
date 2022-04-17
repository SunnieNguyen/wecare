<?php
/**
 * Displays the next and previous post navigation in single posts.
 */

$next_post = get_next_post();
$prev_post = get_previous_post();

if ($next_post || $prev_post) {

    $pagination_classes = '';

    if (!$next_post) {
        $pagination_classes = ' only-one only-prev';
    } elseif (!$prev_post) {
        $pagination_classes = ' only-one only-next';
    }

    ?>

    <nav class="pagination-single section-inner<?php echo esc_attr($pagination_classes); ?>"
         aria-label="<?php esc_attr_e('Post', 'healfio'); ?>" role="navigation">

        <div class="pagination-single-inner">

            <?php
            if ($prev_post) {
                ?>
                <a class="previous-post" href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>">
                    <span class="arrow"
                          aria-hidden="true"><?php get_template_part('template-parts/nav-arrow-prev'); ?></span>
                    <span class="title-nav-prev"><?php esc_html_e('Previous Post', 'healfio') ?></span>
                </a>
                <?php
            }

            if ($next_post) {
                ?>
                <a class="next-post" href="<?php echo esc_url(get_permalink($next_post->ID)); ?>">
                    <span class="title-nav-next"><?php esc_html_e('Next Post', 'healfio') ?></span>
                    <span class="arrow"
                          aria-hidden="true"><?php get_template_part('template-parts/nav-arrow-next'); ?></span>
                </a>
                <?php
            }
            ?>

        </div><!-- .pagination-single-inner -->

    </nav><!-- .pagination-single -->

    <?php
}
