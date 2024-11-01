<?php

namespace STA\Inc\CarbonFields;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use STA\Inc\BadgeSystem;
use STA\Inc\CptCourse;
use STA\Inc\EmailSettings;
use STA\Inc\GoogleTextToSpeech;
use STA\Inc\Helpers;
use STA\Inc\ThemeSetup;

class ThemeOptions {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('carbon_fields_should_save_field_value', [$this, 'carbon_fields_should_save_field_value'], PHP_INT_MAX, 3);
        add_action('carbon_fields_theme_options_container_saved', [$this, 'on_saved_theme_options']);
    }

    public function on_saved_theme_options() {
        $this->assign_badge_to_users();
    }

    private function assign_badge_to_users() {
        if (!current_user_can('administrator')) {
            return;
        }

        if (!isset($_POST['sta_assign_badge'])) {
            return;
        }

        $fields = $_POST['carbon_fields_compact_input'] ?? [];

        $user_list = $fields['_badge_assignment_users'] ?? [];
        $badge_title = $fields['_badge_assignment_badge'] ?? '';

        if (!$badge_title || empty($user_list)) {
            return;
        }

        $badge = self::get_badge_by_title($badge_title);
        if (empty($badge)) {
            return;
        }

        foreach ($user_list as $user_id) {
            $user_id = str_replace('user:user:', '', $user_id);
            $user_id = intval($user_id);
            BadgeSystem::earn_a_badge($user_id, $badge_title, $badge['points']);
        }
    }

    private static function get_badge_by_title($title) {
        $badge_list = self::get_badge_list();
        foreach ($badge_list as $item) {
            if ($item['title'] == $title) {
                return $item;
            }
        }
        return null;
    }

    public static function get_custom_badge_by_slug($slug) {
        $badge_list = self::get_badge_list();
        foreach ($badge_list as $item) {
            if (sanitize_title($item['title']) == $slug) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param bool $save
     * @param mixed $value
     * @param \Carbon_Fields\Field\Field $field
     * @return bool
     */
    public function carbon_fields_should_save_field_value($save, $value, $field) {
        // do not save badge assigment form
        if (str_contains($field->get_base_name(), 'badge_assignment_')) {
            return false;
        }
        return $save;
    }

    public static function register_fields() {
        $lang = ThemeSetup::current_lang();
        self::register_fields_for_lang($lang);

        // fallback
        if (EmailSettings::should_register_fields_fallback($lang)) {
            self::register_fields_for_lang('en');
        }
    }

    public static function meta_field($field, $lang = '') {
        return EmailSettings::meta_field($field, $lang);
    }

    private static function register_fields_for_lang($lang) {
        $theme_options = Container::make('theme_options', 'Theme Options')
            // header
            ->add_tab('Quiz', [
                Field::make('html', 'sq1', '')->set_html('Variables:<br><ul><li><code>{{data.correct}}</code> the number of correct answers</li><li><code>{{data.count}}</code> the number of questions</li></ul>'),
                Field::make('textarea', self::meta_field('quiz_failed'), 'Fail Message')
                    ->set_default_value("You answered {{data.correct}} out of {{data.count}} questions correctly. You'll need to answer 80% correct to complete this module."),
                Field::make('textarea', self::meta_field('quiz_passed'), 'Pass Message')
                    ->set_default_value("You've answered {{data.correct}} out of {{data.count}} questions correctly for this quiz and can now proceed to the next module."),
                Field::make('textarea', self::meta_field('quiz_quick'), 'Quick Quiz Message')->set_default_value("You've answered {{data.correct}} out of {{data.count}} questions correctly for this quiz."),

            ])
            // header
            ->add_tab('Header', [
                // Field::make('image', 'header_logo', 'Logo'),
            ])
            // footer
            ->add_tab('Footer', [
                Field::make('separator', 's1', 'Social'),
                Field::make('text', self::meta_field('social_facebook', $lang), 'Facebook'),
                Field::make('text', self::meta_field('social_instagram', $lang), 'Instagram'),
                Field::make('text', self::meta_field('social_twitter', $lang), 'Twitter'),
                Field::make('text', self::meta_field('social_youtube', $lang), 'Youtube'),
                Field::make('text', self::meta_field('social_linkedin', $lang), 'LinkedIn'),
                Field::make('separator', 's2', 'Call Center'),
                Field::make('rich_text', self::meta_field('footer_call_center_text', $lang), 'Text'),
            ])
            // oauth
            ->add_tab('OAuth', [
                Field::make('text', 'oauth_base_url', 'Base URL'),
                Field::make('text', 'oauth_client_id', 'Client ID'),
                Field::make('text', 'oauth_client_secret', 'Client Secret'),
                Field::make('text', 'oauth_login_url', 'Login URL'),
                Field::make('text', 'oauth_registration_url', 'Registration URL'),
                Field::make('text', 'oauth_edit_profile_url', 'Edit Profile URL'),
            ])
            // email signature
            ->add_tab('Training & Achievement', [
                Field::make('checkbox', 'hide_certificate_section', 'Hide certificate section'),
                Field::make('image', self::meta_field('email_signature', $lang), 'Email Signature'),
                Field::make('image', self::meta_field('website_tile', $lang), 'Website Tile generator'),
            ])
            // welcome prompt
            ->add_tab('Welcome Prompt', [
                Field::make('complex', self::meta_field('welcome_prompt_steps', $lang), 'Steps')
                    ->set_layout('tabbed-vertical')
                    ->add_fields(array(
                        Field::make('text', 'video_url', 'Video URL'),
                        Field::make('image', 'image', 'Image'),
                        Field::make('text', 'heading', 'Heading'),
                        Field::make('rich_text', 'desc', 'Description'),
                    )),
            ])

            // google API
            ->add_tab('Google Text To Speech', GoogleTextToSpeech::theme_options($lang))

            // login
            ->add_tab('Login', [

                // SSID Method
                Field::make( 'select', 'ssid_method' )
                    ->add_options( array('Oauth SSID', 'Key Cloak SSID') )
                    ->set_help_text( 'Pick a SSID Method' ),

                Field::make('association', self::meta_field('user_homepage', $lang), 'User Homepage')
                    ->set_max(1)
                    ->set_types([
                        ['type' => 'post', 'post_type' => 'page'],
                    ]),
                Field::make('association', self::meta_field('user_dashboard_page', $lang), 'User Dashboard Page')
                    ->set_max(1)
                    ->set_types([
                        ['type' => 'post', 'post_type' => 'page'],
                    ]),
                Field::make('association', self::meta_field('register_page', $lang), 'Register Page')
                    ->set_max(1)
                    ->set_types([
                        ['type' => 'post', 'post_type' => 'page'],
                    ]),
                Field::make('association', self::meta_field('login_page', $lang), 'Login Page')
                    ->set_max(1)
                    ->set_types([
                        ['type' => 'post', 'post_type' => 'page'],
                    ]),
                Field::make('association', self::meta_field('reset_password_page', $lang), 'Reset Password Page')
                    ->set_max(1)
                    ->set_types([
                        ['type' => 'post', 'post_type' => 'page'],
                    ]),
            ])
            
            // KeyClock Auth
            ->add_tab('KeyClock SSID', [
                Field::make('text', 'key_clock_base_url', 'Base URL'),
                Field::make('text', 'key_clock_client_id', 'Client ID'),
                Field::make('text', 'key_clock_client_secret', 'Client Secret'),
                Field::make('text', 'key_clock_login_url', 'Login URL'),
                Field::make('text', 'key_clock_registration_url', 'Userinfo URL'),
                Field::make('text', 'key_clock_edit_profile_url', 'Edit User Profile URL'),
                Field::make('text', 'key_clock_logout_url', 'Logout URL'),
            ]);

        // var_dump(self::class . '\\badge_option_list');

        Container::make('theme_options', 'Badges')
            ->set_page_parent($theme_options)
            ->add_tab('Manually assign badge to users', [
                Field::make('association', 'badge_assignment_users', 'Users')
                    ->set_types([
                        ['type' => 'user'],
                    ]),
                Field::make('select', 'badge_assignment_badge', 'Badge')
                    ->add_options(self::class . '::badge_option_list')
                    ->set_help_text('Badges must be saved first before being displayed here'),
                Field::make('html', 'badge_assignment_submit')
                    ->set_html('<button type="submit" class="button button-primary" name="sta_assign_badge" value="yes">Submit</button>'),
            ])
            ->add_tab('Badges', [
                Field::make('complex', self::meta_field('badge_list', $lang), '')
                    ->set_layout('tabbed-vertical')
                    ->add_fields(array(
                        Field::make('image', 'design', 'Design'),
                        Field::make('text', 'title', 'Title')
                            ->set_help_text('Title must be unique between badges'),
                        Field::make('textarea', 'desc', 'Description'),
                        Field::make('text', 'points', 'Points')->set_attributes(['type' => 'number']),
                    ))
                    ->set_header_template('<%- title %> (<%- points %> pts)'),
            ])
            ->add_tab('System Badges', BadgeSystem::field_system_badges($lang));
    }

    public static function get_quiz_failed_message() {
        return self::get_meta_value('quiz_failed');
    }

    public static function get_quiz_passed_message() {
        return self::get_meta_value('quiz_passed');
    }

    public static function get_quick_quiz_message() {
        return self::get_meta_value('quiz_quick');
    }

    public static function get_oauth_edit_profile_url() {
        return carbon_get_theme_option('oauth_edit_profile_url');
    }

    public static function get_oauth_login_url() {
        return carbon_get_theme_option('oauth_login_url');
    }

    public static function get_oauth_registration_url() {
        return carbon_get_theme_option('oauth_registration_url');
    }

    public static function get_oauth_settings() {
        return [
            'base_url' => carbon_get_theme_option('oauth_base_url'),
            'client_id' => carbon_get_theme_option('oauth_client_id'),
            'client_secret' => carbon_get_theme_option('oauth_client_secret'),
        ];
    }

    public static function badge_option_list() {
        $badge_list = self::get_badge_list();
        $option_list = ['' => 'Please select a badge'];
        foreach ($badge_list as $item) {
            $option_list[$item['title']] = sprintf('%s (%s pts)', $item['title'], $item['points']);
        }
        return $option_list;
    }

    /**
     * @return bool
     */
    public static function should_hide_certificate_section() {
        return carbon_get_theme_option('hide_certificate_section') == 'yes';
    }

    public static function get_badge_list() {
        return self::get_meta_value('badge_list');
    }

    public static function get_website_tile_file_id() {
        return self::get_meta_value('website_tile');
    }

    public static function get_email_signature_file_id() {
        return self::get_meta_value('email_signature');
    }

    public static function get_google_text_to_speech_key() {
        return carbon_get_theme_option('google_text_to_speech_key');
    }

    public static function get_meta_value($field, $lang = null, $fallback_to_en = true) {
        return EmailSettings::get_meta_value($field, $lang, $fallback_to_en);
    }

    public static function welcome_prompt_steps() {
        return self::get_meta_value('welcome_prompt_steps');
    }

    public static function get_user_dashboard_page_url() {
        $page_id = self::get_user_dashboard_page_id();
        return get_permalink($page_id);
    }

    public static function get_user_homepage_id() {
        $page_id = self::get_meta_value('user_homepage');
        return $page_id[0]['id'];
    }

    public static function get_user_dashboard_page_id() {
        $page_id = self::get_meta_value('user_dashboard_page');
        return $page_id[0]['id'];
    }

    public static function get_login_page_id() {
        $page_id = self::get_meta_value('login_page');
        return $page_id[0]['id'];
    }

    public static function get_register_page_id() {
        $page_id = self::get_meta_value('register_page');
        return $page_id[0]['id'];
    }

    public static function get_reset_password_page_id() {
        $page_id = self::get_meta_value('reset_password_page');
        return $page_id[0]['id'];
    }

    public static function call_center_text() {
        return self::get_meta_value('footer_call_center_text');
    }

    public static function get_social() {
        // $field_list = ['facebook', 'instagram', 'twitter', 'youtube', 'linkedin'];
        $field_list = ['twitter', 'youtube', 'linkedin'];
        $data = [];
        foreach ($field_list as $field) {
            $value = self::get_meta_value('social_' . $field);
            if ($value) {
                $data[$field] = $value;
            }
        }
        return $data;
    }

    
    // Key Cloak SSID MEthod
    public static function get_ssid_method() {
        $ssid_method = self::get_meta_value('ssid_method');
        return $ssid_method;
    }

    public static function get_key_clock_settings() {
        return [
            'base_url' => carbon_get_theme_option('key_clock_base_url'),
            'client_id' => carbon_get_theme_option('key_clock_client_id'),
            'client_secret' => carbon_get_theme_option('key_clock_client_secret'),
        ];
    }

    public static function get_key_clock_login_url() {
        return carbon_get_theme_option('key_clock_login_url');
    }

    public static function get_key_clock_edit_profile_url() {
        return carbon_get_theme_option('key_clock_edit_profile_url');
    }

}
