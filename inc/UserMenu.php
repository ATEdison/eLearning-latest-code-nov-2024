<?php

namespace STA\Inc;

use STA\Inc\CarbonFields\ThemeOptions;

class UserMenu {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('wp_nav_menu_items', [$this, 'wp_nav_menu_items'], PHP_INT_MAX, 2);
    }

    public function wp_nav_menu_items($items, $args) {
        switch ($args->theme_location) {
            case 'primary_member':
                return $this->primary_member_nav_menu_items($items, $args);
            case 'user_dropdown':
                return $this->user_dropdown_nav_menu_items($items, $args);
        }

        return $items;
    }

    private function user_dropdown_nav_menu_items($items, $args) {
        ob_start();
        get_template_part('template-parts/header/user-dropdown-menu-start-extra-items');
        echo $items;
        get_template_part('template-parts/header/user-dropdown-menu-end-extra-items');
        return ob_get_clean();
    }

    private function primary_member_nav_menu_items($items, $args) {
        if (!is_user_logged_in()) {
            return $items;
        }
        ob_start();
        echo $items;
        get_template_part('template-parts/header/primary-member-menu-extra-items');
        return ob_get_clean();
    }
}
