<?php

namespace STA\Inc\FormHandlers;

use STA\Inc\Helpers;

class FormUserLanguageSettingsHandler extends AbstractFormHandler {

    protected function whitelist_fields() {
        return [
            'preferred_language' => [
                'type' => 'select',
                'label' => __('Preferred Language', 'sta'),
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

        $locale = $post_data['preferred_language'];

        /**
         * @var \SitePress $sitepress
         */
        global $sitepress;
        // $sitepress->set
        update_user_meta($user_id, 'locale', $locale);
        do_action('wpml_switch_language', $locale);

        // Helpers::log([$sitepress->get_ls_languages(), $post_data]); die;
        $page_available_languages = $sitepress->get_ls_languages();
        foreach ($page_available_languages as $item) {
            if ($item['default_locale'] == $locale) {
                $redirect = preg_replace('/\?.*/', '', $item['url']);
                $redirect = sprintf('%s/general-preferences', untrailingslashit($redirect));
                wp_safe_redirect($redirect);
                exit();
            }
        }

        $this->succeed();
    }
}
