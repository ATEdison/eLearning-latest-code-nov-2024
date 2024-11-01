<?php

namespace STA\Inc\FormHandlers;

use STA\Inc\PointLogs;

class FormUserNewslettersSettingsHandler extends AbstractFormHandler {

    protected function whitelist_fields() {
        return [
            'newsletter_saudi_expert' => [
                'type' => 'checkbox',
                'label' => __('Saudi Expert newsletter', 'sta'),
                'required' => false,
            ],
            'newsletter_news_events' => [
                'type' => 'checkbox',
                'label' => __('News & Events newsletter', 'sta'),
                'required' => false,
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
        $user_id = get_current_user_id();

        $is_subscribed = false;

        $meta_fields = array_keys($this->whitelist_fields());
        foreach ($meta_fields as $field) {
            $is_subscribed |= !!$post_data[$field];
            carbon_set_user_meta($user_id, $field, $post_data[$field]);
        }

        if ($is_subscribed) {
            PointLogs::subscribe_to_enewsletter($user_id, 50);
        }
    }

    public static function subscribe($user_id) {
        carbon_set_user_meta($user_id, 'newsletter_saudi_expert', 'yes');
        carbon_set_user_meta($user_id, 'newsletter_news_events', 'yes');
        PointLogs::subscribe_to_enewsletter($user_id, 50);
    }
}
