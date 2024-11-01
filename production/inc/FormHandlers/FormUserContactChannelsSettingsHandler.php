<?php

namespace STA\Inc\FormHandlers;

class FormUserContactChannelsSettingsHandler extends AbstractFormHandler {

    protected function whitelist_fields() {
        return [
            'contact_channel_email' => [
                'type' => 'checkbox',
                'label' => __('Email', 'sta'),
                'required' => false,
            ],
            'contact_channel_sms' => [
                'type' => 'checkbox',
                'label' => __('Text Messages', 'sta'),
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

        $meta_fields = array_keys($this->whitelist_fields());
        foreach ($meta_fields as $field) {
            carbon_set_user_meta($user_id, $field, $post_data[$field]);
        }
    }
}
