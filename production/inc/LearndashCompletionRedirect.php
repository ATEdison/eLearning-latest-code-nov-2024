<?php

namespace STA\Inc;

class LearndashCompletionRedirect {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('learndash_completion_redirect', [$this, 'learndash_completion_redirect'], PHP_INT_MAX, 2);
    }

    /**
     * @param string $link
     * @param int $post_id
     * @return string
     * @see learndash_get_next_lesson_redirect()
     */
    public function learndash_completion_redirect($link, $post_id) {
        if (!isset($_POST['nn_learndash_mark_complete'])) {
            return $link;
        }

        $course_id = learndash_get_course_id($post_id);

        // clicking next from a lesson will redirect user to the first lesson step
        if (get_post_type($post_id) == CptCourseLesson::$post_type) {
            $lesson_steps = learndash_course_get_children_of_step($course_id, $post_id);
            $first_step_id = $lesson_steps[0] ?? null;

            if ($first_step_id) {
                return get_permalink($first_step_id);
            }

            return $link;
        }

        // clicking next from a lesson step will redirect user to the lesson next step
        $lesson_id = learndash_course_get_single_parent_step($course_id, $post_id);
        $lesson_steps = learndash_course_get_children_of_step($course_id, $lesson_id);

        $next_step_id = $lesson_steps[array_search($post_id, $lesson_steps) + 1] ?? null;

        // has next step
        if ($next_step_id) {
            return get_permalink($next_step_id) . '#course-content';
        }

        // $post_id is the lesson last step, redirect user to the next lesson
        $next = learndash_next_post_link('', true, get_post($lesson_id));
        if ($next) {
            return $next;
        }

        // has no next lesson, attempt to redirect user to the course quiz
        $user_id = is_user_logged_in() ? get_current_user_id() : null;
        $quizzes = learndash_get_course_quiz_list($course_id, $user_id);
        $first_quiz = $quizzes[0] ?? null;
        $first_quiz_post = $first_quiz['post'] ?? null;
        if ($first_quiz_post instanceof \WP_Post) {
            return get_permalink($first_quiz_post);
        }

        // no next step, redirect user to the course page
        return get_permalink($course_id);
    }
}
