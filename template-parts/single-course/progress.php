<?php

/**
 * @var array $args
 */

$progress = $args['progress'];
$is_lapsed = $progress['is_lapsed'];
$extra_class = $args['class'] ?? '';
$extra_class = $extra_class ? ' ' . $extra_class : '';

$next_step_id = $progress['next_step_id'];
$is_in_progress = $progress['is_in_progress'];

// If a user hasn't yet started a module, hide the motivation screen
if (!$is_in_progress) {
    return;
}
?>

<div class="sta-course-progress<?php echo $extra_class; ?>">
    <h4><?php _e('Keep up the good work', 'sta'); ?></h4>
    <div>
        <span><?php printf(__("You've completed %s out of %s submodules so far.", 'sta'), $progress['completed'], $progress['total']); ?></span>
        <a href="<?php echo get_permalink($next_step_id); ?>"><?php _e('Continue your training.', 'sta'); ?></a>
    </div>
</div>
