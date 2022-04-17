<?php

if (is_search() || is_archive()) {
    the_title('<h4 class="entry-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h4>');
}

