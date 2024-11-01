<?php

/**
 * @var array $args
 * @see learndash_mark_complete()
 */
$user_id = $args['user_id'];
$course_id = $args['course_id'];
$step_id = $args['step_id'];
$extra_class = $args['class'] ?? '';

$unlocked = learndash_is_course_prerequities_completed($course_id, $user_id);
if (!$unlocked) {
    return;
}

$nonce = wp_create_nonce('sfwd_mark_complete_' . get_current_user_id() . '_' . $step_id);
?>


<form method="post" action="" class="<?php echo $extra_class; ?>">
    <input type="hidden" name="nn_learndash_mark_complete" value="yes">
    <input type="hidden" name="post" value="<?php echo $step_id; ?>">
    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
    <input type="hidden" name="sfwd_mark_complete" value="<?php echo $nonce; ?>">
    <button type="submit" class="btn btn-outline-green btn-mark-complete w-100 w-md-auto"><span><?php _e('Mark Complete and Continue', 'sta'); ?></span></button>
</form>
