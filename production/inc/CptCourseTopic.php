<?php

namespace STA\Inc;

use Carbon_Fields\Container;

class CptCourseTopic {

    private static $instance;
    public static $post_type = 'sfwd-topic';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'add_excerpts_to_topics']);
    }

    public function add_excerpts_to_topics() {
        add_post_type_support(self::$post_type, 'excerpt');
    }

    public static function register_fields() {
        Container::make('post_meta', 'Topic')
            ->where('post_type', '=', self::$post_type)
            ->add_fields([
                CptCourseLesson::field_text_to_speech(),
            ]);
    }

    /**
     * @param int|array $topic_id
     * @return int
     */
    public static function get_topic_quiz_count($topic_id) {
        return CptCourse::count_children($topic_id, 'lesson_id', CptCourseQuiz::$post_type);
    }
}
