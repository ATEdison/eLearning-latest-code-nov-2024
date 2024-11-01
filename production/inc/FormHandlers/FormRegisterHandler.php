<?php

namespace STA\Inc\FormHandlers;

use STA\Inc\CarbonFields\ThemeOptions;
use STA\Inc\UserAuth;

class FormRegisterHandler extends AbstractFormHandler {

    use TraitProfileImageFormHandler;

    protected function whitelist_fields() {
        return [
            'your_role' => [
                'type' => 'text',
                'label' => __('Your Role', 'sta'),
                'required' => true,
            ],
            'user_login' => [
                'type' => 'email',
                'label' => __('Email Address', 'sta'),
                'required' => true,
                'unique_email' => true,
            ],
            'first_name' => [
                'type' => 'text',
                'label' => __('First Name', 'sta'),
                'required' => true,
            ],
            'last_name' => [
                'type' => 'text',
                'label' => __('Last Name', 'sta'),
                'required' => true,
            ],
            'country' => [
                'type' => 'select',
                'label' => __('Country', 'sta'),
                'required' => true,
            ],
            'primary_contact_number' => [
                'type' => 'text',
                'label' => __('Primary Contact Number', 'sta'),
                'required' => true,
            ],
            'mobile_number' => [
                'type' => 'text',
                'label' => __('Mobile Number', 'sta'),
                'required' => false,
            ],
            'preferred_language' => [
                'type' => 'select',
                'label' => __('Preferred Language', 'sta'),
                'required' => true,
            ],
            'password' => [
                'type' => 'text',
                'label' => __('Password', 'sta'),
                'required' => true,
            ],
            'profile_image' => [
                'type' => 'image',
                'label' => __('Profile Image', 'sta'),
                'required' => false,
            ],
            'work_company_name' => [
                'type' => 'text',
                'label' => __('Company Name', 'sta'),
                'required' => true,
            ],
            'work_company_type' => [
                'type' => 'select',
                'label' => __('Company Type', 'sta'),
                'required' => true,
            ],
            'work_contact_number' => [
                'type' => 'text',
                'label' => __('Contact Number', 'sta'),
                'required' => false,
            ],
            'work_website' => [
                'type' => 'text',
                'label' => __('Website', 'sta'),
                'required' => false,
            ],
            'work_street_address' => [
                'type' => 'text',
                'label' => __('Street Address', 'sta'),
                'required' => true,
            ],
            'work_apartment_unit_suite_number' => [
                'type' => 'text',
                'label' => __('Apartment / Unit / Suite Number', 'sta'),
                'required' => false,
            ],
            'work_town_city' => [
                'type' => 'text',
                'label' => __('Town / City', 'sta'),
                'required' => true,
            ],
            'work_state_region' => [
                'type' => 'text',
                'label' => __('State / Region', 'sta'),
                'required' => true,
            ],
            'work_postcode' => [
                'type' => 'text',
                'label' => __('Postcode', 'sta'),
                'required' => true,
            ],
            'work_country' => [
                'type' => 'select',
                'label' => __('Country', 'sta'),
                'required' => true,
            ],
            'terms_1' => [
                'type' => 'acceptance',
                'label' => __('Term', 'sta'),
                'required' => true,
                'messages' => [
                    'required' => __('Please accept our terms', 'sta'),
                ],
            ],
            'terms_2' => [
                'type' => 'acceptance',
            ],
        ];
    }

    protected function save($post_data) {
        $user_login = wp_unslash($post_data['user_login']);
        $user_email = $user_login;
        $user_id = register_new_user($user_login, $user_email);

        // register failed
        if (is_wp_error($user_id)) {
            // var_dump($user_id->get_error_code()); die;
            $this->errors = is_array($this->errors) ? $this->errors : [];
            $this->global_errors = is_array($this->global_errors) ? $this->global_errors : [];

            $error_code = $user_id->get_error_code();
            switch ($error_code) {
                case 'username_exists':
                    $this->errors['user_login'] = __('This email is already registered. Please choose another one.', 'sta');
                    break;
                default:
                    $this->global_errors[] = $user_id->get_error_message();
                    break;
            }
            return;
        }

        /**
         * register success
         * do update user before sending welcome email
         * @see UserAuth::wp_new_user_notification_email()
         */

        // self::send_welcome_email($user_id);

        wp_safe_redirect(add_query_arg(['register' => 'check_mail'], get_permalink(ThemeOptions::get_register_page_id())));
        exit;
    }

    public function update_user_on_register_success($user_id) {
        $post_data = $this->post_data;

        $user_data = [
            'ID' => $user_id,
            'first_name' => $post_data['first_name'],
            'last_name' => $post_data['last_name'],
            'display_name' => sprintf('%s %s', $post_data['first_name'], $post_data['last_name']),
        ];

        wp_update_user($user_data);

        $meta_fields = array_keys($this->whitelist_fields());
        $exclude_fields = ['user_login', 'first_name', 'last_name', 'password', 'terms_1'];
        foreach ($meta_fields as $field) {
            if (in_array($field, $exclude_fields)) {
                continue;
            }
            carbon_set_user_meta($user_id, $field, $post_data[$field]);
        }

        // profile image
        $this->upload_profile_image($user_id);

        $subscribe = ($post_data['terms_2'] ?? '') == 'yes';
        if ($subscribe) {
            FormUserNewslettersSettingsHandler::subscribe($user_id);
        }
    }

    public static function send_welcome_email($user_id, $to = null) {
        $user = get_user_by('ID', $user_id);
        $data = UserAuth::instance()->wp_new_user_notification_email([], $user, '');
        $to = $to ?: $user->user_email;
        return wp_mail($to, $data['subject'], $data['message'], $data['headers']);
    }
}
