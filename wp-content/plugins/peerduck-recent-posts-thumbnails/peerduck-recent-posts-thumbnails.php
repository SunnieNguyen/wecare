<?php
/**
 * Plugin Name:       Recent Posts Widget with Thumbnails
 * Plugin URI:        https://peerduck.com
 * Description:       A small plugin to add thumbnails to recent posts widget.
 * Version:           1.0.0
 * Author:            PeerduckThemes
 * Author URI:        https://peerduck.com
 * Requires at least: 5.0
 * Tested up to:      5.6
 * License:           GNU General Public License v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       peerduck-recent-posts-thumbnails
 * Domain Path:       /languages
 */

class Peerduck_Recent_Posts_Thumb_Widget extends WP_Widget
{

    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'widget_recent_entries',
            'description'                 => esc_html__( 'Your site&#8217;s most recent Posts with thumbnails.', 'peerduck-recent-posts-thumbnails' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'recent-posts', esc_html__( 'Recent Posts with Thumbnails', 'peerduck-recent-posts-thumbnails' ), $widget_ops );
        $this->alt_option_name = 'widget_recent_entries';
    }

    function widget($args, $instance)
    {

        if (!isset($args['widget_id'])) {
            $args['widget_id'] = $this->id;
        }

        $title = (!empty($instance['title'])) ? $instance['title'] : esc_html__('Recent Posts', 'peerduck-recent-posts-thumbnails');

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        $number = (!empty($instance['number'])) ? absint($instance['number']) : 5;
        if (!$number)
            $number = 5;
        $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;

        /**
         * Filter the arguments for the Recent Posts widget.
         *
         * @param array $args An array of arguments used to retrieve the recent posts.
         * @see WP_Query::get_posts()
         *
         */
        $r = new WP_Query(apply_filters('widget_posts_args', array(
            'posts_per_page' => $number,
            'no_found_rows' => true,
            'post_status' => 'publish',
            'ignore_sticky_posts' => true
        )));

        if ($r->have_posts()) :
            ?>
            <?php echo wp_kses_post($args['before_widget']); ?>
            <?php if ($title) {
            echo wp_kses_post($args['before_title'] . $title . $args['after_title']);
        } ?>
            <ul>
                <?php while ($r->have_posts()) : $r->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>">
                            <?php
                            if (has_post_thumbnail()) {
                                the_post_thumbnail();
                            } else {
                                echo '<div class="w-post-placeholder"></div>';
                            }
                            ?>
                            <span class="post-title"><?php get_the_title() ? the_title() : the_ID(); ?></span>
                            <?php if ($show_date) : ?>
                                <span class="post-date"><?php echo get_the_date(); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <?php echo wp_kses_post($args['after_widget']); ?>
            <?php
            // Reset the global $the_post as this query will have stomped on it
            wp_reset_postdata();

        endif;
    }


    /**
     * Handles updating the settings for the current Recent Posts widget instance.
     *
     * @since 2.8.0
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update( $new_instance, $old_instance ) {
        $instance              = $old_instance;
        $instance['title']     = sanitize_text_field( $new_instance['title'] );
        $instance['number']    = (int) $new_instance['number'];
        $instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
        return $instance;
    }

    /**
     * Outputs the settings form for the Recent Posts widget.
     *
     * @since 2.8.0
     *
     * @param array $instance Current settings.
     */
    public function form( $instance ) {
        $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'peerduck-recent-posts-thumbnails' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'number' )); ?>"><?php esc_html_e( 'Number of posts to show:', 'peerduck-recent-posts-thumbnails' ); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3" />
        </p>

        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo esc_attr($this->get_field_id( 'show_date' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_date' )); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id( 'show_date' )); ?>"><?php esc_html_e( 'Display post date?','peerduck-recent-posts-thumbnails' ); ?></label>
        </p>
        <?php
    }

}

/**
 * Register widget on init
 *
 * @since 1.0
 */
function peerduck_recent_posts_grid_widget_registration()
{
    unregister_widget('WP_Widget_Recent_Posts');
    register_widget('Peerduck_Recent_Posts_Thumb_Widget');
}

add_action('widgets_init', 'peerduck_recent_posts_grid_widget_registration', 1);