<?php

namespace STA\Inc;

class PointsSystem {

    private static $instance;
    private const OPTION_LAST_UPDATE_USERS_TOTAL_POINTS = '_sta_last_update_users_total_points';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        /**
         * on mark course completed
         * @see learndash_process_mark_complete()
         * @see learndash_update_user_activity()
         */
        add_action('learndash_update_user_activity', [$this, 'learndash_update_user_activity'], PHP_INT_MAX);

        /**
         * refresh all users total points
         */
        add_action('init', [$this, 'init'], PHP_INT_MAX);

        // test
        // learndash_delete_course_progress(125, 1);
    }

    public function init() {
        $this->refresh_all_users_total_points();
        // $this->test();
    }

    private function test() {
        if (!is_user_logged_in() || !current_user_can('administrator') || !isset($_GET['test'])) {
            return;
        }
        $user_id = get_current_user_id();
        PointLogs::pass_a_quiz($user_id, 391, PointLogs::TYPE_PASS_QUIZ_100, 50);
        die;
    }

    public function refresh_all_users_total_points() {
        if (!isset($_GET['sta-update-all-users-total-points'])) {
            return;
        }
        $last_update = get_option(self::OPTION_LAST_UPDATE_USERS_TOTAL_POINTS);
        $last_update = is_numeric($last_update) ? intval($last_update) : 0;
        $time_diff = time() - $last_update;

        if ($time_diff < DAY_IN_SECONDS) {
            return;
        }

        PointLogs::refresh_all_users_total_points();

        // update last update
        update_option(self::OPTION_LAST_UPDATE_USERS_TOTAL_POINTS, time());
        die;
    }

    /**
     * on mark course completed
     * @param array $data
     * @see learndash_process_mark_complete()
     * @see learndash_update_user_activity()
     */
    public function learndash_update_user_activity($data) {
        if (!$data['activity_completed']) {
            return;
        }

        $user_id = $data['user_id'];
        $activity_id = $data['activity_id'];

        switch ($data['activity_type']) {
            // complete course
            case 'course':
                $course_id = $data['course_id'];
                self::pass_a_course($user_id, $course_id);
                return;
            // complete quiz
            case 'quiz':
                $quiz_id = $data['post_id'];
                self::pass_a_quiz($user_id, $quiz_id, $activity_id);
                return;
        }

        // printf('<pre>%s</pre>', var_export($data, true));
        // die;
    }

    private static function get_quiz_points_by_complete_percentage($complete_percentage) {
        if ($complete_percentage >= 100) {
            return [PointLogs::TYPE_PASS_QUIZ_100, 50];
        }
        if ($complete_percentage >= 90) {
            return [PointLogs::TYPE_PASS_QUIZ_90, 30];
        }
        if ($complete_percentage >= 80) {
            return [PointLogs::TYPE_PASS_QUIZ_80, 10];
        }
        return [null, 0];
    }

    public static function pass_a_quiz($user_id, $quiz_id, $activity_id) {
        $complete_percentage = learndash_get_user_activity_meta($activity_id, 'percentage');

        [$type, $points] = self::get_quiz_points_by_complete_percentage($complete_percentage);

        if (!$type) {
            return;
        }

        PointLogs::pass_a_quiz($user_id, $quiz_id, $type, $points);
    }

    public static function pass_a_course($user_id, $course_id) {
        PointLogs::pass_a_course($user_id, $course_id);
    }

    public static function get_user_course_points($user_id, $course_id, $refresh = false) {
        return PointLogs::get_user_course_points($user_id, $course_id, $refresh);
    }

    public static function get_user_total_points($user_id, $refresh = false) {
        return PointLogs::get_user_total_points($user_id, $refresh);
    }
}
