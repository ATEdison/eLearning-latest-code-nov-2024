<?php

namespace STA\Inc;

class UserWelcome {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('body_class', [$this, 'body_class']);
        add_action('wp_ajax_sta_finish_welcome_prompt', [$this, 'sta_finish_welcome_prompt']);
    }

    public function sta_finish_welcome_prompt() {
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        if (!$user_id) {
            return;
        }
        update_user_meta($user_id, '_sta_finish_welcome_prompt', true);
    }

    public function body_class($class) {
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        if (self::should_display_welcome_prompt($user_id)) {
            $class[] = 'sta-welcome-prompt-active';
        }
        return $class;
    }

    public static function should_display_welcome_prompt($user_id) {
        if (!$user_id) {
            return false;
        }

        // only work on user home page
        if (!is_page_template('page-templates/homepage-agent.php')) {
            return false;
        }

        if (isset($_GET['welcome'])) {
            return true;
        }

        $is_finished = get_user_meta($user_id, '_sta_finish_welcome_prompt', true);
        if ($is_finished) {
            return false;
        }

        return true;
    }
}
