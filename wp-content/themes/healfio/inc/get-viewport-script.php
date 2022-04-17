<?php
/*
 * Generates inline JS to set viewports.
 */
if (!function_exists('healfio_get_viewport_script')) {
    function healfio_get_viewport_script()
    {
        ob_start();
        echo "
    if (screen.width >= 1535 && screen.width < 2561) { 
        let mvp = document.getElementById('siteViewport');
        mvp.setAttribute('content','width=1920');
    }
    if (screen.width > 767 && screen.width < 1535) {
        let mvp = document.getElementById('siteViewport');
        mvp.setAttribute('content','width=1700');
    }
    ";
        return ob_get_clean();
    }
}