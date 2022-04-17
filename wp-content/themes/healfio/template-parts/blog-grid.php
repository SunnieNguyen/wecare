<?php

$columns = get_query_var('columns');

if (get_query_var('paged')) {

    $paged = get_query_var('paged');

} elseif (get_query_var('page')) {

    $paged = get_query_var('page');

} else {

    $paged = 1;
}

if (isset($_GET['s'])) {

    $wp_query = new WP_Query(array(
        's' => esc_sql($_GET['s']),
        'paged' => (int)$paged,
    ));
} else {

    $wp_query = new WP_Query(array(
        'post_type' => 'post',
        'paged' => (int)$paged,
    ));
}

if ($wp_query->have_posts()) {

    $i = 0;

    $col_counter = 0;

    while ($wp_query->have_posts()) {
        $i++;

        $col_counter = $col_counter + 1;

        if ($col_counter == 1) {
            echo '<div class="row">';
        }

        $wp_query->the_post();

        get_template_part('template-parts/blog-tile');

        if ($wp_query->current_post + 1 < $wp_query->post_count) {
            if ($col_counter == $columns) {
                $col_counter = 0;
                echo '</div>';
            }
        } else {
            $add_col = $columns - $col_counter;
            $ic = 1;
            while ($ic <= $add_col) {
                $ic++;
                echo '<div class="col-lg"></div>';
            }
            echo '</div>';
        }

    }

} else {
    ?>

    <h5 class="text-center"><?php esc_html_e('Empty Blog', 'healfio'); ?></h5>
    <a class="d-block text-center mt-5" href="/">
        <div class="d-inline-block elementor-button-link elementor-button elementor-size-md"><?php esc_html_e('Go to homepage', 'healfio') ?></div>
    </a>
    <?php
}

get_template_part('template-parts/pagination');