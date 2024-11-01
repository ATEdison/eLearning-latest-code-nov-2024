<?php

namespace STA\Inc;

use Carbon_Fields\Field;
use STA\Inc\CarbonFields\PageHomeAgent;
use STA\Inc\CarbonFields\PageMyDashboard;
use STA\Inc\CarbonFields\ThemeOptions;

class CarbonFields {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('after_setup_theme', array($this, 'crb_load'));
        add_action('carbon_fields_register_fields', array($this, 'carbon_fields_register_fields'));
        add_filter('carbon_fields_association_field_options_sta_new_notification_users_user', [$this, 'carbon_fields_association_field_options_sta_new_notification_users_user'], PHP_INT_MAX);
    }

    /**
     * @param array $query_args
     * @return  array
     * @see Field\Association_Field::get_user_options_sql()
     */
    public function carbon_fields_association_field_options_sta_new_notification_users_user($query_args) {
        $query_args['search'] = '*' . $query_args['search'] . '*';
        return $query_args;
    }

    public function carbon_fields_register_fields() {
        ThemeOptions::register_fields();
        CptCourse::register_fields();
        UserDashboard::register_fields();
        CptCourseLesson::register_fields();
        EmailSettings::register_fields();
        CptCourseTopic::register_fields();
        NotificationSystem::register_fields();
        PageHomeAgent::register_fields();
        PageMyDashboard::register_fields();

        require_once(get_theme_file_path('/blocks/blocks.php'));
    }

    public function crb_load() {
        \Carbon_Fields\Carbon_Fields::boot();
    }

    public static function field_bg_color() {
        return Field::make('select', 'bg_color', 'Background Color')
            ->set_width(50)
            ->add_options(array(
                '' => 'None',
                'bg-sta-light' => 'Light Background',
            ));
    }
}
