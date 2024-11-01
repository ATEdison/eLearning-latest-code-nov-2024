<?php

namespace STA\Inc;

class CptCourseQuiz {

    private static $instance;
    public static $post_type = 'sfwd-quiz';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'add_excerpts_to_quizzes']);
    }

    public function add_excerpts_to_quizzes() {
        add_post_type_support(self::$post_type, 'excerpt');
    }
}
