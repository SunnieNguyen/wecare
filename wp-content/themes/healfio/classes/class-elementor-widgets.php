<?php

if (did_action( 'elementor/loaded' )) {
    if (!class_exists('healfio_Elementor_Widgets')) {
        class healfio_Elementor_Widgets
        {
            protected static $instance = null;

            public static function get_instance()
            {
                if (!isset(static::$instance)) {
                    static::$instance = new static;
                }

                return static::$instance;
            }

            protected function __construct()
            {
                require_once('class-elementor-recent-posts.php');
                add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
            }

            public function register_widgets()
            {
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\healfio_Recent_Posts());
            }

        }

        add_action('init', 'healfio_elementor_init');
        function healfio_elementor_init()
        {
            healfio_Elementor_Widgets::get_instance();
        }
    }
}