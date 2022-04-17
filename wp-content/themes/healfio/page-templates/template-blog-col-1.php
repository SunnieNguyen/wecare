<?php
/**
 * Template Name: Blog 1 Column
 */
get_header();
?>
    <main id="site-content" class="blog flex-grow-1" role="main">

        <div class="container container-narrow">
            <?php

            set_query_var('columns', 1);
            get_template_part('template-parts/blog-grid');

            ?>
        </div>
    </main>
<?php
get_footer();