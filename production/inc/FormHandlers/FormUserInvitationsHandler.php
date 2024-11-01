<?php

namespace STA\Inc\FormHandlers;

use STA\Inc\EmailSettings;
use STA\Inc\PointLogs;

class FormUserInvitationsHandler extends AbstractFormHandler {

    protected function whitelist_fields() {
        return [
            'email_addresses' => [
                'type' => 'email_list',
                'label' => __('Email Addresses', 'sta'),
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
        // $this->global_errors[] = 'test';
        // return;

        $user = wp_get_current_user();
        $user_id = $user->ID;

        $email_list = $post_data['email_addresses'];

        // send invitations
        self::send_invitation_email($user, $email_list);

        // add points to user
        PointLogs::refer_a_colleague($user_id, $email_list, 0);

        // send invitations
        $this->succeed();
    }

    public static function send_invitation_email($user, $email_list) {
        $data = EmailSettings::get_friend_invitation_email_settings();

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

        $sent = false;
        foreach ($email_list as $email) {
            $sent |= wp_mail($email, $subject, $message, $headers);
        }

        return $sent;
    }
}
