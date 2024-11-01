<?php


namespace STA\Inc;

use STA\Inc\FormHandlers\AbstractFormHandler;
use STA\Inc\FormHandlers\FormPersonalHandler;
use STA\Inc\FormHandlers\FormRegisterHandler;
use STA\Inc\FormHandlers\FormUserContactChannelsSettingsHandler;
use STA\Inc\FormHandlers\FormUserInvitationsHandler;
use STA\Inc\FormHandlers\FormUserLanguageSettingsHandler;
use STA\Inc\FormHandlers\FormUserNewslettersSettingsHandler;
use STA\Inc\FormHandlers\FormUserPasswordHandler;
use STA\Inc\FormHandlers\FormUserResetPasswordHandler;
use STA\Inc\FormHandlers\FormUserWorkDetailsHandler;

class FormHandleInvoker {

    private static $instance;
    private static $form_list;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
        add_action('template_redirect', [$this, 'handle_form_submit']);
    }

    private function init() {
        self::$form_list = [
            'personal' => new FormPersonalHandler(),
            'user_work_details' => new FormUserWorkDetailsHandler(),
            'user_contact_channels' => new FormUserContactChannelsSettingsHandler(),
            'user_newsletters_settings' => new FormUserNewslettersSettingsHandler(),
            'user_password' => new FormUserPasswordHandler(),
            'user_invitations' => new FormUserInvitationsHandler(),
            'user_language_settings' => new FormUserLanguageSettingsHandler(),
            'user_register' => new FormRegisterHandler(),
            'user_reset_password' => new FormUserResetPasswordHandler(),
        ];
    }

    public function handle_form_submit() {
        $form = $_POST['sta_form'] ?? '';
        if (!$form) {
            return;
        }

        $form_instance = self::$form_list[$form] ?? null;
        if ($form_instance instanceof AbstractFormHandler) {
            $form_instance->proceed();
        }
    }

    /**
     * @return FormPersonalHandler
     */
    public static function user_reset_password_form_handler() {
        return self::$form_list['user_reset_password'];
    }

    /**
     * @return FormPersonalHandler
     */
    public static function user_language_settings_form_handler() {
        return self::$form_list['user_language_settings'];
    }

    /**
     * @return FormRegisterHandler
     */
    public static function user_register_form_handler() {
        return self::$form_list['user_register'];
    }

    /**
     * @return FormPersonalHandler
     */
    public static function personal_form_handler() {
        return self::$form_list['personal'];
    }

    /**
     * @return FormPersonalHandler
     */
    public static function user_work_details_form_handler() {
        return self::$form_list['user_work_details'];
    }

    /**
     * @return FormPersonalHandler
     */
    public static function user_password_form_handler() {
        return self::$form_list['user_password'];
    }

    /**
     * @return FormPersonalHandler
     */
    public static function user_invitations_form_handler() {
        return self::$form_list['user_invitations'];
    }

    public static function is_personal_form_active() {
        return self::is_form_active('personal');
    }

    public static function is_user_work_details_form_active() {
        return self::is_form_active('user_work_details');
    }

    private static function is_form_active($form) {
        $form_handler = self::$form_list[$form] ?? null;
        if (!($form_handler instanceof AbstractFormHandler)) {
            return false;
        }

        $errors = $form_handler->get_errors();
        if (is_array($errors) && !empty($errors)) {
            return true;
        }

        $global_errors = $form_handler->get_global_errors();
        if (is_array($global_errors) && !empty($global_errors)) {
            return true;
        }

        return false;
    }
}
