<?php

namespace STA\Inc;

class Scripts {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
    }

    public function wp_enqueue_scripts() {
        wp_enqueue_script('wp-util');
        wp_enqueue_script('d3', get_template_directory_uri() . '/assets/js/d3.min.js');

        $css_version = filemtime(get_stylesheet_directory() . '/assets/css/main.css');
        wp_enqueue_style('sta-main', get_stylesheet_directory_uri() . '/assets/css/main.css?v=' . $css_version, array(), $css_version);

        $js_version = filemtime(get_stylesheet_directory() . '/assets/js/main.js');
        wp_enqueue_script('sta-main', get_stylesheet_directory_uri() . '/assets/js/main.js?v=' . $js_version, array('jquery', 'wp-util'), $js_version);

        wp_localize_script('sta-main', 'staSettings', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'fontFamiliesUrl' => get_template_directory_uri() . '/assets/css/font-families.css',
            'i18n' => [
                'selectPlaceholder' => __('Please select', 'sta'),
            ],
        ]);
    }
}
