<main id="site-content" class="flex-grow-1" role="main">
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
</main><!-- #site-content -->