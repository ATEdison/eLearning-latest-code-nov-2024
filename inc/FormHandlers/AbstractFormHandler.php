<?php

namespace STA\Inc\FormHandlers;

abstract class AbstractFormHandler {

    protected $errors = null;
    protected $global_errors = null;
    protected $post_data = null;
    protected $success = null;

    abstract protected function whitelist_fields();

    abstract protected function save($post_data);

    public function proceed() {
        // verify nonce field
        $nonce = $_POST['_wpnonce'] ?? '';
        if (!wp_verify_nonce($nonce, 'sta_form')) {
            return;
        }

        $this->global_errors = [];
        $this->errors = [];

        $whitelist_fields = $this->whitelist_fields();

        $post_data = [];
        $errors = [];
        foreach ($whitelist_fields as $field => $field_settings) {
            $value = $_POST[$field] ?? '';
            // $value = '';
            $value = $this->sanitize_value($field, $value, $field_settings);
            $messages = $field_settings['messages'] ?? [];

            // post data
            $post_data[$field] = $value;

            // validate
            $required = $field_settings['required'] ?? false;
            if ($required && empty($value)) {
                $errors[$field] = $messages['required'] ?? sprintf(__('%s is required', 'sta'), $field_settings['label']);
                continue;
            }

            // unique_email
            $unique_email = $field_settings['unique_email'] ?? false;
            if ($unique_email && $value && email_exists($value)) {
                $errors[$field] = $messages['unique_email'] ?? __('This email is already registered. Please choose another one.', 'sta');
                continue;
            }
        }

        $this->post_data = $post_data;

        if (!empty($errors)) {
            $this->errors = $errors;
            return;
        }

        $this->save($post_data);
    }

    public function is_success() {
        return $this->success;
    }

    protected function succeed() {
        $this->success = true;
        // reset post_data
        $this->post_data = [];
    }

    protected function upload_image($field) {
        // Input type file name
        $image_input_name = $field . '_file';

        // Allowed image types
        $allowed_image_types = array('image/jpeg', 'image/png');

        // Maximum size in bytes
        $max_image_size = 1024 * 1024; // 1 MB (approx)

        // printf('<pre>%s</pre>', var_export($_FILES, true)); die;

        // Check if there's an image
        if (!isset($_FILES[$image_input_name]['size']) || $_FILES[$image_input_name]['size'] <= 0) {
            return null;
        }

        // validate
        if (!in_array($_FILES[$image_input_name]['type'], $allowed_image_types) || $_FILES[$image_input_name]['size'] > $max_image_size) {
            $this->errors = is_array($this->errors) ? $this->errors : [];
            $this->errors[$field] = __('Invalid format or exceeded maximum size!', 'sta');
            return null;
        }

        // You shall pass

        // These files need to be included as dependencies when on the front end.
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Let WordPress handle the upload.
        // Remember, 'my_image_upload' is the name of our file input in our form above.
        $attachment_id = media_handle_upload($image_input_name, 0);

        if (is_wp_error($attachment_id)) {
            $this->errors = is_array($this->errors) ? $this->errors : [];
            $this->errors[$field] = $attachment_id->get_error_message();
            return null;
        }

        return $attachment_id;
    }

    protected function sanitize_value($field, $value, $field_settings) {
        if ($field_settings['type'] == 'email_list') {
            return $this->sanitize_email_list($value);
        }

        return sanitize_text_field($value);
    }

    protected function sanitize_email_list($value) {
        $email_list = explode(',', $value);
        $email_list = array_map('trim', $email_list);
        $email_list = array_filter($email_list);
        $email_list = array_unique($email_list);

        $data = [];
        foreach ($email_list as $email) {
            if (!is_email($email)) {
                continue;
            }
            $data[] = $email;
        }
        return $data;
    }

    public function get_errors() {
        return $this->errors;
    }

    public function get_global_errors() {
        return $this->global_errors;
    }

    public function get_post_data() {
        return is_array($this->post_data) ? $this->post_data : [];
    }

    protected function update_meta_fields($user_id, $meta_fields) {
        $post_data = $this->post_data;
        foreach ($meta_fields as $field) {
            $value = $post_data[$field];
            carbon_set_user_meta($user_id, $field, $value);
        }
    }
}
