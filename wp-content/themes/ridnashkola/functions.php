<?php
function educenter_child_enqueue_styles() {
    wp_enqueue_style('educenter-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('educenter-child-style', get_stylesheet_directory_uri() . '/style.css', array('educenter-parent-style'), wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'educenter_child_enqueue_styles');
?>
