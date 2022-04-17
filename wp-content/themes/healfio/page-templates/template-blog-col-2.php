<?php
/**
 * Template Name: Blog 2 Columns
 */
get_header();
?>
    <main id="site-content" class="blog flex-grow-1" role="main">

        <div class="container two-col">
            <?php

            set_query_var('columns', 2);
            get_template_part('template-parts/blog-grid');

            ?>
        </div>
    </main>
<?php
get_footer();