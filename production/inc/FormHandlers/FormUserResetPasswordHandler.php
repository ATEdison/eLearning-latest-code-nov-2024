<?php

namespace STA\Inc\FormHandlers;

use STA\Inc\EmailSettings;

class FormUserResetPasswordHandler extends AbstractFormHandler {

    protected function whitelist_fields() {
        return [
            'password' => [
                'type' => 'text',
                'label' => __('Password', 'sta'),
                'required' => true,
            ],
        ];
    }

    protected function save($post_data) {
        $login = $_GET['login'] ?? '';
        $key = $_GET['key'] ?? '';

        $user = check_password_reset_key($key, $login);

        if (is_wp_error($user)) {
            $this->global_errors[] = __('Invalid reset password key!', 'sta');
            return;
        }

        // $user = get_user_by('login', $login);
        if (wp_check_password($post_data['password'], $user->user_pass)) {
            $this->global_errors[] = __('New password must be different from the current password!', 'sta');
            return;
        }

        reset_password($user, $post_data['password']);

        // log user in
        wp_set_auth_cookie($user->ID);
        wp_set_current_user($user->ID);

        self::send_reset_password_confirmation($user);

        wp_safe_redirect(home_url());
        die;
    }

    public static function send_reset_password_confirmation($user, $to = null) {
        $data = EmailSettings::get_email_reset_password_confirmation();

        $subject = $data['subject'];
        $body = $data['body'];

        ob_start();
        get_template_part('template-parts/email/email-builder', '', [
            'placeholders' => EmailSettings::user_placeholders($user),
            'builder' => $body,
        ]);
        $message = ob_get_clean();
        // echo $message;

        $headers = EmailSettings::mail_headers();

        $to = $to ?: $user->user_email;

        return wp_mail($to, $subject, $message, $headers);
    }
}
