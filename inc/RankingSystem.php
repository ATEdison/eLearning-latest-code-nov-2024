<?php

namespace STA\Inc;

use WPML\LIB\WP\User;

class RankingSystem {

    private static $instance;

    private static $table = 'sta_user_total_points';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_sta_leaderboard_load_more', [$this, 'sta_leaderboard_load_more']);
    }

    public function sta_leaderboard_load_more() {
        [$data, $has_more] = self::get_leaderboard($_POST);
        wp_send_json([
            'success' => true,
            'data' => $data,
            'has_more' => $has_more,
        ], 200);
    }

    public static function get_user_list() {
        global $wpdb;
        $table = self::$table;

        $query = "SELECT user_id FROM {$table}";
        // only users who has not updated within 12 hours
        $query .= $wpdb->prepare(" WHERE updated_at < %s", date('Y-m-d H:i:s', strtotime('-12 hours')));
        $query .= " ORDER BY id ASC LIMIT 0, 999999";
        $results = $wpdb->get_results($query, ARRAY_A);
        // $results = wp_list_pluck($results, 'user_id');
        // printf('<pre>%s</pre>', var_export([
        //     '$query' => $query,
        //     '$wpdb->last_query' => $wpdb->last_query,
        //     '$wpdb->last_error' => $wpdb->last_error,
        //     '$results' => $results,
        // ], true));
        return wp_list_pluck($results, 'user_id');
    }

    public static function get_user_rank_data($user_id) {
        global $wpdb;
        $table = self::$table;
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE user_id = %d", $user_id), ARRAY_A);
    }

    public static function get_user_global_rank($user_id, $user_rank_data, $refresh = false) {
        if (!$refresh) {
            $rank = get_transient(self::transient_user_global_rank($user_id));
            // use cache
            if ($rank) {
                return $rank;
            }
        }
        $rank = self::calculate_user_rank($user_rank_data);
        if (!$rank) {
            return null;
        }
        set_transient(self::transient_user_global_rank($user_id), $rank);
        return $rank;
    }

    private static function transient_user_global_rank($user_id) {
        return sprintf('_sta_user_global_rank_%s', $user_id);
    }

    private static function transient_user_country_rank($user_id) {
        return sprintf('_sta_user_country_rank_%s', $user_id);
    }

    public static function get_user_country_rank($user_id, $user_rank_data, $refresh = false) {
        if (!$refresh) {
            $rank = get_transient(self::transient_user_country_rank($user_id));
            // use cache
            if ($rank) {
                return $rank;
            }
        }
        $rank = self::calculate_user_rank($user_rank_data, true);
        if (!$rank) {
            return null;
        }
        set_transient(self::transient_user_country_rank($user_id), $rank);
        return $rank;
    }

    private static function calculate_user_rank($user_rank_data, $country_rank = false) {
        // user has no rank
        if (!is_array($user_rank_data) || empty($user_rank_data)) {
            return null;
        }

        global $wpdb;
        $table = self::$table;

        $query = "SELECT COUNT(1) FROM {$table}";
        $query .= " WHERE (1=1";
        if ($country_rank) {
            $query .= $wpdb->prepare(" AND country = %s", $user_rank_data['country']);
        }
        $query .= " AND (";
        // count users whose points is > current user points
        $query .= $wpdb->prepare("points > %d", $user_rank_data['points']);
        // count users whose points is = current user points but being recorded first
        $query .= $wpdb->prepare(" OR (points = %d AND updated_at < %s)", $user_rank_data['points'], $user_rank_data['updated_at']);
        $query .= ")"; // close end
        $query .= ")"; // close where

        $rank = $wpdb->get_var($query);
        $rank = is_numeric($rank) ? intval($rank) : 0;
        $rank += 1;

        return $rank;
    }

    public static function get_leaderboard($args = []) {
        $offset = isset($args['offset']) && is_numeric($args['offset']) ? intval($args['offset']) : 0;
        $offset = max(0, $offset);
        $per_page = 10;

        $country = sanitize_text_field($args['country'] ?? '');
        $country = strtoupper($country);

        global $wpdb;
        $table = self::$table;

        $query_select = "SELECT * FROM {$table}";
        $query_count = "SELECT COUNT(*) FROM {$table}";

        $query = " WHERE (1=1";
        if ($country) {
            $query .= $wpdb->prepare(" AND country = %s", $country);
        }
        $query .= ")";

        // count
        $count = $wpdb->get_var($query_count . $query);

        // pagination
        $query .= " ORDER BY points DESC, updated_at ASC, user_id DESC";
        $query .= " LIMIT {$offset}, {$per_page}";

        $data = $wpdb->get_results($query_select . $query, ARRAY_A);
        foreach ($data as $item_index => &$item) {
            $user_id = $item['user_id'];
            $user = get_user_by('ID', $user_id);
            $user_tier = TierSystem::get_user_tier($user_id);

            $item['rank'] = $offset + $item_index + 1;
            $item['name'] = $user->display_name;
            $item['avatar'] = UserDashboard::user_profile_image($user_id);
            $item['tier'] = [
                'slug' => $user_tier['slug'],
                'label' => $user_tier['label'],
            ];
            $item['total_badges'] = BadgeSystem::count_user_badges($user_id);
            $item['country'] = strtolower($item['country']);
        }

        // var_dump($wpdb->last_query, $wpdb->last_error); die;

        // return [
        //     [
        //         'rank' => 1,
        //         'country_code' => 'au',
        //         'name' => 'Adam Smith',
        //         'avatar' => get_template_directory_uri() . '/assets/images/avatar-160.jpg',
        //         'tier' => 'gold',
        //         'total_badges' => 12,
        //         'score' => 982,
        //     ]
        // ];
        return [$data, $count > $offset + count($data)];
    }

    public static function update_user_rank($user_id, $points) {
        global $wpdb;
        $table = self::$table;

        $row_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE user_id = %d", $user_id));

        // update if user exists
        if ($row_id) {
            $wpdb->update(
                $table,
                // data
                [
                    'user_id' => $user_id,
                    'country' => UserDashboard::get_user_country_code($user_id),
                    'points' => $points,
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                // where
                [
                    'id' => $row_id,
                ],
                // data format
                [
                    '%d', '%s', '%d', '%s',
                ],
                // where format
                [
                    '%d'
                ]
            );
        } else {
            // create new
            $wpdb->insert(
                $table,
                // data
                [
                    'user_id' => $user_id,
                    'country' => UserDashboard::get_user_country_code($user_id),
                    'points' => $points,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                // data format
                [
                    '%d', '%s', '%d', '%s', '%s',
                ]
            );
        }
    }
}
