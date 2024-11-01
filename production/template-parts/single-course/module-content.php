<?php

/**
 * @var array $args
 */

$user_id = $args['user_id'];
$course_id = $args['course_id'];
$progress = $args['progress'];

$section_list = learndash_30_get_course_sections($course_id);
// $lessons = learndash_course_get_lessons($course_id);
// printf('<pre>%s</pre>', var_export($lessons, true));
?>

<div class="sta-course-module-content">
<?php 
	   $user = get_userdata(get_current_user_id());
            // Get all the user roles for this user as an array.
            $user_roles = $user->roles;
            // print_r($user_roles);
            if(!in_array('stauser', $user_roles, true )) {

?>
    <div class="row justify-content-md-between align-items-md-center">
        <div class="col-12 col-md-auto">
            <h3 class="mb-32 mb-md-0"><?php _e('Module Content', 'sta'); ?></h3>
        </div>
        <div class="col-12 col-md-auto">
            <button
                type="button"
                class="btn btn-outline-green btn-expand-all w-100 w-md-auto"
                data-expand="<?php esc_attr_e('Expand all', 'sta'); ?>"
                data-collapse="<?php esc_attr_e('Collapse all', 'sta'); ?>"
            >
                <span></span>
            </button>
        </div>
    </div>

<?php 
}

?>

    <?php foreach ($section_list as $section): ?>
        <div class="course-module">
            <h4 class="mt-56 mb-44"><?php echo $section->post_title; ?></h4>
            <div class="course-module-step-list">
                <?php foreach ($section->steps as $step_id): ?>
                    <div class="mb-24">
                        <?php get_template_part('template-parts/single-course/module-step', '', [
                            'user_id' => $user_id,
                            'course_id' => $course_id,
                            'step_id' => $step_id,
                        ]); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
