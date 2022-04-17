<?php
/**
 * Template for search, archive
 */
get_header(); ?>
    <div id="site-content-wrap-grow" class="flex-grow-1">
    <main id="site-content" class="container-lg pb-5" role="main">
        <?php

        $archive_title = '';
        $archive_subtitle = '';

        if (is_search()) {
            global $wp_query;

            $archive_title = sprintf(
                '&ldquo;' . get_search_query() . '&rdquo;'
            );

            if ($wp_query->found_posts) {
                $archive_subtitle = sprintf(
                /* translators: %s: Number of search results. */
                    esc_html(_n(
                        'We found %s result for your search.',
                        'We found %s results for your search.',
                        $wp_query->found_posts,
                        'healfio'
                    )),
                    number_format_i18n($wp_query->found_posts)
                );
            } else {
                $archive_subtitle = esc_html__('We could not find any results for your search. You can give it another try through the search form at the top of the page.', 'healfio');
            }
        } elseif (is_archive() && !have_posts()) {
            $archive_title = esc_html__('Nothing Found', 'healfio');
        } elseif (!is_home()) {
            $archive_title = get_the_archive_title();
            $archive_subtitle = get_the_archive_description();
        }

        if ($archive_subtitle) {
            ?>

            <header class="archive-header">

                <div class="archive-header-inner section-inner">

                    <?php if ($archive_subtitle) { ?>
                        <div class="archive-subtitle text-center"><?php echo wp_kses((wpautop($archive_subtitle)), 'post'); ?></div>
                    <?php } else { ?>
                        <div class="pb-1"></div>
                    <?php } ?>
                </div><!-- .archive-header-inner -->

            </header><!-- .archive-header -->

            <?php
        }

        if (have_posts()) {

            $i = 0;

            while (have_posts()) {
                $i++;
                if ($i > 1) {
                    echo '<div class="archive-separator"></div>';
                }
                the_post();

                get_template_part('template-parts/content', get_post_type());

            }
        } elseif (is_search()) {
            ?>

            <div class="no-search-results-form section-inner thin">

            </div><!-- .no-search-results -->

            <?php
        }
        ?>

        <?php get_template_part('template-parts/pagination'); ?>

    </main><!-- #site-content -->
    </div>
<?php
get_footer();
