<?php

namespace STA\Inc;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class CptCourse {

    private static $instance;
    public static $post_type = 'sfwd-courses';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'add_excerpts_to_courses']);

        // learndash_delete_course_progress(47, 1);
        add_action('template_redirect', [$this, 'redirect_unlock_course'], 1);
    }

    public function redirect_unlock_course() {
        if (!is_singular([CptCourseLesson::$post_type, CptCourseTopic::$post_type, CptCourseQuiz::$post_type])) {
            return;
        }

        if (!is_user_logged_in()) {
            return;
        }

        $post_id = get_queried_object_id();
        $course_id = get_post_meta($post_id, 'course_id', true);
        if (!$course_id) {
            return;
        }

        $user_id = get_current_user_id();

        $unlocked = learndash_is_course_prerequities_completed($course_id, $user_id);
        if ($unlocked) {
            // register user to the course automatically
            if (!ld_course_check_user_access($course_id, $user_id)) {
                ld_update_course_access($user_id, $course_id);
            }
            return;
        }

        wp_safe_redirect(get_permalink($course_id));
        exit;
    }

    public function add_excerpts_to_courses() {
        add_post_type_support(self::$post_type, 'excerpt');
    }

    public static function register_fields() {
        Container::make('post_meta', 'Course Details')
            ->where('post_type', '=', self::$post_type)
            ->add_fields([
                Field::make('text', 'duration', 'Duration'),
                Field::make('rich_text', 'course_desc', 'Description'),
            ]);
    }

    public static function user_can_enroll_course($user_id, $course_id) {
        if (!$user_id || !$course_id) {
            return false;
        }

        return learndash_is_course_prerequities_completed($course_id, $user_id);

    }

    public static function get_duration($course_id) {
        return carbon_get_post_meta($course_id, 'duration');
    }

    public static function get_description($course_id) {
        return carbon_get_post_meta($course_id, 'course_desc');
    }

    /**
     * @param int $user_id
     * @param int $course_id
     * @return bool
     */
    private static function is_course_lapsed($user_id, $course_id) {
        global $wpdb;
        $tbl_user_activity = \LDLMS_DB::get_table_name('user_activity');

        $query = "SELECT * FROM {$tbl_user_activity}";
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        $query .= $wpdb->prepare(" AND course_id = %d", $course_id);
        $query .= ")";
        $query .= " ORDER BY activity_updated DESC";
        $query .= " LIMIT 0,1";

        $activity = $wpdb->get_row($query, ARRAY_A);

        // the user has not started this course yet
        if (!is_array($activity) || empty($activity)) {
            return false;
        }

        // Helpers::log($activity);

        // the last activity is over 1 year
        return $activity['activity_updated'] < strtotime('-1 year');
    }

    public static function get_course_status_label($status) {
        if ($status == 'lapsed') {
            return __('Lapsed', 'sta');
        }
        return learndash_course_status_label($status);
    }

    public static function get_user_progress($user_id, $course_id) {
        if (!$user_id || !$course_id) {
            return null;
        }

        // if (self::is_user_course_progress_expired($user_id, $course_id)) {
        //     learndash_delete_course_progress($course_id, $user_id);
        // }

        $unlocked = learndash_is_course_prerequities_completed($course_id, $user_id);
        // if (!$is_course_prerequities_completed) {
        //     return null;
        // }

        $progress = learndash_user_get_course_progress($user_id, $course_id, 'summary');
        if (!is_array($progress) || empty($progress)) {
            return null;
        }

        $progress_completed = $unlocked ? $progress['completed'] : 0;
        $progress_total = $unlocked ? $progress['total'] : 0;
        $progress_status = $unlocked ? $progress['status'] : 'not_started';

        $is_lapsed = self::is_course_lapsed($user_id, $course_id);
        if ($is_lapsed) {
            $progress_status = 'lapsed';
            learndash_delete_course_progress($course_id, $user_id);
        }

        $is_completed = $unlocked && learndash_course_completed($user_id, $course_id);

        $completed_percentage = 100;
        if (!$is_completed) {
            $completed_percentage = $progress_total ? floor($progress_completed / $progress_total * 100) : 0;
            $completed_percentage = min($completed_percentage, 100);
        }

        $next_step_id = !$is_completed ? learndash_user_progress_get_first_incomplete_step($user_id, $course_id) : null;
        if ($completed_percentage < 1) {
            $lesson_list = learndash_course_get_lessons($course_id);
            $next_step_id = is_array($lesson_list) && !empty($lesson_list) ? $lesson_list[0] : $next_step_id;
        }

        return [
            'unlocked' => $unlocked,
            'completed' => $progress_completed,
            'total' => $progress_total,
            'completed_percentage' => $completed_percentage,
            'status' => $progress_status,
            'is_completed' => $is_completed,
            'is_in_progress' => !$is_completed && $completed_percentage > 0,
            'next_step_id' => $next_step_id,
            'is_lapsed' => $is_lapsed,
        ];
    }

    /**
     * @param int|array $parent_id
     * @param string $meta_key
     * @param string $child_post_type
     * @return int
     */
    public static function count_children($parent_id, $meta_key, $child_post_type) {
        global $wpdb;

        $parent_ids = is_array($parent_id) ? $parent_id : [$parent_id];
        $parent_ids = array_map('intval', $parent_ids);
        $parent_ids = array_filter($parent_ids);
        $parent_ids_string = implode(',', $parent_ids);

        $query = "SELECT COUNT(*) FROM {$wpdb->postmeta} as child_meta";
        $query .= $wpdb->prepare(" INNER JOIN {$wpdb->posts} AS child ON (child.ID = child_meta.post_id AND child.post_type = %s)", $child_post_type);
        $query .= $wpdb->prepare(" WHERE (child_meta.meta_key = %s AND child_meta.meta_value IN ({$parent_ids_string}))", $meta_key);

        $count = $wpdb->get_var($query);

        // printf('<pre>%s</pre>', var_export([
        //     '$parent_ids' => $parent_ids,
        //     '$parent_ids_string' => $parent_ids_string,
        //     '$query' => $query,
        //     '$wpdb->last_query' => $wpdb->last_query,
        //     '$wpdb->last_error' => $wpdb->last_error,
        // ], true));

        return is_numeric($count) ? intval($count) : 0;
    }

    public static function get_children($parent_id, $child_post_type, $meta_key = 'course_id') {
        global $wpdb;

        $query = "SELECT child_meta.post_id FROM {$wpdb->postmeta} as child_meta";
        $query .= $wpdb->prepare(" INNER JOIN {$wpdb->posts} AS child ON (child.ID = child_meta.post_id AND child.post_type = %s)", $child_post_type);
        $query .= $wpdb->prepare(" WHERE (child_meta.meta_key = %s AND child_meta.meta_value = %s) GROUP BY child_meta.post_id", $meta_key, $parent_id);

        $results = $wpdb->get_results($query, ARRAY_A);

        $data = wp_list_pluck($results, 'post_id');
        $data = is_array($data) ? $data : [];

        // printf('<pre>%s</pre>', var_export(['$query' => $query], true));

        return $data;
    }

    public static function get_course_quizzes($course_id) {
        return self::get_children($course_id, CptCourseQuiz::$post_type);
    }

    public static function get_course_lesson_count($course_id) {
        return self::count_children($course_id, 'course_id', CptCourseLesson::$post_type);
    }

    public static function get_course_quiz_count($course_id) {
        return self::count_children($course_id, 'course_id', CptCourseQuiz::$post_type);
    }

    public static function get_course_topic_count($course_id) {
        return self::count_children($course_id, 'course_id', CptCourseTopic::$post_type);
    }

    /**
     * @return int last activity in timestamp
     */
    public static function get_user_last_activity($user_id, $course_id) {
        global $wpdb;

        $table_name = \LDLMS_DB::get_table_name('user_activity');

        $query = $wpdb->prepare("SELECT activity_updated FROM {$table_name} WHERE (user_id = %s AND course_id = %s) ORDER BY activity_updated DESC LIMIT 0,1", $user_id, $course_id);
        $last_activity = $wpdb->get_var($query);
        return is_numeric($last_activity) ? $last_activity : 0;
    }

    /**
     * @param int $user_id
     * @return bool
     */
    public static function is_user_passed_all_cat_courses($user_id, $cat_slug) {
        $cat_courses = CptCourse::get_all_cat_courses($cat_slug);
        foreach ($cat_courses as $course_id) {
            $course_status = learndash_course_status($course_id, $user_id, true);
            if ($course_status != 'completed') {
                return false;
            }
        }
        return true;
    }

    public static function is_user_passed_all_courses($user_id) {
        $course_count = learndash_get_courses_count();
        $user_tier = TierSystem::get_user_tier($user_id);
        $total_completed_courses = $user_tier['total_completed_courses'];
        return $total_completed_courses >= $course_count;
    }

    public static function next_core_course_id($user_id) {
        $course_list = self::get_all_cat_courses('core');
        foreach ($course_list as $course_id) {
            $course_status = learndash_course_status($course_id, $user_id, true);
            if ($course_status != 'completed') {
                return $course_id;
            }
        }
        return null;
    }

    public static function next_course_id($user_id) {
        $all_courses = get_posts([
            'post_type' => CptCourse::$post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
        ]);
        foreach ($all_courses as $course_id) {
            $course_status = learndash_course_status($course_id, $user_id, true);
            if ($course_status != 'completed') {
                return $course_id;
            }
        }
        return null;
    }

    public static function get_all_cat_courses($cat_slug) {
        $term = get_term_by('slug', $cat_slug, 'ld_course_category');
        return get_posts([
            'post_type' => CptCourse::$post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => 'ld_course_category',
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ],
            ],
            'fields' => 'ids',
        ]);
    }
}
