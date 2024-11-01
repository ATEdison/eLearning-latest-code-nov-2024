<?php

namespace STA\Inc\FormHandlers;

class FormUserPasswordHandler extends AbstractFormHandler {

    protected function whitelist_fields() {
        return [
            'password_current' => [
                'type' => 'text',
                'label' => __('Current Password', 'sta'),
                'required' => true,
            ],
            'password_new' => [
                'type' => 'text',
                'label' => __('New Password', 'sta'),
                'required' => true,
            ],
            'password_confirm' => [
                'type' => 'text',
                'label' => __('Confirm New Password', 'sta'),
                'required' => true,
            ],
        ];
    }

    public function proceed() {
        // for logged in users only
        if (!is_user_logged_in()) {
            return;
        }

        parent::proceed();
    }

    protected function save($post_data) {
        $user = wp_get_current_user();

        $password_current = $post_data['password_current'];
        $password_new = $post_data['password_new'];
        $password_confirm = $post_data['password_confirm'];

        if (!wp_check_password($password_current, $user->user_pass)) {
            $this->errors = [
                'password_current' => __('Current Password is invalid!', 'sta'),
            ];
            return;
        }

        if ($password_new != $password_confirm) {
            $this->errors = [
                'password_confirm' => __('Password does not match!', 'sta'),
            ];
            return;
        }

        wp_set_password($password_new, $user->ID);
        wp_set_auth_cookie($user->ID);
        wp_set_current_user($user->ID);

        $this->succeed();
    }
}
