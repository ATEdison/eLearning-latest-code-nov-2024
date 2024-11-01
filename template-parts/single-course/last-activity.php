<?php

/**
 * @var array $args
 */

$user_id = $args['user_id'];
$course_id = $args['course_id'];
$progress = $args['progress'];
$is_sidebar = $args['is_sidebar'] ?? false;

$extra_class = [
    $args['class'] ?? '',
    $is_sidebar ? 'sidebar pb-30 border-bottom' : 'p-24 p-md-40',
];
$extra_class = array_filter($extra_class);
$extra_class = array_unique($extra_class);
$extra_class = implode(' ', $extra_class);
$extra_class = $extra_class ? ' ' . $extra_class : '';

$last_activity = \STA\Inc\CptCourse::get_user_last_activity($user_id, $course_id);
?>
<div class="sta-course-last-activity<?php echo $extra_class; ?>">
    <div class="mb-8">
        <?php get_template_part('template-parts/course-progress', '', ['progress' => $progress]); ?>
    </div>
    <?php if ($last_activity): ?>
        <div class="sta-course-last-activity-text fs-14"><?php printf(__('Last activity on %s', 'sta'), date_i18n('F d, Y', $last_activity)); ?></div>
    <?php endif; ?>
</div>
