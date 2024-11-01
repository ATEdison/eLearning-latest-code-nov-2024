<?php

namespace STA\Inc\FormHandlers;

class FormPersonalHandler extends AbstractFormHandler {

    use TraitProfileImageFormHandler;

    protected function whitelist_fields() {
        return [
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
            'profile_image' => [
                'type' => 'image',
                'label' => __('Profile Image', 'sta'),
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

        $user_data = [
            'ID' => $user_id,
            'first_name' => $post_data['first_name'],
            'last_name' => $post_data['last_name'],
            'display_name' => sprintf('%s %s', $post_data['first_name'], $post_data['last_name']),
        ];

        $result = wp_update_user($user_data);
        if (is_wp_error($result)) {
            $this->global_errors = $result->get_error_messages();
        }

        $meta_fields = ['country', 'primary_contact_number', 'mobile_number'];
        foreach ($meta_fields as $field) {
            carbon_set_user_meta($user_id, $field, $post_data[$field]);
        }

        // profile image
        $this->upload_profile_image($user_id);

        $this->succeed();
    }
}
