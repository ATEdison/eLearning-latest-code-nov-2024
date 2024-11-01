<?php

namespace STA\Inc;

use Dompdf\Exception;
use STA\Inc\CarbonFields\ThemeOptions;

class UserAuth {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // redirect wp-login.php to login
        add_action('init', [$this, 'redirect_wp_login'], 1);
        add_action('init', [$this, 'handle_actions']);
        add_action('init', [$this, 'handle_oauth_actions']);

        // reset password
        add_action('login_form_lostpassword', [$this, 'redirect_to_custom_lostpassword']);
        add_filter('retrieve_password_title', [$this, 'retrieve_password_title'], PHP_INT_MAX, 4);
        add_filter('retrieve_password_message', [$this, 'retrieve_password_message'], PHP_INT_MAX, 4);

        // register
        add_filter('user_registration_email', [$this, 'user_registration_email'], PHP_INT_MAX);
        add_filter('wp_pre_insert_user_data', [$this, 'wp_pre_insert_user_data'], PHP_INT_MAX, 4);
        add_action('user_register', [$this, 'user_register']);
        add_filter('wp_new_user_notification_email', [$this, 'wp_new_user_notification_email'], PHP_INT_MAX, 3);
        add_filter('registration_redirect', [$this, 'registration_redirect'], PHP_INT_MAX, 2);

        // login
        add_filter('authenticate', [$this, 'authenticate'], PHP_INT_MAX, 3);

        /**
         * must be called before learndash_login_failed()
         * @see learndash_login_failed()
         */
        add_action('wp_login_failed', [$this, 'wp_login_failed'], 0, 2);

        add_action('admin_init', [$this, 'redirect_user_to_homepage'], 1);
        add_filter('show_admin_bar', [$this, 'show_admin_bar']);

