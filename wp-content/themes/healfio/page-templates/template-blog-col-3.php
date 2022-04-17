<?php
/**
 * Template Name: Blog 3 Columns
 */
get_header();
?>
    <main id="site-content" class="blog flex-grow-1" role="main">

        <div class="container three-col">
            <?php

            set_query_var('columns', 3);
            get_template_part('template-parts/blog-grid');

            ?>
        </div>
    </main>
<?php
get_footer();