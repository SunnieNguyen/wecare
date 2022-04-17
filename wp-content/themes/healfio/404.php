<?php
get_header();
?>

    <main id="site-content" class="container flex-grow-1 pb-5 mt-3" role="main">
        <h2 class="text-center mb-4 font-weight-600"><?php
            echo wp_kses((__('Ooops. <span class="pr-color">Page Not Found!</span>', 'healfio')), 'regular'); ?></h2>
        <div class="text-center">
            <h6><?php
                echo wp_kses((__('The page you are looking for doesnt exist.<br> Looks like you are in the wrong place.<br> Let us guide you back!', 'healfio')), 'regular'); ?></h6>
        </div>
        <a class="d-block text-center mt-5" href="/">
            <div class="d-inline-block elementor-button-link elementor-button elementor-size-md"><?php esc_html_e('Go to homepage', 'healfio'); ?></div>
        </a>
    </main><!-- #site-content -->
<?php
get_footer();
