<?php

/**
 * @var array $args
 */
$user_id = $args['user_id'] ?? '';
$course_id = $args['course_id'] ?? '';
$progress = $args['progress'] ?? '';
$is_lapsed = $progress['is_lapsed'];

$is_completed = $progress['is_completed'];
$is_in_progress = $progress['is_in_progress'];

$next_step_id = $progress['next_step_id'];
$thumbnail_id = get_post_thumbnail_id($course_id);
$summary_text = get_the_excerpt($course_id);

if (!$is_completed) {
    $thumbnail_id = has_post_thumbnail($next_step_id) ? get_post_thumbnail_id($next_step_id) : $thumbnail_id;
    $next_step_excerpt = get_the_excerpt($next_step_id);
    $summary_text = $next_step_excerpt ?: $summary_text;
}

$lesson_count = \STA\Inc\CptCourse::get_course_lesson_count($course_id);
$topic_count = \STA\Inc\CptCourse::get_course_topic_count($course_id);
$quiz_count = \STA\Inc\CptCourse::get_course_quiz_count($course_id);

$course_duration = \STA\Inc\CptCourse::get_duration($course_id);

$is_unlocked = learndash_is_course_prerequities_completed($course_id, $user_id);
?>
<div class="sta-course-summary">
    <div class="sta-course-summary-image"><?php echo wp_get_attachment_image($thumbnail_id, 'thumb_485x250'); ?></div>
    <div class="sta-course-summary-body p-24 p-md-40">
        <?php if (!$is_lapsed && $is_completed): ?>
            <div class="sta-course-summary-course-completed p-12">
                <?php _e('Module Complete', 'sta'); ?>
            </div>
        <?php elseif (!$is_unlocked): ?>
            <div class="d-flex align-items-center justify-content-center border rounded-5">
                <div class="course-progress-icon"></div>
            </div>
        <?php else:
            $btn_text = $is_in_progress ? __('Continue', 'sta') : __('Begin Module', 'sta'); ?>
            <a href="<?php echo get_permalink($next_step_id); ?>" class="btn btn-outline-green w-100"><?php echo $btn_text; ?></a>
        <?php endif; ?>
        <div class="mt-24 mb-32">
            <?php echo $summary_text; ?>
        </div>
        <div class="mb-30 py-30 border-y">
            <div class="sta-course-summary-lesson-count"><?php printf(_n('%s Lesson', '%s Lessons', $lesson_count, 'sta'), $lesson_count); ?></div>
            <div class="sta-course-summary-topic-count"><?php printf(_n('%s Topic', '%s Topics', $topic_count, 'sta'), $topic_count); ?></div>
            <div class="sta-course-summary-quiz-count"><?php printf(_n('%s Quiz', '%s Quizzes', $quiz_count, 'sta'), $quiz_count); ?></div>
        </div>
        <div>
            <?php printf(__('This module will take around <strong>%s</strong> to complete.', 'sta'), $course_duration); ?>
        </div>
    </div>
</div>
