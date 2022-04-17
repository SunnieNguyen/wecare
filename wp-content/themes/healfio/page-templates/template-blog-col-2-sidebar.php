<?php
/**
 * Template Name: Blog 2 Columns with Sidebar
 */
get_header();
?>
    <main id="site-content" class="blog flex-grow-1" role="main">

        <div class="container two-col-sidebar">
            <?php if (is_active_sidebar('blog-sidebar')) { ?>
                <div class="row">
                    <div class="col-lg-8">
                        <?php
                        set_query_var('columns', 2);
                        get_template_part('template-parts/blog-grid');

                        ?>
                    </div>
                    <div class="col-lg-4">
                        <?php dynamic_sidebar('blog-sidebar'); ?>
                    </div>
                </div>
            <?php } else {
                set_query_var('columns', 3);
                get_template_part('template-parts/blog-grid');
            } ?>
        </div>
    </main>
<?php
get_footer();