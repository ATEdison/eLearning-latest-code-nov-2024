<?php

namespace STA\Inc;

use Google\Type\DateTime;
use STA\Inc\CarbonFields\ThemeOptions;

class PointLogs {

    public static $table = 'sta_point_logs';

    public const TYPE_PASS_A_COURSE = 'pass_a_course';
    public const TYPE_COMPLETE_2_COURSES_IN_A_DAY = 'complete_2_courses_in_a_day';
    public const TYPE_PASS_QUIZ_100 = 'pass_quiz_100';
    public const TYPE_PASS_QUIZ_90 = 'pass_quiz_90';
    public const TYPE_PASS_QUIZ_80 = 'pass_quiz_80';
    public const TYPE_GAIN_A_TIER = 'gain_a_tier';
    public const TYPE_EARN_A_BADGE = 'earn_a_badge';
    public const TYPE_REFER_A_COLLEAGUE = 'refer_a_colleague';
    public const TYPE_UPLOAD_A_PROFILE_PHOTO = 'upload_a_profile_photo';
    public const TYPE_SUBSCRIBE_TO_ENEWSLETTER = 'subscribe_to_enewsletter';

    private const TRANSIENT_USER_TOTAL_POINTS_DATA = '_sta_user_total_points_data';
    private const META_USER_TOTAL_POINTS = '_sta_user_total_points';

    public static function last_time_pass_a_core_course($user_id) {
        global $wpdb;
        $table = self::$table;

        $query = "SELECT tbl_point_log.created_at FROM {$table} AS tbl_point_log";
        $query .= " INNER JOIN {$wpdb->term_relationships} AS term_relationships ON (term_relationships.object_id = tbl_point_log.object_id)";
        $query .= " INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy ON (term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id AND term_taxonomy.taxonomy = 'ld_course_category')";
        $query .= " INNER JOIN {$wpdb->terms} AS terms ON (terms.term_id = term_taxonomy.term_id AND terms.slug = 'core')";
        $query .= " WHERE (1=1";
        $query .= " AND (";
        $query .= $wpdb->prepare("tbl_point_log.user_id = %d AND tbl_point_log.type = 'pass_a_course'", $user_id);
        $query .= ")"; // end AND
        $query .= ")"; // end WHERE
        $query .= " GROUP BY tbl_point_log.id ORDER BY tbl_point_log.created_at DESC";

        return $wpdb->get_var($query);
        // Helpers::log([
        //     '$query' => $query,
        //     '$wpdb->last_query' => $wpdb->last_query,
        //     '$wpdb->last_error' => $wpdb->last_error,
        //     '$result' => $result,
        // ]);
        // return $result;
    }

