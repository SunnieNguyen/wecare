<?php
/**
 * Custom comment walker for this theme.
 * @since Healfio 1.0
 */

if (!class_exists('healfio_Walker_Comment')) {
    class healfio_Walker_Comment extends Walker_Comment
    {

        /**
         * Outputs a comment in the HTML5 format.
         *
         * @param WP_Comment $comment Comment to display.
         * @param int $depth Depth of the current comment.
         * @param array $args An array of arguments.
         * @see https://developer.wordpress.org/reference/functions/get_avatar/
         * @see https://developer.wordpress.org/reference/functions/get_comment_reply_link/
         * @see https://developer.wordpress.org/reference/functions/get_edit_comment_link/
         *
         * @see wp_list_comments()
         * @see https://developer.wordpress.org/reference/functions/get_comment_author_url/
         * @see https://developer.wordpress.org/reference/functions/get_comment_author/
         */
        protected function html5_comment($comment, $depth, $args)
        {

            $tag = ('div' === $args['style']) ? 'div' : 'li';

            ?>
            <<?php echo wp_kses($tag, 'post'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static output ?> id="comment-<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
                <header class="comment-meta">
                    <div class="comment-author">
                        <?php
                        $comment_author_url = get_comment_author_url($comment);
                        $comment_author = get_comment_author($comment);
                        $avatar = get_avatar($comment, $args['avatar_size']);
                        if (0 !== $args['avatar_size']) {
                            if (empty($comment_author_url)) {
                                echo wp_kses($avatar, 'comment-avatar');
                            } else {
                                printf('<a href="%s" rel="external nofollow" class="url">', $comment_author_url); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Escaped in https://developer.wordpress.org/reference/functions/get_comment_author_url/
                                echo wp_kses($avatar, 'comment-avatar');
                            }
                        }

                        printf(
                            '<span class="fn">%1$s</span><span class="screen-reader-text says">%2$s</span>',
                            esc_html($comment_author),
                            esc_html__('says:', 'healfio')
                        );

                        if (!empty($comment_author_url)) {
                            echo '</a>';
                        }
                        ?>
                    </div><!-- .comment-author -->

                    <div class="comment-metadata">
                        <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                            <?php
                            /* translators: 1: Comment date, 2: Comment time. */
                            $comment_timestamp = sprintf(esc_html__('%1$s at %2$s', 'healfio'), get_comment_date('', $comment), get_comment_time());
                            ?>
                            <time datetime="<?php comment_time('c'); ?>"
                                  title="<?php echo esc_attr($comment_timestamp); ?>">
                                <?php echo esc_html($comment_timestamp); ?>
                            </time>
                        </a>
                        <?php
                        if (get_edit_comment_link()) {
                            echo ' <span aria-hidden="true">&bull;</span> <a class="comment-edit-link" href="' . esc_url(get_edit_comment_link()) . '">' . esc_html__('Edit', 'healfio') . '</a>';
                        }
                        ?>
                    </div><!-- .comment-metadata -->

                </header><!-- .comment-meta -->

                <div class="comment-content">

                    <?php

                    comment_text();

                    if ('0' === $comment->comment_approved) {
                        ?>
                        <p class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'healfio'); ?></p>
                        <?php
                    }

                    ?>

                </div><!-- .comment-content -->

                <?php

                $comment_reply_link = get_comment_reply_link(
                    array_merge(
                        $args,
                        array(
                            'add_below' => 'div-comment',
                            'depth' => $depth,
                            'max_depth' => $args['max_depth'],
                            'before' => '<span class="comment-reply">',
                            'after' => '</span>',
                        )
                    )
                );

                $by_post_author = healfio_is_comment_by_post_author($comment);

                if ($comment_reply_link || $by_post_author) {
                    ?>

                    <footer class="comment-footer-meta">

                        <?php
                        if ($comment_reply_link) {
                            echo wp_kses($comment_reply_link,'post'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Link is escaped in https://developer.wordpress.org/reference/functions/get_comment_reply_link/
                        }
                        if ($comment_reply_link && $by_post_author) {
                            echo '<span class="by-post-author">' . esc_html__(' to Post Author', 'healfio') . '</span>';
                        }
                        ?>

                    </footer>

                    <?php
                }
                ?>

            </article><!-- .comment-body -->

            <?php
        }
    }
}
