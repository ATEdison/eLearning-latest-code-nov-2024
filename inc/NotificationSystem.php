<?php

namespace STA\Inc;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class NotificationSystem {

    private static $instance;
    private static $table_notifications = 'sta_notifications';
    private static $table_user_notifications = 'sta_user_notifications';
    private const STATUS_USER_NOTIFICATION_NEW = 'new';
    private const STATUS_USER_NOTIFICATION_READ = 'read';

    private const OPTION_USER_NOTIFICATION_COUNT = '_sta_user_notification_count';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('carbon_fields_should_save_field_value', [$this, 'carbon_fields_should_save_field_value'], PHP_INT_MAX, 3);
        add_action('carbon_fields_theme_options_container_saved', [$this, 'on_saved_theme_options']);

        // mark ad read
        add_action('wp_ajax_sta_notification_mark_as_read', [$this, 'sta_notification_mark_as_read']);

        // admin action
        add_action('admin_init', [$this, 'handle_admin_actions']);
    }

    public function handle_admin_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['sta_notification_delete'])) {
            self::admin_delete_notification();
            return;
        }
    }

    private static function admin_delete_notification() {
        $nonce = $_GET['nonce'] ?? '';
        if (!wp_verify_nonce($nonce, 'sta_delete_notification')) {
            return;
        }

        $notification_id = $_GET['sta_notification_delete'] ?? 0;
        if (!$notification_id) {
            return;
        }

        global $wpdb;
        $wpdb->delete(self::$table_notifications, ['id' => $notification_id], ['%d']);
        wp_safe_redirect(remove_query_arg(['sta_notification_delete', 'nonce']));
        exit;
    }

    public static function get_user_new_notification_count($user_id, $refresh = false) {
        $data = get_option(self::OPTION_USER_NOTIFICATION_COUNT);
        if (!$refresh) {
            if (is_array($data) && !empty($data) && isset($data[$user_id])) {
                return $data[$user_id];
            }
        }

        $data = is_array($data) ? $data : [];
        $data[$user_id] = self::count_user_new_notifications($user_id);
        update_option(self::OPTION_USER_NOTIFICATION_COUNT, $user_id, $data);
        return $data[$user_id];
    }

    private static function count_user_new_notifications($user_id) {
        $user = get_user_by('ID', $user_id);
        if (!($user instanceof \WP_User)) {
            return 0;
        }

        global $wpdb;
        $tbl_notifications = self::$table_notifications;
        $tbl_user_notifications = self::$table_user_notifications;

        $query = "SELECT COUNT(DISTINCT tbl_notifications.id) FROM {$tbl_notifications} AS tbl_notifications";
        $query .= $wpdb->prepare(" LEFT JOIN {$tbl_user_notifications} AS tbl_user_notifications ON (tbl_user_notifications.notification_id = tbl_notifications.id AND tbl_user_notifications.user_id = %d)", $user_id);
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND (tbl_notifications.updated_at >= %s)", date('Y-m-d H:i:s', strtotime($user->user_registered)));
        $query .= " AND (";
        // query notifications to the user and they have not read it yet
        $query .= $wpdb->prepare(" (tbl_notifications.global = 0 AND tbl_user_notifications.status = %s)", self::STATUS_USER_NOTIFICATION_NEW);
        // query global notifications and the user has not read it yet
        $query .= $wpdb->prepare(" OR (tbl_notifications.global = 1 AND (tbl_user_notifications.id IS NULL OR tbl_user_notifications.status = 'new'))");
        $query .= ")"; // close AND
        $query .= ")"; // close WHERE

        $count = $wpdb->get_var($query);
        // Helpers::log([
        //     '$query' => $query,
        //     '$count' => $count,
        // ]);
        return is_numeric($count) ? intval($count) : 0;
    }

    public function sta_notification_mark_as_read() {
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        if (!$user_id) {
            wp_send_json_error([], 401);
            return;
        }
        $notification_id = $_POST['notification_id'] ?? 0;
        if (!$notification_id) {
            wp_send_json_error([], 400);
            return;
        }

        self::update_user_notification_status($user_id, $notification_id, self::STATUS_USER_NOTIFICATION_READ);

        // invalid user notification count whenever a notification is marked as read
        self::invalid_user_notification_count([$user_id]);

        wp_send_json_success(['count' => self::count_user_new_notifications($user_id)]);
    }

    private static function invalid_user_notification_count($user_list) {
        $data = get_option(self::OPTION_USER_NOTIFICATION_COUNT);
        if (!is_array($data) || empty($data)) {
            return;
        }
        foreach ($user_list as $user_id) {
            if (!isset($data[$user_id])) {
                continue;
            }
            // invalid user notification count
            unset($data[$user_id]);
        }
        update_option(self::OPTION_USER_NOTIFICATION_COUNT, $data);
    }

    private static function update_user_notification_status($user_id, $notification_id, $status) {
        global $wpdb;
        $row_id = self::db_get_user_notification($user_id, $notification_id);

        // not existing -> insert new
        if (!$row_id) {
            self::db_insert_user_notification($user_id, $notification_id, self::STATUS_USER_NOTIFICATION_READ);
            return;
        }

        // update
        self::db_update_user_notification($row_id, self::STATUS_USER_NOTIFICATION_READ);
    }

    private static function db_get_user_notification($user_id, $notification_id) {
        global $wpdb;
        $tbl_user_notifications = self::$table_user_notifications;
        $row_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$tbl_user_notifications} WHERE (user_id = %d AND notification_id = %d)", $user_id, $notification_id));
        return is_numeric($row_id) ? intval($row_id) : 0;
    }

    /**
     * @param bool $save
     * @param mixed $value
     * @param \Carbon_Fields\Field\Field $field
     * @return bool
     */
    public function carbon_fields_should_save_field_value($save, $value, $field) {
        // do not save new notification form
        if (str_contains($field->get_base_name(), 'sta_new_notification_')) {
            return false;
        }
        return $save;
    }

    public function on_saved_theme_options() {
        if (!current_user_can('administrator')) {
            return;
        }

        $this->handle_new_notification();
        $this->handle_update_notification();
    }

    private function handle_update_notification() {
        $notification_id = isset($_POST['sta_update_notification']) && is_numeric($_POST['sta_update_notification']) ? intval($_POST['sta_update_notification']) : 0;
        if (!$notification_id) {
            return;
        }

        $fields = $_POST['carbon_fields_compact_input'] ?? [];

        $title = $fields['_sta_new_notification_title'] ?? '';
        $message = $fields['_sta_new_notification_message'] ?? '';
        $is_global = ($fields['_sta_new_notification_is_global'] ?? '') == 'yes';
        $user_list = $fields['_sta_new_notification_users'] ?? '';

        if (!$title || !$message || (!$is_global && empty($user_list))) {
            return;
        }

        global $wpdb;

        // delete old user notifications
        $wpdb->delete(self::$table_user_notifications, ['notification_id' => $notification_id], ['%d']);

        // update notification
        $wpdb->update(
            self::$table_notifications,
            [
                'title' => $title,
                'message' => $message,
                'global' => $is_global ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            ['id' => $notification_id],
            ['%s', '%s', '%d', '%s'],
            ['%d']
        );

        // notification to specific users
        self::admin_new_user_notification($notification_id, $user_list);

        // redirect
        $redirect = remove_query_arg(['sta_notification_edit']);
        wp_safe_redirect($redirect);
        exit;
    }

    private function handle_new_notification() {
        if (!isset($_POST['sta_new_notification'])) {
            return;
        }

        $fields = $_POST['carbon_fields_compact_input'] ?? [];

        $title = $fields['_sta_new_notification_title'] ?? '';
        $message = $fields['_sta_new_notification_message'] ?? '';
        $is_global = ($fields['_sta_new_notification_is_global'] ?? '') == 'yes';
        $user_list = $fields['_sta_new_notification_users'] ?? '';

        // Helpers::log([$_POST, $fields, $message, $is_global, $user_list]); die;

        if (!$title || !$message || (!$is_global && empty($user_list))) {
            return;
        }

        // Helpers::log([$message, $is_global, $user_list]); die;

        // new notification
        $notification_id = self::db_new_notification($title, $message, $is_global);

        // global
        if ($is_global) {
            // invalid all user notification count
            delete_option(self::OPTION_USER_NOTIFICATION_COUNT);
            return;
        }

        // notification to specific users
        self::admin_new_user_notification($notification_id, $user_list);
    }

    private static function admin_new_user_notification($notification_id, $user_list) {
        foreach ($user_list as &$user_id) {
            $user_id = str_replace('user:user:', '', $user_id);
            $user_id = intval($user_id);
            self::db_insert_user_notification($user_id, $notification_id);
        }

        // invalid user notification count whenever there is a new notification
        self::invalid_user_notification_count($user_list);
    }

    private static function db_insert_user_notification($user_id, $notification_id, $status = self::STATUS_USER_NOTIFICATION_NEW) {
        global $wpdb;
        $wpdb->insert(
            self::$table_user_notifications,
            [
                'user_id' => $user_id,
                'notification_id' => $notification_id,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    }

    private static function db_update_user_notification($row_id, $status = self::STATUS_USER_NOTIFICATION_NEW) {
        global $wpdb;
        $wpdb->update(
            self::$table_user_notifications,
            [
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $row_id,
            ],
            ['%s', '%s'],
            ['%d']
        );
    }

    private static function db_new_notification($title, $message, $global = false) {
        global $wpdb;
        $wpdb->insert(
            self::$table_notifications,
            [
                'title' => $title,
                'message' => $message,
                'global' => $global ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            ['%s', '%s', '%d', '%s', '%s']
        );
        return $wpdb->insert_id;
    }

    public static function register_fields() {
        Container::make('theme_options', 'Notifications')
            ->add_tab('Notifications', [
                Field::make('html', 'notification_list')
                    ->set_html(self::class . '::notification_list'),
            ])
            ->add_tab('New notification', self::field_new_notification());
    }

    private static function db_get_notification($notification_id) {
        global $wpdb;
        $table_notifications = self::$table_notifications;
        $table_user_notifications = self::$table_user_notifications;

        $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_notifications} WHERE id = %d", $notification_id), ARRAY_A);

        if (!is_array($data) || empty($data)) {
            return null;
        }

        // is global
        if ($data['global']) {
            return $data;
        }

        // query notification users
        $user_list = $wpdb->get_results($wpdb->prepare("SELECT user_id FROM {$table_user_notifications} WHERE notification_id = %d", $notification_id), ARRAY_A);
        $user_list = wp_list_pluck($user_list, 'user_id');

        /**
         * @var  Field\Association_Field $field
         */
        $field = Field\Association_Field::factory('association', 'user');
        foreach ($user_list as &$user_id) {
            $user = new \WP_User($user_id);
            $user_id = $field->format_user_option($user);
        }
        $data['user_list'] = $user_list;

        return $data;
    }

    private static function field_new_notification() {
        $edit_id = $_GET['sta_notification_edit'] ?? '';
        $data = self::db_get_notification($edit_id);
        // Helpers::log($data);

        return [
            Field::make('text', 'sta_new_notification_title', 'Title')
                ->set_default_value($data['title'] ?? ''),
            Field::make('rich_text', 'sta_new_notification_message', 'Message')
                ->set_default_value(stripslashes($data['message'] ?? '')),
            Field::make('checkbox', 'sta_new_notification_is_global', 'Notification is global and will be sent to all the users')
                ->set_default_value(($data['global'] ?? '') ? 'yes' : 'no'),
            Field::make('association', 'sta_new_notification_users', 'Users')
                ->set_default_value($data['user_list'] ?? [])
                ->set_types([
                    ['type' => 'user'],
                ]),
            Field::make('html', 'notification_submit')
                ->set_html(self::field_notification_submit_html($edit_id)),
        ];
    }

    private static function field_notification_submit_html($edit_id) {
        if ($edit_id) {
            return sprintf('<button type="submit" class="button button-primary" name="sta_update_notification" value="%s">Submit</button>', $edit_id);
        }
        return '<button type="submit" class="button button-primary" name="sta_new_notification" value="yes">Submit</button>';
    }

    public static function notification_list() {
        ob_start();
        get_template_part('template-parts/admin/notification-list');
        return ob_get_clean();
    }

    public static function admin_list_notifications($args = []) {
        $per_page = 10;
        $page = isset($args['paged']) && is_numeric($args['paged']) ? intval($args['paged']) : 0;
        $page = max(1, $page);
        $offset = ($page - 1) * $per_page;

        global $wpdb;
        $tbl_notifications = self::$table_notifications;

        $query_select = "SELECT * FROM {$tbl_notifications} ORDER BY updated_at DESC, id DESC LIMIT {$offset}, {$per_page}";
        $results = $wpdb->get_results($query_select, ARRAY_A);

        $query_count = "SELECT COUNT(1) FROM {$tbl_notifications}";
        $count = $wpdb->get_var($query_count);

        return [$results, ceil($count / $per_page)];
    }

    public static function get_user_notifications($user_id, $args = []) {
        $user = get_user_by('ID', $user_id);
        if (!($user instanceof \WP_User)) {
            return [];
        }

        // $offset = isset($args['offset']) && is_numeric($args['offset']) ? intval($args['offset']) : 0;
        // $offset = max(0, $offset);
        $per_page = 10;
        $page = isset($args['paged']) && is_numeric($args['paged']) ? intval($args['paged']) : 0;
        $page = max(1, $page);
        $offset = ($page - 1) * $per_page;

        global $wpdb;
        $tbl_notifications = self::$table_notifications;
        $tbl_user_notifications = self::$table_user_notifications;

        $query_select = "SELECT tbl_notifications.*, tbl_user_notifications.status FROM {$tbl_notifications} AS tbl_notifications";
        $query_count = "SELECT COUNT(DISTINCT tbl_notifications.id) FROM {$tbl_notifications} AS tbl_notifications";

        $query = $wpdb->prepare(" LEFT JOIN {$tbl_user_notifications} AS tbl_user_notifications ON (tbl_user_notifications.notification_id = tbl_notifications.id AND tbl_user_notifications.user_id = %d)", $user_id);
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND (tbl_notifications.updated_at >= %s)", date('Y-m-d H:i:s', strtotime($user->user_registered)));
        $query .= " AND (";
        $query .= "(tbl_notifications.global = 0 AND tbl_user_notifications.id IS NOT NULL)";
        $query .= " OR (tbl_notifications.global = 1)";
        $query .= ")"; // close AND
        $query .= ")"; // close WHERE

        $count = $wpdb->get_var($query_count . $query);

        $query .= " GROUP BY tbl_notifications.id";
        $query .= " ORDER BY tbl_notifications.updated_at DESC, tbl_notifications.id DESC";
        $query .= " LIMIT {$offset}, {$per_page}";

        $results = $wpdb->get_results($query_select . $query, ARRAY_A);
        // Helpers::log([
        //     '$query' => $query,
        //     '$wpdb->last_query' => $wpdb->last_query,
        //     '$wpdb->last_error' => $wpdb->last_error,
        //     '$results' => $results,
        // ]);
        $data = [];
        foreach ($results as $item) {
            $post_date = strtotime($item['updated_at']);
            $data[] = [
                'id' => $item['id'],
                'title' => $item['title'],
                'message' => $item['message'],
                'is_new' => !$item['status'] || $item['status'] == self::STATUS_USER_NOTIFICATION_NEW,
                'post_date' => date_i18n('d F \'y', $post_date),
                'post_time' => date_i18n('H:i', $post_date),
            ];
        }

        return [$data, ceil($count / $per_page)];
    }
}