    public static function pass_a_quiz($user_id, $quiz_id, $type, $points) {
        global $wpdb;

        $data = [
            'type' => $type,
            'user_id' => $user_id,
            'object_id' => $quiz_id,
            'points' => $points,
            'note' => sprintf('quiz_id = %s', $quiz_id),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $format = ['%s', '%d', '%s', '%d', '%s', '%s'];

        $wpdb->insert(self::$table, $data, $format);

        // update transient
        // self::get_transient_user_total_points_data($user_id, true);

        // update user total points
        self::get_user_total_points($user_id, true);

        if ($type == self::TYPE_PASS_QUIZ_100) {
            self::attempt_to_earn_accuracy_badge($user_id, $quiz_id);
        }

        // update course points
        $course_id = get_post_meta($quiz_id, 'course_id', true);
        delete_transient(self::transient_user_course_points($user_id, $course_id));
    }

    private static function attempt_to_earn_accuracy_badge($user_id, $quiz_id) {
        $course_id = get_post_meta($quiz_id, 'course_id', true);
        if (!$course_id) {
            return;
        }

        $course_quiz_list = CptCourse::get_course_quizzes($course_id);
        $course_quiz_list_string = implode(', ', $course_quiz_list);

        global $wpdb;
        $table = self::$table;

        $query = "SELECT COUNT(1) FROM {$table}";
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND `user_id` = %d", $user_id);
        $query .= $wpdb->prepare(" AND `created_at` >= %s", self::expiry_date());
        $query .= $wpdb->prepare(" AND `type` IN (%s, %s)", self::TYPE_PASS_QUIZ_80, self::TYPE_PASS_QUIZ_90);
        $query .= " AND `object_id` IN ($course_quiz_list_string)";
        $query .= ")";
        $query .= " LIMIT 0,1";

        $count = $wpdb->get_var($query);

        if ($count > 0) {
            return;
        }

        self::earn_a_badge($user_id, 'accuracy', 0, ['course_id' => $course_id, 'quiz_id' => $quiz_id]);
    }

    public static function refer_a_colleague($user_id, $colleague_list, $points) {
        global $wpdb;

        $data = [
            'type' => self::TYPE_REFER_A_COLLEAGUE,
            'user_id' => $user_id,
            'object_id' => self::TYPE_REFER_A_COLLEAGUE,
            'points' => $points,
            'note' => maybe_serialize($colleague_list),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $format = ['%s', '%d', '%s', '%d', '%s', '%s'];

        $wpdb->insert(self::$table, $data, $format);

        // earn a referral badge
        self::earn_a_referral_badge($user_id);

        // only need to invalidate user total points, do not invalidate user total points data
        self::invalid_user_total_points($user_id);

        // update user total points
        self::get_user_total_points($user_id);
    }

    private static function earn_a_referral_badge($user_id) {
        $total_referrals = self::count_user_total_referrals($user_id);

        global $wpdb;
        $table = self::$table;

        $query = "SELECT * FROM {$table} WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        $query .= $wpdb->prepare(" AND type = %s", self::TYPE_EARN_A_BADGE);
        $query .= " AND object_id LIKE 'referrer%'";
        $query .= ')';

        $earn_badges = $wpdb->get_results($query, ARRAY_A);
        $earn_badges = wp_list_pluck($earn_badges, 'object_id');

        $referral_system = BadgeSystem::referral_system();
        foreach ($referral_system as $item) {
            if ($total_referrals < $item['count']) {
                break;
            }

            if (in_array($item['slug'], $earn_badges)) {
                continue;
            }

            self::earn_a_badge($user_id, $item['slug'], $item['points']);
        }
    }

    public static function pass_a_course($user_id, $course_id, $points = 100) {
        global $wpdb;

        $created_at = date('Y-m-d H:i:s');

        $data = [
            'type' => self::TYPE_PASS_A_COURSE,
            'user_id' => $user_id,
            'object_id' => $course_id,
            'points' => $points,
            'note' => sprintf('course_id = %s', $course_id),
            'created_at' => $created_at,
        ];

        $format = ['%s', '%d', '%s', '%d', '%s', '%s'];

        $wpdb->insert(self::$table, $data, $format);

        // earn course badge
        self::earn_a_badge($user_id, sprintf('badge_course_%s', $course_id), self::earn_a_badge_points());

        // starter
        self::earn_a_badge($user_id, 'starter', self::earn_a_badge_points(), ['course_id' => $course_id]);

        // complete 2 courses in a day
        self::attempt_to_earn_badge_complete_2_courses_in_a_day($user_id, $course_id, $created_at);

        // complete all partner courses
        self::attempt_to_earn_badge_complete_all_category_courses($user_id, $course_id, 'partner');

        // complete all optional courses
        self::attempt_to_earn_badge_complete_all_category_courses($user_id, $course_id, 'optional');

        // tier
        $user_tier = TierSystem::get_user_tier($user_id, true);
        if ($user_tier['is_new']) {
            self::gain_a_tier($user_id, $user_tier['slug'], $user_tier['points'], $course_id);
        }

        self::attempt_to_earn_quick_learner_badge($user_id, $user_tier, $course_id);

        self::attempt_to_earn_perfectionist_badge($user_id, $course_id);

        if (self::is_user_pass_all_category_courses($user_id, 'core', $course_id)) {
            self::attempt_to_earn_early_adopter_badge($user_id, $course_id);
            self::attempt_to_earn_language_speaker_badge($user_id, $course_id);
        }

        // update user points data transient
        self::get_transient_user_total_points_data($user_id, true);

        // invalidate course points
        delete_transient(self::transient_user_course_points($user_id, $course_id));
    }

    private static function attempt_to_earn_language_speaker_badge($user_id, $course_id) {
        $lang = ThemeSetup::current_lang();
        if ($lang == 'en') {
            self::earn_a_badge($user_id, 'english_speaker', 0, ['course_id' => $course_id]);
        }
    }

    private static function attempt_to_earn_early_adopter_badge($user_id, $course_id) {
        global $wpdb;
        $table = self::$table;

        $query = "SELECT COUNT(DISTINCT `user_id`) FROM {$table}";
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND `type` = %s", self::TYPE_EARN_A_BADGE);
        $query .= " AND `object_id` = 'early_adopter'";
        $query .= ")";

        $count = $wpdb->get_var($query);

        // early adopter badge is only available for the first 50 agents
        if ($count >= 50) {
            return;
        }

        self::earn_a_badge($user_id, 'early_adopter', 0, ['course_id' => $course_id]);
    }

    private static function attempt_to_earn_perfectionist_badge($user_id, $course_id) {
        $completed_course_count = self::count_user_completed_courses($user_id);
        $wp_count_posts = wp_count_posts(CptCourse::$post_type);
        $total_courses = $wp_count_posts->publish;

        // has not completed all the courses
        if ($completed_course_count < $total_courses) {
            return;
        }

        global $wpdb;
        $table = self::$table;

        $query = "SELECT COUNT(1) FROM {$table}";
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND `user_id` = %d", $user_id);
        $query .= $wpdb->prepare(" AND `created_at` >= %s", self::expiry_date());
        $query .= $wpdb->prepare(" AND `type` IN (%s, %s)", self::TYPE_PASS_QUIZ_80, self::TYPE_PASS_QUIZ_90);
        $query .= ")";
        $query .= " LIMIT 0,1";

        $count = $wpdb->get_var($query);

        // there is a least one quiz not full points
        if ($count > 0) {
            return;
        }

        self::earn_a_badge($user_id, 'perfectionist', 0, ['course_id' => $course_id]);
    }

    /**
     * @param int $user_id
     * @param array $user_tier
     * @param int $course_id
     */
    private static function attempt_to_earn_quick_learner_badge($user_id, $user_tier, $course_id) {
        if ($user_tier['slug'] != 'platinum') {
            return;
        }

        global $wpdb;
        $table = self::$table;

        $query = "SELECT COUNT(DISTINCT `object_id`) FROM {$table}";
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d AND `type` = %s", $user_id, self::TYPE_PASS_A_COURSE);
        $query .= $wpdb->prepare(" AND `created_at` >= %s", date('Y-m-d H:i:s', strtotime('-1 day')));
        $query .= ")";
        $query .= " ORDER BY `created_at` DESC";

        $count = $wpdb->get_var($query);

        // did not complete courses in a day
        if ($count < $user_tier['course_count']) {
            return;
        }

        self::earn_a_badge($user_id, 'quick_learner', 0, ['course_id' => $course_id]);
    }

    private static function attempt_to_earn_badge_complete_all_category_courses($user_id, $course_id, $cat_slug, $points = 0) {
        if (!self::is_user_pass_all_category_courses($user_id, $cat_slug, $course_id)) {
            return;
        }
        self::earn_a_badge($user_id, sprintf('complete_%s_courses', $cat_slug), $points);
    }

    /**
     * @param int $user_id
     * @param string $cat_slug
     * @param int $course_id
     * @return bool
     */
    public static function is_user_pass_all_category_courses($user_id, $cat_slug, $course_id = null) {
        $cat = get_term_by('slug', $cat_slug, 'ld_course_category');

        if ($course_id) {
            $course_cats = wp_get_post_terms($course_id, 'ld_course_category', ['fields' => 'ids']);
            // the course does not contain $cat
            if (!in_array($cat->term_id, $course_cats)) {
                return false;
            }
        }

        return CptCourse::is_user_passed_all_cat_courses($user_id, $cat_slug);
    }

    public static function get_all_passed_courses($user_id) {
        global $wpdb;
        $table = self::$table;

        $query = "SELECT DISTINCT `object_id` FROM {$table}";
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %s", $user_id);
        $query .= $wpdb->prepare(" AND `type` = %s", self::TYPE_PASS_A_COURSE);
        $query .= $wpdb->prepare(" AND `created_at` >= %s", self::expiry_date());
        $query .= ")";

        $result = $wpdb->get_results($query);

        $course_list = wp_list_pluck($result, 'object_id');
        $course_list = is_array($course_list) ? $course_list : [];
        /** @noinspection PhpUnnecessaryLocalVariableInspection */
        $course_list = array_filter($course_list);
        return $course_list;
    }

    /**
     * @param int $user_id
     * @param int $course_id
     * @param string $completed_at
     */
    private static function attempt_to_earn_badge_complete_2_courses_in_a_day($user_id, $course_id, $completed_at) {
        global $wpdb;
        $table = self::$table;

        $query = "SELECT * FROM {$table}";
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND `user_id` = %d", $user_id);
        $query .= $wpdb->prepare(" AND ((`type` = %s AND `object_id` != %s) OR `type` = %s)", self::TYPE_PASS_A_COURSE, $course_id, self::TYPE_COMPLETE_2_COURSES_IN_A_DAY);
        $query .= ")";
        $query .= " ORDER BY `created_at` DESC, `id` DESC LIMIT 0, 1;";
        $row = $wpdb->get_row($query, ARRAY_A);

        if (!is_array($row) || empty($row)) {
            return;
        }

        if ($row['type'] == self::TYPE_COMPLETE_2_COURSES_IN_A_DAY) {
            return;
        }

        $prev_course_completed_at = new \DateTime($row['created_at']);
        $course_completed_at = new \DateTime($completed_at);
        $date_diff = $course_completed_at->getTimestamp() - $prev_course_completed_at->getTimestamp();
        if ($date_diff >= DAY_IN_SECONDS) {
            return;
        }

        $prev_course_id = $row['object_id'];

        $query = "SELECT * FROM {$table}";
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND `user_id` = %d", $user_id);
        $query .= $wpdb->prepare(" AND `type` = %s", self::TYPE_COMPLETE_2_COURSES_IN_A_DAY);
        $query .= " AND (`object_id` LIKE '${course_id}_%' OR `object_id` LIKE '%_{$course_id}'OR `object_id` LIKE '{$prev_course_id}_%'  OR `object_id` LIKE '%_{$prev_course_id}')";
        $query .= $wpdb->prepare(" AND `created_at` >= %s", self::expiry_date()); // only search for not expired data
        $query .= ")";
        $query .= " LIMIT 0, 1;";
        $row = $wpdb->get_row($query, ARRAY_A);

        // course or prev_course is counted
        if (is_array($row) && !empty($row)) {
            return;
        }

        $data = [
            'type' => self::TYPE_COMPLETE_2_COURSES_IN_A_DAY,
            'user_id' => $user_id,
            'object_id' => sprintf('%s_%s', $course_id, $prev_course_id),
            'points' => 50,
            'note' => sprintf('course_id = %s, prev_course_id = %s', $course_id, $prev_course_id),
            'created_at' => $completed_at,
        ];

        $format = ['%s', '%d', '%s', '%d', '%s', '%s'];

        $wpdb->insert(self::$table, $data, $format);
    }

    private static function expiry_date() {
        return date('Y-m-d H:i:s', strtotime('-12 months'));
    }

    private static function earn_a_badge_points() {
        return 50;
    }

    public static function gain_a_tier($user_id, $tier, $points, $course_id) {
        global $wpdb;

        $data = [
            'type' => self::TYPE_GAIN_A_TIER,
            'user_id' => $user_id,
            'object_id' => $tier,
            'points' => $points,
            'note' => sprintf('tier = %s', $tier),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $format = ['%s', '%d', '%s', '%d', '%s', '%s'];

        $wpdb->insert(self::$table, $data, $format);

        // earn a tier badge
        self::earn_a_badge($user_id, sprintf('badge_tier_%s', $tier), self::earn_a_badge_points(), ['course_id' => $course_id]);
    }

    public static function upload_a_profile_photo($user_id) {
        global $wpdb;

        $row = self::is_action_completed($user_id, self::TYPE_UPLOAD_A_PROFILE_PHOTO);
        if (is_array($row) && !empty($row)) {
            return;
        }

        $data = [
            'type' => self::TYPE_UPLOAD_A_PROFILE_PHOTO,
            'user_id' => $user_id,
            'object_id' => self::TYPE_UPLOAD_A_PROFILE_PHOTO,
            'points' => 30,
            'note' => '',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $format = ['%s', '%d', '%s', '%d', '%s', '%s'];

        $wpdb->insert(self::$table, $data, $format);

        // invalidate user total points
        self::invalid_user_total_points($user_id);

        // update user total points
        self::get_user_total_points($user_id);
    }

    public static function subscribe_to_enewsletter($user_id, $points) {
        global $wpdb;

        $row = self::is_action_completed($user_id, self::TYPE_SUBSCRIBE_TO_ENEWSLETTER);
        if (is_array($row) && !empty($row)) {
            return;
        }

        $data = [
            'type' => self::TYPE_SUBSCRIBE_TO_ENEWSLETTER,
            'user_id' => $user_id,
            'object_id' => self::TYPE_SUBSCRIBE_TO_ENEWSLETTER,
            'points' => $points,
            'note' => '',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $format = ['%s', '%d', '%s', '%d', '%s', '%s'];

        $wpdb->insert(self::$table, $data, $format);

        // invalidate user total points
        self::invalid_user_total_points($user_id);

        // update user total points
        self::get_user_total_points($user_id);
    }

    public static function earn_a_badge($user_id, $badge, $points, $note = null) {
        global $wpdb;

        $row = self::is_action_completed($user_id, self::TYPE_EARN_A_BADGE, $badge);
        if (is_array($row) && !empty($row)) {
            return;
        }

        $data = [
            'type' => self::TYPE_EARN_A_BADGE,
            'user_id' => $user_id,
            'object_id' => $badge,
            'points' => $points,
            'note' => $note ? maybe_serialize($note) : '',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $format = ['%s', '%d', '%s', '%d', '%s', '%s'];

        $wpdb->insert(self::$table, $data, $format);

        // invalidate user total points
        self::invalid_user_total_points($user_id);
    }

    public static function is_action_completed($user_id, $action_type, $object_id = null) {
        global $wpdb;
        $table = self::$table;

        $query = "SELECT * FROM {$table} WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        $query .= $wpdb->prepare(" AND type = %s", $action_type);
        if ($object_id) {
            $query .= $wpdb->prepare(" AND object_id = %s", $object_id);
        }
        $query .= ')';

        return $wpdb->get_row($query, ARRAY_A);
    }

    public static function refresh_all_users_total_points() {
        $user_list = RankingSystem::get_user_list();
        // var_dump($user_list);

        foreach ($user_list as $user_id) {
            // refresh user total points
            self::get_user_total_points($user_id, true);
        }
    }

    public static function get_user_total_points($user_id, $refresh = false) {
        // $refresh = true;
        if (!$refresh) {
            $total = get_user_meta($user_id, self::META_USER_TOTAL_POINTS, true);

            // use cache, positive or -1
            if ($total) {
                return max(0, $total);
            }
        }


        // $refresh = true;
        $data = self::get_transient_user_total_points_data($user_id, $refresh);

        // printf('<pre>%s</pre>', var_export($data, true));

        $total = 0;

        // expiring points, courses, quizzes, tier
        if (!empty($data)) {
            $total += self::calculate_user_points_data($user_id, $data);
        }

        // non-expiring points

        // refer a colleague
        $total += self::count_user_referral_points($user_id);

        // upload a profile photo
        $total += self::get_user_completed_action_points($user_id, self::TYPE_UPLOAD_A_PROFILE_PHOTO);

        // subscribe to enewsletter
        $total += self::get_user_completed_action_points($user_id, self::TYPE_SUBSCRIBE_TO_ENEWSLETTER);

        // badge points
        $total += self::count_user_badge_points($user_id);

        /**
         * cache value = positive or -1
         * this value will be deleted once user has a new point log
         * @see PointLogs::update_transient_user_total_points_data()
         * @see PointLogs::refer_colleagues()
         */
        update_user_meta($user_id, self::META_USER_TOTAL_POINTS, $total ?: -1);

        // update user rank
        RankingSystem::update_user_rank($user_id, $total);

        return $total;
    }

    private static function count_user_badge_points($user_id) {
        global $wpdb;
        $table = self::$table;

        $query = "SELECT SUM(points) FROM {$table} WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        $query .= $wpdb->prepare(" AND type = %s", self::TYPE_EARN_A_BADGE);
        $query .= ')';

        return $wpdb->get_var($query);
    }

    private static function get_user_completed_action_points($user_id, $type) {
        $data = self::is_action_completed($user_id, $type);
        if (!is_array($data) || empty($data)) {
            return 0;
        }

        return $data['points'];
    }

    public static function count_user_total_referrals($user_id) {
        global $wpdb;
        $table = self::$table;

        $query = "SELECT * FROM {$table} WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        $query .= $wpdb->prepare(" AND type = %s", self::TYPE_REFER_A_COLLEAGUE);
        $query .= ')';

        $data = $wpdb->get_results($query, ARRAY_A);
        $email_list = [];
        foreach ($data as $item) {
            $email_list = array_merge($email_list, maybe_unserialize($item['note']));
        }
        $email_list = array_unique($email_list);
        $email_list = array_filter($email_list);
        return count($email_list);
    }

    public static function count_user_referral_points($user_id) {
        $total_referrals = self::count_user_total_referrals($user_id);
        return $total_referrals * 50;
    }

    public static function invalid_user_total_points($user_id) {
        delete_user_meta($user_id, self::META_USER_TOTAL_POINTS);

        // update user total points
        self::get_user_total_points($user_id);
    }

    private static function calculate_user_points_data($user_id, $data) {
        $total = 0;

        // O(n)
        foreach ($data as $object_id => $item) {
            switch ($item['type']) {
                // course points
                // accept 2 highest values for each course
                case self::TYPE_PASS_A_COURSE:
                    $accept_list = array_slice($item['point_list'], 0, 2);
                    $total += array_sum($accept_list);
                    break;
                // quiz points
                // accept only the highest value for each quiz
                case self::TYPE_PASS_QUIZ_80:
                case self::TYPE_PASS_QUIZ_90:
                case self::TYPE_PASS_QUIZ_100:
                    $total += intval($item['point_list'][0]);
                    break;
                case self::TYPE_COMPLETE_2_COURSES_IN_A_DAY:
                    $total += array_sum($item['point_list']);
                    break;
            }
        }

        // tier points
        $user_tier = TierSystem::get_user_tier($user_id);
        $total += $user_tier['total_points'];

        return $total;
    }

    public static function get_user_course_points($user_id, $course_id, $refresh = false) {
        $value = get_transient(self::transient_user_course_points($user_id, $course_id));

        // positive or -1
        if ($value) {
            return max(0, $value);
        }

        // $refresh = true;
        $data = self::get_transient_user_total_points_data($user_id, $refresh);

        if (empty($data)) {
            self::update_transient_user_course_points($user_id, $course_id, 0);
            return 0;
        }

        $total = 0;

        // course points
        $course_point_logs = $data[$course_id] ?? [];
        $course_point_logs = $course_point_logs['point_list'] ?? [];
        $total += array_sum(array_slice($course_point_logs, 0, 2)); // accept the 2 highest values within 12 months

        // quizzes points
        $quizzes = CptCourse::get_course_quizzes($course_id);
        foreach ($quizzes as $quiz_id) {
            $quiz_point_logs = $data[$quiz_id] ?? [];
            $quiz_point_logs = $quiz_point_logs['point_list'] ?? [];
            $total += $quiz_point_logs[0] ?? 0;
        }

        self::update_transient_user_course_points($user_id, $course_id, $total);

        return $total;
    }

    private static function transient_user_course_points($user_id, $course_id) {
        return sprintf('_sta_user_course_points_%s_%s', $user_id, $course_id);
    }

    private static function update_transient_user_course_points($user_id, $course_id, $value) {
        /**
         * this value will be expired on next 12 hours or be deleted/updated once course children is updated
         * @see PointLogs::pass_a_course()
         * @see PointLogs::pass_a_quiz()
         */
        set_transient(self::transient_user_course_points($user_id, $course_id), $value ?: -1, 12 * HOUR_IN_SECONDS); // expired on next 12 hours
    }

    /**
     * this value holds user expiring points data
     * @param int $user_id
     * @param bool $refresh
     * @return array
     */
    private static function get_transient_user_total_points_data($user_id, $refresh = false) {
        $transient_name = sprintf('%s_%s', self::TRANSIENT_USER_TOTAL_POINTS_DATA, $user_id);

        // use cache
        if (!$refresh) {
            $data = get_transient($transient_name);

            // the transient is expired or the data has never ever calculated
            if (is_array($data)) {
                return $data;
            }
        }

        global $wpdb;
        $table = self::$table;

        $query = "SELECT * FROM {$table} WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        $query .= $wpdb->prepare(" AND created_at >= %s", self::expiry_date()); // only query logs within 12 months
        $query .= ')';

        $logs = $wpdb->get_results($query, ARRAY_A);

        // printf('<pre>%s</pre>', var_export([
        //     '$query' => $query,
        //     '$wpdb->last_error' => $wpdb->last_error,
        //     '$logs' => $logs,
        // ], true));

        $data = [];

        if (!is_array($logs) || empty($logs)) {
            self::update_transient_user_total_points_data($user_id, $data);
            return $data;
        }

        // O(n)
        foreach ($logs as $item) {
            $object_id = $item['object_id'];
            $data[$object_id] = is_array($data[$object_id])
                ? $data[$object_id]
                : [
                    'type' => $item['type'],
                    'point_list' => [],
                ];

            $data[$object_id]['point_list'][] = $item['points'];
        }

        // O(n) - sort point list in descending order
        foreach ($data as &$item) {
            rsort($item['point_list']);
        }

        self::update_transient_user_total_points_data($user_id, $data);

        return $data;
    }

    private static function update_transient_user_total_points_data($user_id, $data) {
        $transient_name = sprintf('%s_%s', self::TRANSIENT_USER_TOTAL_POINTS_DATA, $user_id);
        set_transient($transient_name, $data, 12 * HOUR_IN_SECONDS); // expired on next 12 hours
        delete_user_meta($user_id, self::META_USER_TOTAL_POINTS);
    }

    public static function count_user_completed_courses($user_id) {
        global $wpdb;
        $table = self::$table;

        $query = "SELECT COUNT(DISTINCT `object_id`) FROM {$table}";
        $query .= " INNER JOIN `{$wpdb->posts}` AS `posts` ON (`posts`.`ID` = `sta_point_logs`.`object_id` AND `posts`.`post_type` = 'sfwd-courses' AND `posts`.`post_status` = 'publish')";
        $query .= " WHERE (1=1";
        $query .= $wpdb->prepare(" AND `user_id` = %d AND `type` = %s", $user_id, self::TYPE_PASS_A_COURSE);
        $query .= $wpdb->prepare(" AND `created_at` >= %s", self::expiry_date()); // only count courses that are done within one year
        $query .= ")";

        $value = $wpdb->get_var($query);

        return is_numeric($value) ? intval($value) : 0;
    }
}
