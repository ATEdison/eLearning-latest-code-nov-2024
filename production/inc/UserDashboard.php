<?php

namespace STA\Inc;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use STA\Inc\CarbonFields\ThemeOptions;

class UserDashboard {

    private static $instance;
    private static $country_options = [];

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('rewrite_rules_array', [$this, 'user_dashboard_rewrite_rules']);
        add_filter('query_vars', [$this, 'query_vars']);
    }

    public function query_vars($vars) {
        $vars[] = 'sta_subpage';
        return $vars;
    }

    public function user_dashboard_rewrite_rules($rules) {
        $new_rules = [];
        $dashboard_page_id = ThemeOptions::get_user_dashboard_page_id();
        $dashboard_page_slug = get_post_field('post_name', $dashboard_page_id);
        $new_rules[sprintf('%1$s/(.+)', $dashboard_page_slug)] = sprintf('index.php?pagename=%1$s&sta_subpage=$matches[1]', $dashboard_page_slug);
        return $new_rules + $rules;
    }

    public static function training_achievement_page_url() {
        $user_dashboard_page_url = ThemeOptions::get_user_dashboard_page_url();
        return untrailingslashit($user_dashboard_page_url) . '/training-achievement';
    }

    public static function leaderboards_page_url() {
        $user_dashboard_page_url = ThemeOptions::get_user_dashboard_page_url();
        return untrailingslashit($user_dashboard_page_url) . '/leaderboards';
    }

    public static function notifications_page_url() {
        $user_dashboard_page_url = ThemeOptions::get_user_dashboard_page_url();
        return untrailingslashit($user_dashboard_page_url) . '/notifications';
    }

    public static function communications_page_url() {
        $user_dashboard_page_url = ThemeOptions::get_user_dashboard_page_url();
        return untrailingslashit($user_dashboard_page_url) . '/communications';
    }

    public static function referrals_page_url() {
        $user_dashboard_page_url = ThemeOptions::get_user_dashboard_page_url();
        return untrailingslashit($user_dashboard_page_url) . '/referrals';
    }

    public static function user_profile_page_url() {
        $user_dashboard_page_url = ThemeOptions::get_user_dashboard_page_url();
        return untrailingslashit($user_dashboard_page_url) . '/my-details';
    }

    public static function get_user_country_code($user_id) {
        // return carbon_get_user_meta($user_id, 'country');
        return get_user_meta($user_id, '_nn_oauth_country_code', true);
    }

    public static function country_options() {
        if (is_array(self::$country_options) && !empty(self::$country_options)) {
            return self::$country_options;
        }
        $data = json_decode(file_get_contents(get_theme_file_path('assets/resources/countries.json')), true);
        // printf('<pre>%s</pre>', var_export($data, true)); die;
        self::$country_options = [];
        foreach ($data as $item) {
            self::$country_options[$item['code']] = $item['name'];
        }
        return self::$country_options;
    }

    public static function company_type_options() {
        return [
            'Travel Agency' => 'Travel Agency',
        ];
    }

    public static function register_fields() {
        Container::make('user_meta', 'User Details')
            ->add_tab('Personal', [
                Field::make('select', 'country', 'Country')
                    ->add_options(self::country_options()),
                Field::make('text', 'primary_contact_number', 'Primary Contact Number'),
                Field::make('text', 'mobile_number', 'Mobile Number'),
                Field::make('image', 'profile_image', 'Profile Image'),
            ])
            ->add_tab('Work', [
                Field::make('text', 'work_company_name', 'Company Name'),
                Field::make('select', 'work_company_type', 'Company Type')
                    ->add_options(self::company_type_options()),
                Field::make('text', 'work_contact_number', 'Contact Number'),
                Field::make('text', 'work_website', 'Website'),
                Field::make('text', 'work_street_address', 'Street Address'),
                Field::make('text', 'work_apartment_unit_suite_number', 'Apartment / Unit / Suite Number'),
                Field::make('text', 'work_town_city', 'Town / City'),
                Field::make('text', 'work_state_region', 'State / Region'),
                Field::make('text', 'work_postcode', 'Postcode'),
                Field::make('select', 'work_country', 'Country')
                    ->add_options(self::country_options()),
            ])
            ->add_tab('Contact channels', [
                Field::make('checkbox', 'contact_channel_email', 'Email')
                    ->set_option_value('yes'),
                Field::make('checkbox', 'contact_channel_sms', 'Text Messages')
                    ->set_option_value('yes'),
            ])
            ->add_tab('Newsletters', [
                Field::make('checkbox', 'newsletter_saudi_expert', 'Saudi Expert newsletter')
                    ->set_option_value('yes'),
                Field::make('checkbox', 'newsletter_news_events', 'News & Events newsletter')
                    ->set_option_value('yes'),
            ]);
    }

    public static function get_user_primary_contact_number($user_id) {
        return carbon_get_user_meta($user_id, 'primary_contact_number');
    }

    public static function user_profile_image($user_id, $size = 'thumb_160x160', $icon = false, $attr = '') {
        // $image_id = carbon_get_user_meta($user_id, 'profile_image');
        // return self::profile_image($image_id, $size, $icon, $attr);
        $image_url = get_user_meta($user_id, '_nn_oauth_profile_image_url', true);
        if ($image_url) {
            return sprintf('<img src="%1$s" alt="">', $image_url);
        }
        return '<span class="user-profile-placeholder"></span>';
    }

    public static function profile_image($image_id, $size = 'thumb_160x160', $icon = false, $attr = '') {
        // $image_id = null;
        if (!$image_id) {
            return '<span class="user-profile-placeholder"></span>';
        }
        return wp_get_attachment_image($image_id, $size, $icon, $attr);
    }

    public static function get_country_label($country_code) {
        $country_options = self::country_options();
        if (isset($country_options[$country_code])) {
            return $country_options[$country_code];
        }
        return '';
    }

    public static function get_user_newsletters_settings($user_id) {
        return [
            'newsletter_saudi_expert' => carbon_get_user_meta($user_id, 'newsletter_saudi_expert'),
            'newsletter_news_events' => carbon_get_user_meta($user_id, 'newsletter_news_events'),
        ];
    }

    public static function get_user_contact_channels_settings($user_id) {
        return [
            'contact_channel_email' => carbon_get_user_meta($user_id, 'contact_channel_email'),
            'contact_channel_sms' => carbon_get_user_meta($user_id, 'contact_channel_sms'),
        ];
    }

    public static function get_language_settings($user_id) {
        return [
            'preferred_language' => get_user_meta($user_id, 'locale', true),
        ];
    }

    public static function get_personal_details($user_id) {
        $user = get_user_by('ID', $user_id);
        if (!($user instanceof \WP_User)) {
            return null;
        }

        $data = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->user_email,
        ];

        $meta_fields = ['country', 'primary_contact_number', 'mobile_number', 'profile_image'];
        foreach ($meta_fields as $field) {
            $data[$field] = carbon_get_user_meta($user_id, $field);
        }

        return $data;
    }

    public static function get_work_details($user_id) {
        $data = [];
        $meta_fields = [
            'work_company_name', 'work_company_type', 'work_contact_number', 'work_website',
            'work_street_address', 'work_apartment_unit_suite_number', 'work_town_city', 'work_state_region', 'work_postcode', 'work_country',
        ];
        foreach ($meta_fields as $field) {
            $data[$field] = carbon_get_user_meta($user_id, $field);
        }
        return $data;
    }
}
