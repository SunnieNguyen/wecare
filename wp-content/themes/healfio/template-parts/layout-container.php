<main id="site-content" class="flex-grow-1" role="main">

    <div class="container-lg <?php
    if (function_exists('is_checkout')) {
        if (is_checkout()) {
            echo 'container-narrow';
        }
    } ?>">
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

</main><!-- #site-content -->