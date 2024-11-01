<?php

namespace STA\Inc\CarbonFields;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class PageMyDashboard {
    public static function register_fields() {
        Container::make('post_meta', 'My Dashboard')
            ->where('post_type', '=', 'page')
            ->where('post_template', '=', 'page-templates/my-dashboard.php')
            ->add_fields([
                Field::make('separator', 's1', 'Leaderboard'),
                Field::make('text', 'leaderboard_heading', 'Heading')->set_default_value('Leaderboard'),
                Field::make('rich_text', 'leaderboard_desc', 'Description'),
            ]);
    }

    public static function get_leaderboard_heading($post_id) {
        return carbon_get_post_meta($post_id, 'leaderboard_heading');
    }

    public static function get_leaderboard_description($post_id) {
        return carbon_get_post_meta($post_id, 'leaderboard_desc');
    }
}
