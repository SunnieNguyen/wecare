<?php

function healfio_kses_allowed_html($tags, $context): array
{
    switch ($context) {
        case 'regular':
            $tags = array(
                'span' => array(
                    'class' => array(),
                    'aria-hidden' => array(),
                ),
                'br' => array(),
                'b' => array(),
                'strong' => array(),
            );
            return $tags;
        case 'link':
            $tags = array(
                'a' => array(
                    'href' => array(),
                    'target' => array(),
                )
            );
            return $tags;
        case 'comment-avatar':
            $tags = array(
                'img' => array(
                    'src' => array(),
                    'alt' => array(),
                    'class' => array(),
                    'srcset' => array(),
                    'height' => array(),
                    'width' => array(),
                    'loading' => array(),
                )
            );
            return $tags;
        case 'woo-style':
            $tags = array(
                'style' => array(),
            );
            return $tags;
        case 'pagination':
            $tags = array(
                'div' => array(),
                'span' => array(),
                'a' => array(),
            );
            return $tags;
        default:
            return $tags;
    }
}

add_filter('wp_kses_allowed_html', 'healfio_kses_allowed_html', 10, 2);