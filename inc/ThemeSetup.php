<?php

namespace STA\Inc;

class ThemeSetup {

    private static $instance;
    private static $lang = '';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('after_setup_theme', array($this, 'after_setup_theme'));
        add_action('admin_bar_menu', [$this, 'admin_bar_item'], PHP_INT_MAX);
        add_action('admin_menu', [$this, 'admin_menu'], PHP_INT_MAX);
        add_filter('xmlrpc_enabled', '__return_false');
        add_filter('json_enabled', '__return_false');
        add_filter('json_jsonp_enabled', '__return_false');

        add_filter('the_content', [$this, 'correct_content_space']);
    }

    public function correct_content_space($content) {
        $string = htmlentities($content, null, 'utf-8');
        $content = str_replace('&nbsp;', ' ', $string);
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $content = html_entity_decode($content);
        return $content;
    }

    public function admin_menu() {
        add_submenu_page('tools.php', 'STA Help', 'STA Help', 'manage_options', 'sta-help', [$this, 'admin_sta_help_page']);
    }

    public function admin_sta_help_page() {
        get_template_part('template-parts/admin/help');
    }

    /**
     * @param \WP_Admin_Bar $admin_bar
     */
    public function admin_bar_item($admin_bar) {
        ob_start();
        get_template_part('template-parts/admin/help-title');
        $title = ob_get_clean();

        $admin_bar->add_menu(array(
            'id' => 'help',
            'parent' => null,
            'group' => null,
            'title' => $title,
            'href' => admin_url('/tools.php?page=sta-help'),
        ));
    }

    public static function current_lang($refresh = false) {
        if (!$refresh && self::$lang) {
            return self::$lang;
        }

        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        if (!is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
            self::$lang = 'en';
            return self::$lang;
        }

        /**
         * @var \SitePress $sitepress
         */
        global $sitepress;
        self::$lang = $sitepress->get_current_language();
        return self::$lang;
    }

    public function after_setup_theme() {
        /**
         * Let WordPress manage the document title.
         * This theme does not use a hard-coded <title> tag in the document head,
         * WordPress will provide it for us.
         */
        add_theme_support('title-tag');

        /**
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');
        // set_post_thumbnail_size(1568, 9999);
        add_image_size('thumb_160x160', 160, 160, true);
        // add_image_size('thumb_50x50', 50, 50, true);
        add_image_size('thumb_485x', 485);
        add_image_size('thumb_485x250', 485, 250, true);
        add_image_size('badge_80', 80);

        register_nav_menus(array(
            'top' => 'Top Menu',
            'primary_public' => 'Primary Menu (Public)',
            'primary_member' => 'Primary Menu (Members)',
            'user_dropdown' => 'User Dropdown Menu',
            'sidebar_menu' => 'Sidebar Menu',
            'footer_1' => 'Footer Menu 1',
            'footer_2' => 'Footer Menu 2',
            'footer_bottom' => 'Footer Bottom Menu',
	        'maintenance_page' => 'Maintenance Page',
            'discover_saudi' => 'Discover Saudi',
            'business_and_partner' => 'Business & Partner',
            'get_help' => 'Get Help',
        ));

        /**
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5',
            array(
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
                'navigation-widgets',
            )
        );

        // Add support for responsive embedded content.
        add_theme_support('responsive-embeds');

        // Add support for custom units.
        // This was removed in WordPress 5.6 but is still required to properly support WP 5.5.
        add_theme_support('custom-units');
    }
}
