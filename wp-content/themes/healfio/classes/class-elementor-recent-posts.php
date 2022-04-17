<?php

namespace Elementor;

if (!class_exists('healfio_Recent_Posts')) {
    class healfio_Recent_Posts extends Widget_Base
    {

        public function get_name()
        {
            return 'peerduck-recent-posts';
        }

        public function get_title()
        {
            return 'Recent Posts Grid';
        }

        public function get_icon()
        {
            return 'eicon-gallery-grid';
        }

        public function get_categories()
        {
            return ['basic'];
        }

        protected function _register_controls()
        {

            $this->start_controls_section(
                'section_title',
                [
                    'label' => esc_html__('Content', 'healfio'),
                ]
            );

            $this->add_control(
                'number_of_posts',
                [
                    'label' => esc_html__('Number of posts', 'healfio'),
                    'label_block' => true,
                    'type' => Controls_Manager::NUMBER,
                    'placeholder' => esc_html__('Enter number of posts', 'healfio'),
                    'min' => 3,
                    'max' => 9,
                    'step' => 3,
                    'default' => 3,
                ]
            );

            $this->end_controls_section();
        }

        protected function render()
        {
            $settings = $this->get_settings_for_display();

            echo '<div class="peerduck-recent-posts container-fluid three-col">';
            set_query_var('columns', 3);
            set_query_var('number_of_posts', $settings['number_of_posts']);
            get_template_part('template-parts/elementor-blog-grid');
            echo '</div>';

        }

        protected function _content_template()
        {

        }

    }
}