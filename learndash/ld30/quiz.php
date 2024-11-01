<?php
/**
 * LearnDash LD30 Displays a quiz.
 *
 * Available Variables:
 *
 * @var int $course_id : (int) ID of the course
 * @var WP_Post $course : (object) Post object of the course
 * @var array $course_settings : (array) Settings specific to current course
 * @var string $course_status : Course Status
 * @var bool $has_access : User has access to course or is enrolled.
 *
 * @var array $courses_options : Options/Settings as configured on Course Options page
 * @var array $lessons_options : Options/Settings as configured on Lessons Options page
 * @var array $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * @var int $user_id : (object) Current User ID
 * @var bool $logged_in : (true/false) User is logged in
 * @var WP_User $current_user : (object) Currently logged in user object
 * @var WP_Post $post : (object) The quiz post object () (Deprecated in LD 3.1. User $quiz_post instead).
 * @var WP_Post $quiz_post : (object) The quiz post object ().
 * @var bool $lesson_progression_enabled : (true/false)
 * @var bool $show_content : (true/false) true if user is logged in and lesson progression is disabled or if previous lesson and topic is completed.
 * @var bool $attempts_left : (true/false)
 * @var int $attempts_count : (integer) No of attempts already made
 * @var array $quiz_settings : (array)
 * @var bool $bypass_course_limits_admin_users
 * @var string $content
 * @var string $materials
 * @var string $quiz_content
 *
 * Note:
 *
 * To get lesson/topic post object under which the quiz is added:
 * $lesson_post = !empty($quiz_settings["lesson"])? get_post($quiz_settings["lesson"]):null;
 *
 * @since 2.1.0
 *
 * @package LearnDash\Templates\LD30
 */

if (!defined('ABSPATH')) {
    exit;
}

