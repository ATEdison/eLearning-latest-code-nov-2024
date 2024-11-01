<?php

namespace STA\Inc;

use Carbon_Fields\Field;
use STA\Inc\CarbonFields\ThemeOptions;

class BadgeSystem {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {

    }

    public static function earn_a_badge($user_id, $badge_title, $points) {
        $badge_slug = sanitize_title($badge_title);
        PointLogs::earn_a_badge($user_id, $badge_slug, $points, $badge_title);
    }

    public static function referral_system() {
        return [
            [
                'slug' => 'referrer',
                'label' => __('Referrer', 'sta'),
                'points' => 50,
                'count' => 1,
            ],
            [
                'slug' => 'referrer_pro',
                'label' => __('Referrer Pro', 'sta'),
                'points' => 50,
                'count' => 2,
            ],
            [
                'slug' => 'referrer_champion',
                'label' => __('Referrer Champion', 'sta'),
                'points' => 50,
                'count' => 4,
            ],
            [
                'slug' => 'referrer_expert',
                'label' => __('Referrer Expert', 'sta'),
                'points' => 50,
                'count' => 8,
            ],
            [
                'slug' => 'referrer_master',
                'label' => __('Referrer Master', 'sta'),
                'points' => 50,
                'count' => 10,
            ],
        ];
    }

    public static function get_course_earned_badges($user_id, $course_id) {
        $badge_list = [
            sprintf('badge_course_%s', $course_id),
        ];

        global $wpdb;
        $table = PointLogs::$table;

        $query = "SELECT * FROM {$table} WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        $query .= $wpdb->prepare(" AND type = %s", PointLogs::TYPE_EARN_A_BADGE);
        $query .= " AND note LIKE '%\"course_id\";i:{$course_id};%'";
        $query .= ')';

        $results = $wpdb->get_results($query, ARRAY_A);
        $results = wp_list_pluck($results, 'object_id');

        // printf('<pre>%s</pre>', var_export([
        //     '$query' => $query,
        //     '$wpdb->last_error' => $wpdb->last_error,
        // ], true));

        return array_merge($badge_list, $results);
    }

    public static function count_user_badges($user_id) {
        global $wpdb;
        $table = PointLogs::$table;

        $query = "SELECT COUNT(1) FROM {$table} WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        $query .= $wpdb->prepare(" AND type = %s", PointLogs::TYPE_EARN_A_BADGE);
        $query .= ')';

        $count = $wpdb->get_var($query);

        // printf('<pre>%s</pre>', var_export([
        //     '$count' => $count,
        //     '$wpdb->last_query' => $wpdb->last_query,
        //     '$wpdb->last_error' => $wpdb->last_error,
        // ], true));

        return is_numeric($count) ? intval($count) : 0;
    }

    public static function get_user_earned_badges($user_id) {
        global $wpdb;
        $table = PointLogs::$table;

        $query = "SELECT * FROM {$table} WHERE (1=1";
        $query .= $wpdb->prepare(" AND user_id = %d", $user_id);
        $query .= $wpdb->prepare(" AND type = %s", PointLogs::TYPE_EARN_A_BADGE);
        $query .= ')';

        $results = $wpdb->get_results($query, ARRAY_A);
        return wp_list_pluck($results, 'object_id');
    }

    public static function is_user_subscribed_to_newsletters($user_id) {
        $row = PointLogs::is_action_completed($user_id, PointLogs::TYPE_SUBSCRIBE_TO_ENEWSLETTER);
        return is_array($row) && !empty($row);
    }

    public static function is_user_invited_colleagues($user_id) {
        $row = PointLogs::is_action_completed($user_id, PointLogs::TYPE_REFER_A_COLLEAGUE);
        return is_array($row) && !empty($row);
    }

    public static function is_user_earned_upload_profile_photo_badge($user_id) {
        $row = PointLogs::is_action_completed($user_id, PointLogs::TYPE_UPLOAD_A_PROFILE_PHOTO);
        return is_array($row) && !empty($row);
    }

    public static function is_user_gained_a_silver_badge($user_id) {
        $row = PointLogs::is_action_completed($user_id, PointLogs::TYPE_GAIN_A_TIER);
        return is_array($row) && !empty($row); // any tier is ok because silver is the first tier user can gain
    }

