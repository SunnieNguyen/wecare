<?php

$columns = get_query_var('columns');

$number_of_posts = get_query_var('number_of_posts', 0); // Custom number of posts

$posts = get_posts(array(
    'numberposts' => $number_of_posts
));

if (!empty($posts)) {

    $i = 0;

    $col_counter = 0;

    foreach ($posts as $post) {
        $i++;

        $col_counter = $col_counter + 1;

        setup_postdata($post);

        if ($number_of_posts + 1 !== $i) {

            if ($col_counter == 1) {
                echo '<div class="row">';
            }
            get_template_part('template-parts/blog-tile');
        }

        if ($i < $number_of_posts) {
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
    wp_reset_postdata();
} else {
    ?>
    <h5 class="text-center"><?php esc_html_e('Empty Blog', 'healfio'); ?></h5>
    <?php
}