        // on logout
        add_action('wp_logout', [$this, 'on_logout']);
    }

    public function on_logout($user_id) {
        if (!session_id()) {
            session_start();
        }

        $token = $_SESSION['nn_oauth_token'] ?? null;
        if (!is_array($token) || empty($token)) {
            return;
        }

        $this->oauth_logout();
    }

    private function oauth_logout() {
        $token = $_SESSION['nn_oauth_token'] ?? null;
        $access_token = $token['access_token'] ?? null;
        if (!$access_token) {
            return;
        }

        // https://SSID_DOMAIN/partner/api/v2/oauth/jwt_logout
        $oauth_settings = ThemeOptions::get_oauth_settings();
        $endpoint = sprintf('%s/api/v2/oauth/jwt_logout', untrailingslashit($oauth_settings['base_url']));

        $ssid_method = ThemeOptions::get_ssid_method();
        if($ssid_method == 1){
            $oauth_settings = ThemeOptions::get_key_clock_settings();
            $endpoint = sprintf('%s/realms/ssid-realm/protocol/openid-connect/logout', untrailingslashit($oauth_settings['base_url']));
        }

        $endpoint = add_query_arg([
            'client_id' => $oauth_settings['client_id'],
            // 'client_secret' => $oauth_settings['client_secret'],
            'logout_url' => home_url(),
            'state' => $_SESSION['nn_oauth_state'],
            'access_token' => $access_token,
        ], $endpoint);
        $response = wp_remote_get($endpoint);


        // if (is_wp_error($response) || $response['response']['code'] != 200) {
        //     printf('<pre>%s</pre>', var_export([
        //         '$request' => $endpoint,
        //         'response' => $response['response'],
        //         'body' => json_decode($response['body'], true),
        //     ], true));
        //     die;
        // }
        // printf('<pre>%s</pre>', var_export([
        //     '$request' => $endpoint,
        //     'response' => json_decode($response['body'], true),
        // ], true));
        // die;

        // https://SSID_DOMAIN/partner/api/v2/oauth/logout?client_id=YOUR_CLIENT_ID&logout_url=https://YOUR_APP/logout&state=shauweyyrfucjkznxcjhdsfh
        // $oauth_settings = ThemeOptions::get_oauth_settings();
        // $endpoint = sprintf('%s/api/v2/oauth/logout', $oauth_settings['base_url']);
        // $redirect_url = add_query_arg([
        //     'client_id' => $oauth_settings['client_id'],
        //     'logout_url' => home_url(),
        //     'state' => $_SESSION['nn_oauth_state'],
        //     'access_token' => $access_token,
        // ], $endpoint);
        // wp_redirect($redirect_url);
        // exit();
    }

    public function handle_oauth_actions() {
        $request_uri = $_SERVER['REQUEST_URI'] ?? null;

        if ($request_uri == '/login') {
            $this->oauth_login_request();
            return;
        }

        if (preg_match('@^/oauth/?\?@', $request_uri, $matches)) {
            $this->oauth_login();
            return;
        }
    }

    private function oauth_login() {
        if (!session_id()) {
            session_start();
        }

        $success = $this->oauth_get_token();
        // invalid request
        if (!$success) {
            return;
        }

        $oauth_details = $this->oauth_get_user_details();
        // printf('<pre>%s</pre>', var_export($user_oauth_details, true));

        $user_id = $this->get_user_id_by_oauth_details($oauth_details);
        if (!$user_id) {
            return;
        }
        // $user = get_user_by('ID', $user_id);
        self::update_oauth_user_details($user_id, $oauth_details);

        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        // do_action('wp_login', $user->user_login, $user);
        $redirect_url = get_permalink(ThemeOptions::get_user_homepage_id());
        wp_safe_redirect($redirect_url);
        exit();
    }

    /**
     * @return int|null
     */
    private function get_user_id_by_oauth_details($oauth_details) {
        $oauth_sid = $oauth_details['sid'] ?? null;

        $ssid_method = ThemeOptions::get_ssid_method();
        if($ssid_method == 1){
            $oauth_sid = $oauth_details['ssid'] ?? null;
        }

        if (!$oauth_sid) {
            return null;
        }

        $user_list = get_users(array(
            'meta_key' => '_nn_oauth_sid',
            'meta_value' => $oauth_details['sid'],
        ));
        $user = $user_list[0] ?? null;

        // existing
        if ($user instanceof \WP_User) {
            return $user->ID;
        }

        // check if email exists
        $oauth_email = $oauth_details['email'] ?? null;
        $user = get_user_by('email', $oauth_email);
        if ($user instanceof \WP_User) {
            return $user->ID;
        }

        $first_name = $oauth_details['firstName'] ?? '';
        $last_name = $oauth_details['lastName'] ?? '';

        // create new
        $user_id = wp_insert_user([
            'user_login' => $oauth_email,
            'user_email' => $oauth_email,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);
        if (is_wp_error($user_id)) {
            return null;
        }
        return $user_id;
    }

    private static function update_oauth_user_details($user_id, $oauth_details) {
        // printf('<pre>%s</pre>', var_export($oauth_details, true)); die;
        self::activate_user($user_id);

        $first_name = $oauth_details['firstName'] ?? '';
        $last_name = $oauth_details['lastName'] ?? '';
        $picture_url = $oauth_details['pictureUrl'] ?? '';
        $country_code = $oauth_details['countryCode'] ?? '';

        wp_update_user([
            'ID' => $user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);

        update_user_meta($user_id, '_nn_oauth_profile_image_url', $picture_url);
        update_user_meta($user_id, '_nn_oauth_country_code', $country_code);
    }

    /**
     * @return array|null
     */
    private function oauth_get_user_details() {
        if (!session_id()) {
            session_start();
        }

        $oauth_settings = ThemeOptions::get_oauth_settings();
        $endpoint = sprintf('%s/api/v2/userinfo', untrailingslashit($oauth_settings['base_url']));

        $ssid_method = ThemeOptions::get_ssid_method();

        if($ssid_method == 1){
            $oauth_settings = ThemeOptions::get_key_clock_settings();
            $endpoint = sprintf('%s/realms/ssid-realm/protocol/openid-connect/userinfo', untrailingslashit($oauth_settings['base_url']));
        }

        $token = isset($_SESSION['nn_oauth_token']) && isset($_SESSION['nn_oauth_token']['access_token']) ? $_SESSION['nn_oauth_token']['access_token'] : null;
        if (!$token) {
            return null;
        }

        $response = wp_remote_get($endpoint, [
            'headers' => [
                'Authorization' => "Bearer $token",
            ],
        ]);

        if (is_wp_error($response)) {
            return null;
        }

        if (!is_array($response) || !isset($response['response']) || !isset($response['response']['code']) || $response['response']['code'] != 200) {
            return null;
        }

        return json_decode($response['body'], true);
    }

    /**
     * @return bool
     */
    private function oauth_get_token() {
        if (!session_id()) {
            session_start();
        }

        $code = $_GET['code'] ?? null;
        $state = $_GET['state'] ?? null;
        if (!$code || !$state || $state != $_SESSION['nn_oauth_state']) {
            return false;
        }

        $oauth_settings = ThemeOptions::get_oauth_settings();
        $endpoint = sprintf('%s/api/v2/oauth/token', untrailingslashit($oauth_settings['base_url']));

        $response = $this->oauth_post_request($endpoint, [
            'client_id' => $oauth_settings['client_id'],
            'client_secret' => $oauth_settings['client_secret'],
            'code' => $code,
            'grant_type' => 'authorization_code',
            'codeVerify' => $_SESSION['nn_oauth_code_challenge'],
        ]);

        $ssid_method = ThemeOptions::get_ssid_method();

        if($ssid_method == 1){
            $auth_settings = ThemeOptions::get_key_clock_settings();
            $endpoint = sprintf('%s/realms/ssid-realm/protocol/openid-connect/token', untrailingslashit($auth_settings['base_url']));  

            $data = array(
                'client_id' => $auth_settings['client_id'],
                'client_secret' => $auth_settings['client_secret'],
                'code' => $code,
                'grant_type' => 'authorization_code',
                'code_verifier' => $_SESSION['nn_oauth_code_verifier'] ,
                'redirect_uri' => site_url().'/oauth',
                'state' => $state,
            );

            $header = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            );

            $args = array(
                'body' => $data,
                'headers' => $header,
            );         
    
            $response = wp_remote_post( $endpoint, $args );
        }  

        if (is_wp_error($response)) {
            return false;
        }

        if (!is_array($response) || !isset($response['response']) || !isset($response['response']['code']) || $response['response']['code'] != 200) {
            return false;
        }

        $body = json_decode($response['body'], true);
        $token = $body['access_token'] ?? null;
        if (!$token) {
            return false;
        }

        $this->oauth_update_token($body);
        return true;
    }

    private function oauth_update_token($data) {
        $_SESSION['nn_oauth_token'] = array_merge($data, [
            'expiry_date' => date('Y-m-d H:i:s', $data['expires_in'] + time()),
        ]);
    }

    private function oauth_post_request($endpoint, $post_fields) {
        $boundary = wp_generate_password(24);
        $headers = array(
            'content-type' => 'multipart/form-data; boundary=' . $boundary,
        );
        $payload = '';

        // First, add the standard POST fields:
        foreach ($post_fields as $name => $value) {
            $payload .= '--' . $boundary;
            $payload .= "\r\n";
            $payload .= 'Content-Disposition: form-data; name="' . $name .
                '"' . "\r\n\r\n";
            $payload .= $value;
            $payload .= "\r\n";
        }

        // Upload the file
        // if ($local_file) {
        //     $payload .= '--' . $boundary;
        //     $payload .= "\r\n";
        //     $payload .= 'Content-Disposition: form-data; name="' . 'upload' .
        //         '"; filename="' . basename($local_file) . '"' . "\r\n";
        //     //        $payload .= 'Content-Type: image/jpeg' . "\r\n";
        //     $payload .= "\r\n";
        //     $payload .= file_get_contents($local_file);
        //     $payload .= "\r\n";
        // }
        $payload .= '--' . $boundary . '--';

        return wp_remote_post($endpoint, [
            'headers' => $headers,
            'body' => $payload,
        ]);
    }

    // Function to encode bytes to base64url
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function oauth_login_request() {
        session_start();
        $state = wp_generate_password(32, false);
        $code_challenge = wp_generate_password(43, false);
        $scope = 'profile';

        $oauth_settings = ThemeOptions::get_oauth_settings();
        $endpoint = ThemeOptions::get_oauth_login_url();

        $ssid_method = ThemeOptions::get_ssid_method();

        if($ssid_method == 1){
            // Generate a random code_verifier
            $codeVerifier = $this->base64UrlEncode(random_bytes(32)); // 256 bits
            $state = $this->base64UrlEncode(random_bytes(32)); // 256 bits

            // // Create a code_challenge based on the code_verifier
            $codeVerifierBytesHashed = hash('sha256', $codeVerifier, true);

            $code_challenge = $this->base64UrlEncode($codeVerifierBytesHashed);

            $_SESSION['nn_oauth_code_verifier'] = $codeVerifier;

            $oauth_settings = ThemeOptions::get_key_clock_settings();

            $endpoint = ThemeOptions::get_key_clock_login_url();

            $scope = 'openid profile email';
            
        }

        $_SESSION['nn_oauth_state'] = $state;
        $_SESSION['nn_oauth_code_challenge'] = $code_challenge;

        // https://SSID_DOMAIN/partner/process/STA/Login
        // ?response_type=code
        // &redirect_uri=https://YOUR_APP/callback
        // &state=132839c78a9499383a499b34b82766dc
        // &client_id=YOUR_CLIENT_ID
        // &scope=profile
        // &code_challenge_method=S256
        // &code_challenge=Wc4wDiWfHAvu6jxCaOf9wG4Y3WXUl4_kNCVr76jWk8A
        $login_url = add_query_arg([
            'response_type' => 'code',
            'redirect_uri' => home_url('/oauth'),
            'state' => $state,
            'client_id' => $oauth_settings['client_id'],
            'scope' => $scope,
            'code_challenge_method' => 'S256',
            'code_challenge' => $code_challenge,
        ], $endpoint);

        wp_redirect($login_url);
        exit();
    }

    public function show_admin_bar() {
        if (self::current_user_can_access_admin()) {
            return true;
        }
        return false;
    }

    private static function current_user_can_access_admin() {
        if (!is_user_logged_in()) {
            return false;
        }

        if (wp_doing_ajax()) {
            return true;
        }

        if (current_user_can('manage_options')) {
            return true;
        }

        if (current_user_can('edit_posts')) {
            return true;
        }

        return false;
    }

    public function redirect_user_to_homepage() {
        if (self::current_user_can_access_admin()) {
            return;
        }
        $redirect = home_url();
        wp_safe_redirect($redirect);
        exit();
    }

    /**
     * set reset password mail content type to text/html
     * @param string $content_type
     * @return string
     */
    public function reset_password_wp_mail_content_type($content_type) {
        return 'text/html';
    }

    /**
     * @param string $title Email subject.
     * @param string $user_login The username for the user.
     * @param \WP_User $user_data WP_User object.
     * @return string
     * @see retrieve_password()
     */
    public function retrieve_password_title($title, $user_login, $user) {
        $data = EmailSettings::get_email_reset_password_trigger();
        return $data['subject'];
    }

    /**
     * @param string $message
     * @param string $key
     * @param string $user_login
     * @param \WP_User $user
     * @return string
     * @see retrieve_password()
     */
    public function retrieve_password_message($message, $key, $user_login, $user) {
        add_filter('wp_mail_content_type', [$this, 'reset_password_wp_mail_content_type'], PHP_INT_MAX);

        $placeholders = EmailSettings::user_placeholders($user);

        $reset_password_page_url = get_permalink(\STA\Inc\CarbonFields\ThemeOptions::get_reset_password_page_id());
        $reset_password_url = add_query_arg([
            'login' => $user->user_login,
            'key' => $key,
        ], $reset_password_page_url);
        $placeholders['reset_password_link'] = $reset_password_url;

        $data = EmailSettings::get_email_reset_password_trigger();
        $body = $data['body'];

        ob_start();
        get_template_part('template-parts/email/email-builder', '', [
            'builder' => $body,
            'placeholders' => $placeholders,
        ]);
        return ob_get_clean();
    }

    /**
     * @param string $registration_redirect
     * @param \WP_Error|int $errors
     */
    public function registration_redirect($registration_redirect, $errors) {
        // var_dump($registration_redirect); die;


        return $registration_redirect;
    }

    public function redirect_wp_login() {
        global $pagenow;
        // var_dump($pagenow); die;
        if ($pagenow != 'wp-login.php') {
            return;
        }

        $is_post_request = $_SERVER['REQUEST_METHOD'] === 'POST';
        if ($is_post_request) {
            return;
        }

        $action = $_GET['action'] ?? '';
        $whitelist_actions = ['logout', 'switch_to_user', 'switch_to_olduser'];
        if (!$action || !in_array($action, $whitelist_actions)) {
            $login_page_url = get_permalink(ThemeOptions::get_login_page_id());
            // var_dump($login_page_url); die;
            wp_safe_redirect($login_page_url);
            die;
        }
    }

    /**
     * @param string $username
     * @param \WP_Error $error
     * @see learndash_login_failed()
     */
    public function wp_login_failed($username, $error) {
        $redirect_to = get_permalink(ThemeOptions::get_login_page_id());
        $error_code = $error->get_error_code();
        $whitelist_codes = ['sta_unverified'];
        $error_code = in_array($error_code, $whitelist_codes) ? $error_code : 'failed';
        $redirect_to = add_query_arg(['login' => $error_code], $redirect_to);
        wp_safe_redirect($redirect_to);
        die;
    }

    /**
     * @param \WP_User|null|\WP_Error $user
     * @param string $username
     * @param string $password
     * @return \WP_User|null|\WP_Error
     * @see wp_authenticate()
     */
    public function authenticate($user, $username, $password) {
        if (!($user instanceof \WP_User)) {
            return $user;
        }

        $user_id = $user->ID;

        // administrator does not need to be verified
        if (user_can($user, 'administrator')) {
            return $user;
        }

        if (!self::is_user_activated($user_id)) {
            return new \WP_Error('sta_unverified', Translator::user_unverified());
        }

        return $user;
    }

    public function handle_actions() {
        $action = $_GET['sta_action'] ?? '';
        if (!$action) {
            return;
        }

        switch ($action) {
            case 'verify_user':
                self::verify_user();
                break;
        }
    }

    private static function verify_user() {
        $user_id = $_GET['user_id'] ?? 0;
        $user_id = is_numeric($user_id) ? intval($user_id) : 0;
        $code = sanitize_text_field($_GET['code'] ?? '');

        if (!$user_id || !$code) {
            return;
        }

        if (self::is_user_activated($user_id)) {
            return;
        }

        $user_code = self::get_user_verification_code($user_id);

        // invalid code
        if ($user_code != $code) {
            return;
        }

        self::activate_user($user_id);

        $register_page_url = get_permalink(ThemeOptions::get_register_page_id());
        wp_redirect(esc_url(add_query_arg(['register' => 'verified'], $register_page_url)));
        die;
    }

    private static function activate_user($user_id) {
        update_user_meta($user_id, '_sta_user_activated', true);
    }

    /**
     * @param array $data
     * @param \WP_User $user
     * @param string $blogname
     * @return array
     * @see wp_send_new_user_notifications()
     */
    public function wp_new_user_notification_email($data, $user, $blogname) {
        $user_id = $user->ID;

        // update user details before sending welcome email
        FormHandleInvoker::user_register_form_handler()->update_user_on_register_success($user_id);

        $settings = EmailSettings::get_welcome_email();

        $code = \STA\Inc\UserAuth::get_user_verification_code($user_id);
        $register_page_id = \STA\Inc\CarbonFields\ThemeOptions::get_register_page_id();
        $verification_url = add_query_arg(['sta_action' => 'verify_user', 'user_id' => $user_id, 'code' => $code], get_permalink($register_page_id));

        $placeholders = array_merge(EmailSettings::user_placeholders($user), [
            'email_confirmation_link' => $verification_url,
        ]);

        // var_dump($placeholders); die;

        ob_start();
        get_template_part('template-parts/email/email-builder', '', [
            'placeholders' => $placeholders,
            'builder' => $settings['body'],
        ]);
        $data['message'] = ob_get_clean();

        $data['subject'] = $settings['subject'];
        $data['headers'] = EmailSettings::mail_headers();

        return $data;
    }

    /**
     * @param int $user_id
     */
    public function user_register($user_id) {
        $code = wp_generate_password(64, false);
        update_user_meta($user_id, '_sta_verification_code', $code);
    }

    public static function get_user_verification_code($user_id) {
        return get_user_meta($user_id, '_sta_verification_code', true);
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public static function is_user_activated($user_id) {
        return boolval(get_user_meta($user_id, '_sta_user_activated', true));
    }

    /**
     * @param array $data
     * @param bool $update
     * @param int|null $user_id
     * @param array $userdata
     * @see wp_insert_user()
     */
    public function wp_pre_insert_user_data($data, $update, $user_id, $userdata) {
        $password = sanitize_text_field($_POST['password'] ?? '');
        if ($password) {
            $data['user_pass'] = wp_hash_password($password);
        }
        return $data;
    }

    /**
     * @param string $email
     * @return string
     * @see register_new_user()
     */
    public function user_registration_email($email) {
        $email = wp_unslash($_POST['user_login'] ?? '');
        $_POST['user_email'] = $email;
        return $email;
    }

    /**
     * Redirects the user to the custom "Forgot your password?" page instead of
     * wp-login.php?action=lostpassword.
     */
    public function redirect_to_custom_lostpassword() {
        // get request
        if ('GET' == $_SERVER['REQUEST_METHOD']) {
            // if (is_user_logged_in()) {
            //     $this->redirect_logged_in_user();
            //     exit;
            // }

            wp_redirect(get_permalink(ThemeOptions::get_reset_password_page_id()));
            exit;
        }

        // post request
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $errors = retrieve_password();
            // if (is_wp_error($errors)) {
            //     // Errors found
            //     $redirect_url = get_permalink(ThemeOptions::get_reset_password_page_id());
            //     $redirect_url = add_query_arg(['error' => $errors->get_error_code()], $redirect_url);
            // } else {
            //     // Email sent
            //     $redirect_url = get_permalink(ThemeOptions::get_reset_password_page_id());
            //     $redirect_url = add_query_arg(['checkemail' => 'confirm'], $redirect_url);
            // }
            $redirect_url = get_permalink(ThemeOptions::get_reset_password_page_id());
            $redirect_url = add_query_arg(['confirm' => 'yes'], $redirect_url);

            wp_redirect($redirect_url);
            exit;
        }
    }
}
