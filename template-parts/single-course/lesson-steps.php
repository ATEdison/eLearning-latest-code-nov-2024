<?php

/**
 * @var array $args
 */
$user_id = $args['user_id'];
$course_id = $args['course_id'];
$lesson_id = $args['lesson_id'];
$progress = $args['progress'];

$lesson_steps = learndash_course_get_children_of_step($course_id, $lesson_id);
// printf('<pre>%s</pre>', var_export($lesson_steps, true));
?>

<div>
    <div class="row justify-content-md-between mb-20">
        <div class="col-12 col-md-auto mb-0 mb-md-0">
            <h6 class="mb-0"><?php _e('Lesson Content', 'sta'); ?></h6>
        </div>
        <div class="col-12 col-md-auto fw-500">
            <span><?php printf(__('%s Complete', 'sta'), $progress['percentage'] . '%'); ?></span>
            <span class="ms-20"><?php printf(_n('%1$s/%2$s Step', '%1$s/%2$s Steps', $progress['total'], 'sta'), $progress['completed'], $progress['total']); ?></span>
        </div>
    </div>
    <?php if (is_array($lesson_steps) && !empty($lesson_steps)): ?>
        <ul class="course-module-step-item-steps">
            <?php foreach ($lesson_steps as $step_id):
                $is_step_completed = learndash_is_item_complete($step_id, $user_id, $course_id); ?>
                <li><a href="<?php echo get_permalink($step_id); ?>"><?php echo get_the_title($step_id); ?><span class="<?php echo $is_step_completed ? 'completed' : ''; ?>"></span></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
