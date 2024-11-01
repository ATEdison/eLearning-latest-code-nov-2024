<?php

namespace STA\Inc\Shortcodes;

class ShortcodeYoutube {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('nn_youtube', [$this, 'nn_youtube']);
    }

    public function nn_youtube($attrs, $content = '') {
        ob_start();
        get_template_part('template-parts/shortcodes/youtube', '', $attrs);
        return ob_get_clean();
    }
}
