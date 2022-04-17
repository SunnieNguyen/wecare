<?php
/**
 * The template for displaying single posts and pages.
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @since Healfio 1.0
 */

get_header();
?>

<main id="site-content" class="flex-grow-1" role="main">

    <div class="container-xl blog-post">
        <div class="row">

            <div class="col-lg-8 pb-45 pb-lg-0 mx-auto">
                <?php

                if (have_posts()) {


                    if (!is_search()) {
                        get_template_part('template-parts/featured-media');
                    }


                    while (have_posts()) {
                        the_post();

                        get_template_part('template-parts/content', get_post_type());
                    }
                }

                ?>
            </div>

            <?php if (is_active_sidebar('blog-sidebar')) { ?>
                <div class="col-lg-4">
                    <?php
                    dynamic_sidebar('blog-sidebar'); ?>
                </div>
            <?php } ?>

        </div>
    </div>

</main><!-- #site-content -->

<?php get_footer(); ?>