if ((!isset($quiz_post)) || (!is_a($quiz_post, 'WP_Post'))) {
    return;
}
?>
<div class="<?php learndash_the_wrapper_class(); ?>">
    <?php
    /**
     * Fires before the quiz content starts.
     *
     * @param int $quiz_id Quiz ID.
     * @param int $course_id Course ID.
     * @param int $user_id User ID.
     * @since 3.0.0
     *
     */
    do_action('learndash-quiz-before', $quiz_post->ID, $course_id, $user_id);

    // learndash_get_template_part(
    //     'modules/infobar.php',
    //     array(
    //         'context' => 'quiz',
    //         'course_id' => $course_id,
    //         'user_id' => $user_id,
    //         'post' => $quiz_post,
    //     ),
    //     true
    // );

    if (!empty($lesson_progression_enabled)) :
        $last_incomplete_step = learndash_is_quiz_accessable($user_id, $quiz_post, true, $course_id);
        if (!empty($user_id)) {
            if (learndash_user_progress_is_step_complete($user_id, $course_id, $quiz_post->ID)) {
                $show_content = true;
            } else {
                if ($bypass_course_limits_admin_users) {
                    remove_filter('learndash_content', 'lesson_visible_after', 1, 2);
                    $previous_lesson_completed = true;
                } else {
                    $previous_step_post_id = learndash_user_progress_get_previous_incomplete_step($user_id, $course_id, $quiz_post->ID);
                    $previous_lesson_completed = true;
                    if ((!empty($previous_step_post_id)) && ($previous_step_post_id !== $quiz_post->ID)) {
                        $previous_lesson_completed = false;

                        $last_incomplete_step = get_post($previous_step_post_id);
                    }

                    /**
                     * Filter to override previous step completed.
                     *
                     * @param bool $previous_lesson_completed True if previous step completed.
                     * @param int $step_id Step Post ID.
                     * @param int $user_id User ID.
                     */
                    $previous_lesson_completed = apply_filters('learndash_previous_step_completed', $previous_lesson_completed, $quiz_post->ID, $user_id);
                }

                $show_content = $previous_lesson_completed;
            }

            if (learndash_is_sample($quiz_post)) {
                $show_content = true;
            } elseif (($last_incomplete_step) && (is_a($last_incomplete_step, 'WP_Post'))) {
                $show_content = false;

                $sub_context = '';
                if ('on' === learndash_get_setting($last_incomplete_step->ID, 'lesson_video_enabled')) {
                    if (!empty(learndash_get_setting($last_incomplete_step->ID, 'lesson_video_url'))) {
                        if ('BEFORE' === learndash_get_setting($last_incomplete_step->ID, 'lesson_video_shown')) {
                            if (!learndash_video_complete_for_step($last_incomplete_step->ID, $course_id, $user_id)) {
                                $sub_context = 'video_progression';
                            }
                        }
                    }
                }

                /**
                 * Fires before the quiz progression.
                 *
                 * @param int $quiz_id Quiz ID.
                 * @param int $course_id Course ID.
                 * @param int $user_id User ID.
                 * @since 3.0.0
                 *
                 */
                do_action('learndash-quiz-progression-before', $quiz_post->ID, $course_id, $user_id);

                learndash_get_template_part(
                    'modules/messages/lesson-progression.php',
                    array(
                        'previous_item' => $last_incomplete_step,
                        'course_id' => $course_id,
                        'user_id' => $user_id,
                        'context' => 'quiz',
                        'sub_context' => $sub_context,
                    ),
                    true
                );

                /**
                 * Fires after the quiz progress.
                 *
                 * @param int $quiz_id Quiz ID.
                 * @param int $course_id Course ID.
                 * @param int $user_id User ID.
                 * @since 3.0.0
                 *
                 */
                do_action('learndash-quiz-progression-after', $quiz_post->ID, $course_id, $user_id);

            }
        } else {
            $show_content = true;
        }
    endif;

    if ($show_content) :

        /**
         * Content and/or tabs
         */
        learndash_get_template_part(
            'modules/tabs.php',
            array(
                'course_id' => $course_id,
                'post_id' => $quiz_post->ID,
                'user_id' => $user_id,
                'content' => $content,
                'materials' => $materials,
                'context' => 'quiz',
            ),
            true
        );

        if ($attempts_left) :

            /**
             * Fires before the actual quiz content (not WP_Editor content).
             *
             * @param int $quiz_id Quiz ID.
             * @param int $course_id Course ID.
             * @param int $user_id User ID.
             * @since 3.0.0
             *
             */
            do_action('learndash-quiz-actual-content-before', $quiz_post->ID, $course_id, $user_id);

            echo $quiz_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Post content

            /**
             * Fires after the actual quiz content (not WP_Editor content).
             *
             * @param int $quiz_id Quiz ID.
             * @param int $course_id Course ID.
             * @param int $user_id User ID.
             * @since 3.0.0
             *
             */
            do_action('learndash-quiz-actual-content-after', $quiz_post->ID, $course_id, $user_id);

        else :

            /**
             * Display an alert
             */

            /**
             * Fires before the quiz attempts alert.
             *
             * @param int $quiz_id Quiz ID.
             * @param int $course_id Course ID.
             * @param int $user_id User ID.
             * @since 3.0.0
             *
             */
            do_action('learndash-quiz-attempts-alert-before', $quiz_post->ID, $course_id, $user_id);

            learndash_get_template_part(
                'modules/alert.php',
                array(
                    'type' => 'warning',
                    'icon' => 'alert',
                    'message' => sprintf(
                    // translators: placeholders: quiz, attempts count.
                        esc_html_x('You have already taken this %1$s %2$d time(s) and may not take it again.', 'placeholders: quiz, attempts count', 'learndash'),
                        learndash_get_custom_label_lower('quiz'),
                        $attempts_count
                    ),
                ),
                true
            );

            /**
             * Fires after the quiz attempts alert.
             *
             * @param int $quiz_id Quiz ID.
             * @param int $course_id Course ID.
             * @param int $user_id User ID.
             * @since 3.0.0
             *
             */
            do_action('learndash-quiz-attempts-alert-after', $quiz_post->ID, $course_id, $user_id);

        endif;
    endif;

    /**
     * Fires before the quiz content starts.
     *
     * @param int $quiz_id Quiz ID.
     * @param int $course_id Course ID.
     * @param int $user_id User ID.
     * @since 3.0.0
     *
     */
    do_action('learndash-quiz-after', $quiz_post->ID, $course_id, $user_id);
    learndash_load_login_modal_html();
    ?>

</div> <!--/.learndash-wrapper-->
