<?php

namespace STA\Inc\CarbonFields;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class PageHomeAgent {
    public static function register_fields() {
        Container::make('post_meta', 'Homepage Agent Content')
            ->where('post_type', '=', 'page')
            ->where('post_template', '=', 'page-templates/homepage-agent.php')
            ->add_fields([
                Field::make('text', 'heading', 'Heading')
                    ->set_default_value('Welcome %s'),
                Field::make('rich_text', 'description', 'Description'),
                Field::make('separator', 's1', 'Continue Training'),
                Field::make('text', 'ct_heading', 'Heading')
                    ->set_default_value('Continue your Saudi Expert Training'),
                Field::make('text', 'ct_btn_label', 'Button Label')
                    ->set_default_value('Continue training'),
                Field::make('text', 'ct_btn_url', 'Button URL'),
                Field::make('separator', 's2', 'View Dashboard'),
                Field::make('text', 'vd_heading', 'Heading')
                    ->set_default_value('View your status and performance'),
                Field::make('text', 'vd_btn_label', 'Button Label')
                    ->set_default_value('View dashboard'),
                Field::make('text', 'vd_btn_url', 'Button URL'),
                Field::make('separator', 's3', 'Invite Colleagues'),
                Field::make('text', 'ic_heading', 'Heading')
                    ->set_default_value('Invite colleagues to Saudi Expert'),
                Field::make('text', 'ic_btn_label', 'Button Label')
                    ->set_default_value('Invite colleagues'),
                Field::make('text', 'ic_btn_url', 'Button URL'),
            ]);
    }

    public static function get_heading($post_id) {
        return carbon_get_post_meta($post_id, 'heading');
    }

    public static function get_description($post_id) {
        return carbon_get_post_meta($post_id, 'description');
    }

    public static function get_continue_training_heading($post_id) {
        return carbon_get_post_meta($post_id, 'ct_heading');
    }

    public static function get_continue_training_btn_label($post_id) {
        return carbon_get_post_meta($post_id, 'ct_btn_label');
    }

    public static function get_continue_training_btn_url($post_id) {
        return carbon_get_post_meta($post_id, 'ct_btn_url');
    }

    public static function get_view_dashboard_heading($post_id) {
        return carbon_get_post_meta($post_id, 'vd_heading');
    }

    public static function get_view_dashboard_btn_label($post_id) {
        return carbon_get_post_meta($post_id, 'vd_btn_label');
    }

    public static function get_view_dashboard_btn_url($post_id) {
        return carbon_get_post_meta($post_id, 'vd_btn_url');
    }

    public static function get_invite_colleagues_heading($post_id) {
        return carbon_get_post_meta($post_id, 'ic_heading');
    }

    public static function get_invite_colleagues_btn_label($post_id) {
        return carbon_get_post_meta($post_id, 'ic_btn_label');
    }

    public static function get_invite_colleagues_btn_url($post_id) {
        return carbon_get_post_meta($post_id, 'ic_btn_url');
    }
}
