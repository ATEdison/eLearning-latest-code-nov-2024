<?php

namespace STA\Inc;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class CptCourseLesson {

    private static $instance;
    public static $post_type = 'sfwd-lessons';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'add_excerpts_to_lessons']);
    }

    public function add_excerpts_to_lessons() {
        add_post_type_support(self::$post_type, 'excerpt');
    }

    public static function register_fields() {
        Container::make('post_meta', 'Lesson')
            ->where('post_type', '=', self::$post_type)
            ->add_fields([
                self::field_text_to_speech(),
            ]);
    }

    public static function field_text_to_speech() {
        ob_start();
        get_template_part('template-parts/admin/text-to-speech-input');
        $text_to_speech_input = ob_get_clean();
        return Field::make('html', 'text_to_speech', 'Text to speech')->set_html($text_to_speech_input);
    }

    public static function get_step_index($step_id, $course_id, $lesson_id) {
        $lesson_steps = learndash_course_get_children_of_step($course_id, $lesson_id);
        return [array_search($step_id, $lesson_steps) + 1, count($lesson_steps)];
    }

    public static function get_step_prev_next_link($step_id, $course_id, $lesson_id) {
        $lesson_steps = learndash_course_get_children_of_step($course_id, $lesson_id);
        $step_count = count($lesson_steps);
        $item_index = array_search($step_id, $lesson_steps);
        return [
            $item_index && $item_index > 0 ? get_permalink($lesson_steps[$item_index - 1]) : null,
            $item_index && $item_index + 1 < $step_count ? get_permalink($lesson_steps[$item_index + 1]) : null,
        ];
    }


    public static function get_lesson_index($course_id, $lesson_id) {
        $lesson_list = learndash_course_get_children_of_step($course_id, 0, self::$post_type);
        return [array_search($lesson_id, $lesson_list) + 1, count($lesson_list)];
    }

    public static function get_lesson_topic_count($lesson_id) {
        return CptCourse::count_children($lesson_id, 'lesson_id', CptCourseTopic::$post_type);
    }

    public static function lesson_summary($lesson_id) {
        $topic_count = \STA\Inc\CptCourseLesson::get_lesson_topic_count($lesson_id);
        $quiz_count = \STA\Inc\CptCourseLesson::get_lesson_quiz_count($lesson_id);

        $lesson_summary = [
            $topic_count ? sprintf(_n('%s Topic', '%s Topics', $topic_count, 'sta'), $topic_count) : '',
            $quiz_count ? sprintf(_n('%s Quiz', '%s Quizzes', $quiz_count, 'sta'), $quiz_count) : '',
        ];
        return array_filter($lesson_summary);
    }

    /**
     * @param int|array $lesson_id
     */
    public static function get_lesson_quiz_count($lesson_id) {
        $topic_list = CptCourse::get_children($lesson_id, CptCourseTopic::$post_type, 'lesson_id');
        $topic_list[] = $lesson_id;
        return CptCourseTopic::get_topic_quiz_count($topic_list);
    }

    public static function is_lesson_completed($user_id, $lesson_id) {
        return learndash_is_lesson_complete($user_id, $lesson_id);
    }

    /**
     * @see learndash_lesson_progress()
     */
    public static function get_user_lesson_progress($user_id, $lesson_id, $course_id) {
        $is_completed = \STA\Inc\CptCourseLesson::is_lesson_completed($user_id, $lesson_id);
        $topics = learndash_topic_dots($lesson_id, false, 'array', $user_id, $course_id);

        $progress = array(
            'total' => 0,
            'completed' => 0,
            'percentage' => 0,
            'is_completed' => $is_completed,
            'status' => '',
            'has_progress' => false,
        );

        foreach ($topics as $topic) {
            $progress['total']++;
            if (isset($topic->completed) && $topic->completed) {
                $progress['completed']++;
            }
        }

        if (0 !== absint($progress['completed'])) {
            $progress['percentage'] = floor($progress['completed'] / $progress['total'] * 100);
        }

        $is_in_progress = !$is_completed && 0 < $progress['percentage'];
        $progress['is_in_progress'] = $is_in_progress;

        if ($is_completed) {
            $progress['has_progress'] = true;
            $progress['status'] = 'completed';
        } else if ($is_in_progress) {
            $progress['has_progress'] = true;
            $progress['status'] = 'in_progress';
        }

        return $progress;
    }
}
