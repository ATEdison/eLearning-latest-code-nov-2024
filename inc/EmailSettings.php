<?php

namespace STA\Inc;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use STA\Inc\FormHandlers\FormRegisterHandler;
use STA\Inc\FormHandlers\FormUserInvitationsHandler;
use STA\Inc\FormHandlers\FormUserResetPasswordHandler;

class EmailSettings {

    private static $instance;

    private const TYPE_WELCOME = 'welcome';
    private const TYPE_RESET_PASSWORD = 'reset_password';
    private const TYPE_RESET_PASSWORD_CONFIRM = 'reset_password_confirm';
    private const TYPE_FRIEND_INVITATION = 'friend_invitation';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_print_footer_scripts', [$this, 'sta_test_email_script'], PHP_INT_MAX);
        add_action('wp_ajax_sta_test_email', [$this, 'sta_test_email']);
    }

    public function sta_test_email() {
        if (!is_user_logged_in() || !current_user_can('administrator')) {
            wp_send_json_error(['message' => 'Unauthorized'], 401);
            return;
        }

        $nonce = $_POST['nonce'] ?? '';
        if (!wp_verify_nonce($nonce, 'sta_test_email')) {
            wp_send_json_error(['message' => 'Invalid nonce'], 400);
            return;
        }

        $email = $_POST['email'] ?? '';
        $type = $_POST['type'] ?? '';

        if (!$email || !$type) {
            wp_send_json_error(['message' => 'Invalid email or email type.'], 400);
            return;
        }

        $user = wp_get_current_user();
        $user_id = $user->ID;

        $sent = false;

        switch ($type) {
            case self::TYPE_WELCOME:
                $sent = FormRegisterHandler::send_welcome_email($user_id, $email);
                break;
            case self::TYPE_RESET_PASSWORD:
                /**
                 * @see retrieve_password()
                 */
                $sent = self::send_password_reset_test_email($user, $email);
                break;
            case self::TYPE_RESET_PASSWORD_CONFIRM:
                $sent = FormUserResetPasswordHandler::send_reset_password_confirmation($user, $email);
                break;
            case self::TYPE_FRIEND_INVITATION:
                $sent = FormUserInvitationsHandler::send_invitation_email($user, [$email]);
                break;
        }

        if (!$sent) {
            wp_send_json_error(['message' => 'Fail to send test email.', 'data' => $sent], 500);
            return;
        }
        wp_send_json_success();
    }

    /**
     * @param \WP_User $user
     * @param string $email
     * @return bool|\WP_Error
     * @see retrieve_password()
     * @see FormRegisterHandler::send_welcome_email()
     */
    private static function send_password_reset_test_email($user, $email) {
        $key = get_password_reset_key($user);
        if (is_wp_error($key)) {
            return $key;
        }

        $subject = UserAuth::instance()->retrieve_password_title('', $user->user_login, $user);
        $message = UserAuth::instance()->retrieve_password_message('', $key, $user->user_login, $user);

        $headers = self::mail_headers();

        return wp_mail($email, $subject, $message, $headers);
    }

    public static function mail_headers() {
        return [
            'Content-Type: text/html; charset=UTF-8',
        ];
    }

    public function sta_test_email_script() {
        global $current_screen;
        // printf('<pre style="position: fixed;top:0;left:0;z-index: 99999;background-color: #000;padding: 20px;color: #fff;">%s</pre>', var_export($current_screen, true));
        if ($current_screen->parent_base != 'crb_carbon_fields_container_email_settings') {
            return;
        }
        ?>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    var sending = false;

                    $(document).on('click', '.sta-send-test-email', function () {
                        var $btn = $(this);
                        sendTestEmail($btn)
                    });

                    function sendTestEmail($btn) {
                        if (sending) {
                            return;
                        }
                        sending = true;

                        var $input = $btn.parent().find('input[name="sta_test_email"]');
                        var $message = $btn.next('.sta-test-email-message');
                        $message.html('Sending...');

                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                nonce: '<?php echo wp_create_nonce('sta_test_email'); ?>',
                                action: 'sta_test_email',
                                email: $input.val(),
                                type: $btn.val(),
                            },
                            success: function (response) {
                                // console.log(response);
                                if (response.success) {
                                    $message.html('Test email has been sent to ' + $input.val());
                                }
                            },
                            error: function (xhr) {
                                $message.html('Something wrong happened. <code>' + JSON.stringify(xhr) + '</code>');
                            },
                            complete: function () {
                                sending = false;
                            }
                        });
                    }
                });
            })(jQuery);
        </script>
        <?php
    }

    public static function meta_field($field, $lang = '') {
        if (!$lang) {
            $lang = ThemeSetup::current_lang();
        }
        if ($lang == 'en') {
            return $field;
        }
        return sprintf('%s_%s', $field, $lang);
    }

    public static function should_register_fields_fallback($lang) {
        if ($lang == 'en') {
            return false;
        }

        if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
            return false;
        }

        return true;
    }

    public static function register_fields() {
        $lang = ThemeSetup::current_lang();
        self::register_fields_for_lang($lang);

        // fallback
        if (self::should_register_fields_fallback($lang)) {
            self::register_fields_for_lang('en');
        }
    }

    private static function register_fields_for_lang($lang) {
        Container::make('theme_options', 'Email Settings')
            ->add_tab('General', [
                Field::make('image', self::meta_field('email_header_logo', $lang), 'Header Logo')
                    ->set_help_text('Must be JPG/PNG. Do not use SVG'),
                Field::make('image', self::meta_field('email_footer_logo', $lang), 'Footer Logo')
                    ->set_help_text('Must be JPG/PNG. Do not use SVG'),
                Field::make('rich_text', self::meta_field('email_footer_text', $lang), 'Footer Text'),
            ])
            ->add_tab('Welcome Email', [
                Field::make('text', self::meta_field('email_welcome_heading', $lang), 'Subject')->set_default_value('Welcome Email'),
                self::email_placeholders(self::TYPE_WELCOME),
                self::email_builder(self::meta_field('email_welcome', $lang), ''),
            ])
            ->add_tab('Reset Password Trigger', [
                Field::make('text', self::meta_field('email_reset_password_trigger_heading', $lang), 'Subject')->set_default_value('Reset Password'),
                self::email_placeholders(self::TYPE_RESET_PASSWORD),
                self::email_builder(self::meta_field('email_reset_password_trigger', $lang), ''),
            ])
            ->add_tab('Reset Password Confirmation', [
                Field::make('text', self::meta_field('email_reset_password_confirmation_heading', $lang), 'Subject')->set_default_value('Reset Password Confirmation'),
                self::email_placeholders(self::TYPE_RESET_PASSWORD_CONFIRM),
                self::email_builder(self::meta_field('email_reset_password_confirmation', $lang), ''),
            ])
            ->add_tab('Friend Invitation Email', [
                Field::make('text', self::meta_field('friend_invitation_subject', $lang), 'Subject')->set_default_value('Friend Invitation'),
                self::email_placeholders(self::TYPE_FRIEND_INVITATION),
                self::email_builder(self::meta_field('friend_invitation_body', $lang), ''),
            ]);
    }

    public static function get_meta_value($field, $lang = null, $fallback_to_en = true) {
        $value = carbon_get_theme_option(self::meta_field($field, $lang));
        if (!empty($value) || !$fallback_to_en) {
            return $value;
        }
        return carbon_get_theme_option(self::meta_field($field, 'en'));
    }

    public static function get_friend_invitation_email_settings() {
        $lang = ThemeSetup::current_lang();
        return [
            'subject' => self::get_meta_value('friend_invitation_subject', $lang),
            'body' => self::get_meta_value('friend_invitation_body', $lang),
        ];
    }

    public static function get_footer_settings() {
        $lang = ThemeSetup::current_lang();
        return [
            'logo_id' => self::get_meta_value('email_footer_logo', $lang),
            'text' => self::get_meta_value('email_footer_text', $lang),
        ];
    }

    public static function get_header_logo_image_id() {
        return self::get_meta_value('email_header_logo');
    }

    public static function get_welcome_email() {
        $lang = ThemeSetup::current_lang();
        return [
            'subject' => self::get_meta_value('email_welcome_heading', $lang),
            'body' => self::get_meta_value('email_welcome', $lang),
        ];
    }

    public static function get_email_reset_password_trigger() {
        $lang = ThemeSetup::current_lang();
        return [
            'subject' => self::get_meta_value('email_reset_password_trigger_heading', $lang),
            'body' => self::get_meta_value('email_reset_password_trigger', $lang),
        ];
    }

    public static function get_email_reset_password_confirmation() {
        $lang = ThemeSetup::current_lang();
        return [
            'subject' => self::get_meta_value('email_reset_password_confirmation_heading', $lang),
            'body' => self::get_meta_value('email_reset_password_confirmation', $lang),
        ];
    }

    private static function email_placeholders($type) {
        $placeholders = [
            'first_name', 'last_name', 'name',
        ];
        switch ($type) {
            case self::TYPE_RESET_PASSWORD:
                $placeholders = array_merge($placeholders, [
                    'reset_password_link',
                ]);
                break;
            case self::TYPE_WELCOME:
                $placeholders = array_merge($placeholders, [
                    'email_confirmation_link',
                ]);
        }

        ob_start();
        get_template_part('template-parts/admin/email-settings', '', [
            'type' => $type,
            'placeholders' => $placeholders,
        ]);
        $content = ob_get_clean();

        return Field::make('html', 'email_placeholders_' . $type)
            ->set_html($content);
    }

    private static function email_builder($field, $label) {
        return Field::make('complex', $field, $label)
            ->add_fields('image', [
                Field::make('image', 'image', 'Image')
                    ->set_help_text('Must be JPG/PNG. Do not use SVG'),
            ])
            ->add_fields('text', [
                Field::make('rich_text', 'text', 'Text'),
            ])
            ->add_fields('cta', [
                Field::make('text', 'btn_label', 'Button Label'),
                Field::make('text', 'btn_url', 'Button URL'),
            ])
            ->add_fields('icon_text_list', [
                Field::make('text', 'heading', 'Heading'),
                Field::make('complex', 'items', 'List')
                    ->set_layout('tabbed-vertical')
                    ->add_fields([
                        Field::make('image', 'image', 'Icon')
                            ->set_help_text('Must be JPG/PNG. Do not use SVG'),
                        Field::make('text', 'heading', 'Heading'),
                        Field::make('textarea', 'desc', 'Description'),
                    ])
            ]);
    }

    public static function apply_placeholders($text, $placeholders) {
        foreach ($placeholders as $placeholder => $value) {
            $text = str_replace('[' . $placeholder . ']', $value, $text);
        }
        return $text;
    }

    /**
     * @param \WP_User $user
     * @return array
     */
    public static function user_placeholders($user) {
        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'name' => $user->display_name,
        ];
    }

    public static function p_style($text, $custom_css = '') {
        return preg_replace('@<p>@', sprintf('<p style="margin: 0 0 30px 0;%s">', $custom_css), $text);
    }

    public static function side_padding() {
        printf('<td width="20" style="width: 20px;">&nbsp;</td>');
    }

    public static function vertical_padding($size) {
        ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
            <tbody>
                <tr>
                    <td width="100%" height="<?php echo $size; ?>"></td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    public static function horizontal_line() {
        ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
            <tr>
                <td height="2" style="height:2px; font-size: 0px; line-height: 0px;background-color: #e6e6e6;" bgcolor="#e6e6e6"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/spacer.gif" width="1" height="1" alt="" border="0" style="display:block;"></td>
            </tr>
        </table>
        <?php
    }
}
