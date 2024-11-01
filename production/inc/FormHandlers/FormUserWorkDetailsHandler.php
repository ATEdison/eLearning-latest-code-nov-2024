<?php

namespace STA\Inc\FormHandlers;

class FormUserWorkDetailsHandler extends AbstractFormHandler {

    protected function whitelist_fields() {
        return [
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

        $this->succeed();
    }
}
