<?php
/**
 * Displays the featured media
 * @since Healfio 1.0
 */
if (has_post_thumbnail() && !post_password_required()) {
    ?>
    <div class="featured-media">
        <figure class="mb-0">
            <?php
            the_post_thumbnail();
            ?>
        </figure>
    </div>
    <?php
}