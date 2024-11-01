<?php

namespace STA\Inc;

class TierSystem {

    private static $instance;
    private const META_USER_TIER = '_sta_user_tier';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
    }

    public static function tier_system() {
        return [
            [
                'slug' => 'no-tier',
                'label' => __('No achievements', 'sta'),
                'course_count' => 0,
                'points' => 0,
            ],
            [
                'slug' => 'silver',
                'label' => __('Silver', 'sta'),
                'course_count' => 3,
                'points' => 200,
            ],
            [
                'slug' => 'gold',
                'label' => __('Gold', 'sta'),
                'course_count' => 5,
                'points' => 200,
            ],
           /*[
                'slug' => 'platinum',
                'label' => __('Platinum', 'sta'),
                'course_count' => 9,
                'points' => 200,
            ],*/
        ];
    }

    public static function get_user_tier($user_id, $refresh = false) {
        if (!$refresh) {
            $user_tier = get_user_meta($user_id, self::META_USER_TIER, true);
            if (is_array($user_tier) && !empty($user_tier)) {
                return $user_tier;
            }
        }

        $current_tier = get_user_meta($user_id, self::META_USER_TIER, true);

        $user_total_completed_courses = PointLogs::count_user_completed_courses($user_id);
        // $user_total_completed_courses = 0;
        // $user_total_completed_courses = 8;
        $tier_system = self::tier_system();

        $user_tier = $tier_system[0];
        $total_points = $tier_system[0]['points'];
        $next_tier = null;

        foreach ($tier_system as $item) {
            if ($user_total_completed_courses >= $item['course_count']) {
                $total_points += $item['points'];
                $user_tier = $item;
                continue;
            }
            $next_tier = $item;
            break;
        }

        $next_tier_course_count = $next_tier['course_count'] ?? $user_tier['course_count'];
        $next_tier_require_courses = $next_tier_course_count - $user_total_completed_courses;

        $user_tier['next_tier'] = $next_tier;
        $user_tier['next_tier_require_courses'] = $next_tier_require_courses;
        $user_tier['total_completed_courses'] = $user_total_completed_courses;
        $user_tier['total_points'] = $total_points;

        // user has just gained a new tier
        if ($user_tier['slug'] != 'no-tier' && (!is_array($current_tier) || empty($current_tier) || $current_tier['slug'] != $user_tier['slug'])) {
            // PointLogs::gain_a_tier($user_id, $user_tier['slug'], $user_tier['points']);
            $user_tier['is_new'] = true;
        }

        update_user_meta($user_id, self::META_USER_TIER, $user_tier);

        return $user_tier;
    }
}