    public static function field_system_badges($lang) {
        $fields = [
            Field::make('separator', 's1', 'Tiers'),
            Field::make('image', 'badge_tier_silver', 'Silver')->set_width(30),
            Field::make('text', ThemeOptions::meta_field('badge_tier_silver_desc', $lang), 'Tier Silver Description')
                ->set_width(70)
                ->set_default_value('Tier Silver'),
            Field::make('image', 'badge_tier_gold', 'Gold')->set_width(30),
            Field::make('text', ThemeOptions::meta_field('badge_tier_gold_desc', $lang), 'Tier Gold Description')
                ->set_width(70)
                ->set_default_value('Tier Gold'),
            Field::make('image', 'badge_tier_platinum', 'Platinum')->set_width(30),
            Field::make('text', ThemeOptions::meta_field('badge_tier_platinum_desc', $lang), 'Tier Platinum Description')
                ->set_width(70)
                ->set_default_value('Tier Platinum'),
            Field::make('separator', 's2', 'Courses'),
            // Field::make('image', 'badge_starter', 'Starter'),
        ];

        // @TODO: this might cause slow query issue once more courses are added
        $course_list = get_posts([
            'post_type' => CptCourse::$post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ]);

        /**
         * @var \WP_Post $course
         */
        foreach ($course_list as $course) {
            $fields[] = Field::make('image', sprintf('badge_course_%s', $course->ID), sprintf('%s (ID: %s)', $course->post_title, $course->ID))->set_width(30);
            $fields[] = Field::make('text', ThemeOptions::meta_field(sprintf('badge_course_%s_desc', $course->ID), $lang), 'Description')
                ->set_width(70)
                ->set_default_value(sprintf('Completed %s course', $course->post_title));
        }

        // referrals
        $fields[] = Field::make('separator', 's3', 'Referrals');
        $referral_badge_list = self::referral_system();
        foreach ($referral_badge_list as $item) {
            $field_name = sprintf('badge_referral_%s', $item['slug']);
            $field_desc_name = ThemeOptions::meta_field(sprintf('badge_referral_%s_desc', $item['slug']), $lang);
            $fields[] = Field::make('image', $field_name, sprintf('%s Image', $item['label']))->set_width(30);
            $fields[] = Field::make('text', $field_desc_name, sprintf('%s Description', $item['label']))
                ->set_width(70)
                ->set_default_value($item['label']);
        }

        // category
        $fields[] = Field::make('separator', 's5', 'Categories');
        $fields[] = Field::make('image', 'badge_category_complete_partner_courses', 'Partner Modules Completion')->set_width(30);
        $fields[] = Field::make('text', ThemeOptions::meta_field('badge_category_complete_partner_courses_desc', $lang), 'Description')
            ->set_width(70)
            ->set_default_value('Completed all partner courses');
        $fields[] = Field::make('image', 'badge_category_complete_optional_courses', 'Optional Modules Completion')->set_width(30);
        $fields[] = Field::make('text', ThemeOptions::meta_field('badge_category_complete_optional_courses_desc', $lang), 'Description')
            ->set_width(70)
            ->set_default_value('Completed all optional courses');

        // others
        $fields[] = Field::make('separator', 's4', 'Others');

        $fields = array_merge($fields, [
            Field::make('image', 'badge_starter', 'Starter')->set_width(30),
            Field::make('text', ThemeOptions::meta_field('badge_starter_desc', $lang), 'Description')->set_width(70)->set_default_value('Starter'),
            Field::make('image', 'badge_quick_learner', 'Quick Learner')->set_width(30),
            Field::make('text', ThemeOptions::meta_field('badge_quick_learner_desc', $lang), 'Description')->set_width(70)->set_default_value('Quick Learner'),
            Field::make('image', 'badge_accuracy', 'Accuracy')->set_width(30),
            Field::make('text', ThemeOptions::meta_field('badge_accuracy_desc', $lang), 'Description')->set_width(70)->set_default_value('Accuracy'),
            Field::make('image', 'badge_perfectionist', 'Perfectionist')->set_width(30),
            Field::make('text', ThemeOptions::meta_field('badge_perfectionist_desc', $lang), 'Description')->set_width(70)->set_default_value('Perfectionist'),
            Field::make('image', 'badge_early_adopter', 'Early Adopter')->set_width(30),
            Field::make('text', ThemeOptions::meta_field('badge_early_adopter_desc', $lang), 'Description')->set_width(70)->set_default_value('Early Adopter'),
            Field::make('image', 'badge_english_speaker', 'English Speaker')->set_width(30),
            Field::make('text', ThemeOptions::meta_field('badge_english_speaker_desc', $lang), 'Description')->set_width(70)->set_default_value('English Speaker'),
        ]);

        return $fields;
    }

    /**
     * @param array $args
     * @return array|null
     */
    public static function get_badge_details($args) {
        $slug = $args['slug'] ?? null;
        if (!$slug) {
            return [];
        }

        if (strpos($slug, 'badge_tier_') === 0) {
            $tier = str_replace('badge_tier_', '', $slug);
            return [
                'image_id' => carbon_get_theme_option(sprintf('badge_tier_%s', $tier)),
                'desc' => ThemeOptions::get_meta_value(sprintf('badge_tier_%s_desc', $tier)),
            ];
        }

        if (strpos($slug, 'badge_course_') === 0) {
            $course_id = str_replace('badge_course_', '', $slug);
            return [
                'image_id' => carbon_get_theme_option(sprintf('badge_course_%s', $course_id)),
                'desc' => ThemeOptions::get_meta_value(sprintf('badge_course_%s_desc', $course_id)),
            ];
        }
        if (strpos($slug, 'referrer') === 0) {
            $referral_level = str_replace('referrer_', '', $slug);
            return [
                'image_id' => carbon_get_theme_option(sprintf('badge_referral_referrer_%s', $referral_level)),
                'desc' => ThemeOptions::get_meta_value(sprintf('badge_referral_referrer_%s_desc', $referral_level)),
            ];
        }

        if (preg_match('@complete_.*_courses@', $slug)) {
            return [
                'image_id' => carbon_get_theme_option(sprintf('badge_category_%s', $slug)),
                'desc' => ThemeOptions::get_meta_value(sprintf('badge_category_%s_desc', $slug)),
            ];
        }

        $image_id = carbon_get_theme_option(sprintf('badge_%s', $slug));
        if ($image_id) {
            return [
                'image_id' => $image_id,
                'desc' => carbon_get_theme_option(sprintf('badge_%s_desc', $slug)),
            ];
        }

        $custom_badge = ThemeOptions::get_custom_badge_by_slug($slug);
        if (is_array($custom_badge) && !empty($custom_badge)) {
            return [
                'image_id' => $custom_badge['design'],
                'desc' => $custom_badge[ThemeOptions::meta_field('desc')] ?? '',
            ];
        }

        return null;
    }
}
