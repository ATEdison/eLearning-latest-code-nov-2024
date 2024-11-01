<?php
/**
 * @var array $args
 */

$user_id = $args['user_id'];
$course_id = $args['course_id'];
$lesson_id = $args['step_id'];

$lesson_attrs = learndash_get_lesson_attributes($lesson_id);

$lesson_summary = \STA\Inc\CptCourseLesson::lesson_summary($lesson_id);

$progress = \STA\Inc\CptCourseLesson::get_user_lesson_progress($user_id, $lesson_id, $course_id);
$is_completed = $progress['is_completed'];
$is_in_progress = $progress['is_in_progress'];

$attributes = [
    $is_in_progress ? sprintf('data-percentage="%s"', $progress['percentage']) : '',
];
$attributes = array_filter($attributes);
$attributes = array_unique($attributes);
$attributes = implode(' ', $attributes);
$attributes = $attributes ? ' ' . $attributes : '';

$extra_class = [];
$lesson_progress_class = [];

if ($is_completed) {
    $extra_class[] = 'completed';
    $lesson_progress_class[] = 'completed';
}

if ($is_in_progress) {
    $extra_class[] = 'in-progress';
    $lesson_progress_class[] = 'in-progress';
}

$extra_class = implode(' ', $extra_class);
$extra_class = $extra_class ? ' ' . $extra_class : '';

$lesson_progress_class = implode(' ', $lesson_progress_class);
$lesson_progress_class = $lesson_progress_class ? ' ' . $lesson_progress_class : '';

?>

<div class="course-module-step-item<?php echo $extra_class; ?>"<?php echo $attributes; ?>>
    <div class="course-module-step-item-heading ps-25 pe-60 py-30">
        <div class="row gx-50 justify-content-md-between flex-md-nowrap">
            <div class="col-12 col-md-auto mb-8 mb-md-0">
                <h5 class="mb-0 course-module-step-item-heading-title"><a href="<?php echo get_permalink($lesson_id); ?>"><?php echo get_the_title($lesson_id); ?></a></h5>
            </div>
            <div class="col-12 col-md-auto d-flex align-items-center">
                <ul class="course-module-step-item-summary ps-20 ps-md-0">
                    <?php foreach ($lesson_summary as $item): ?>
                        <li><?php echo $item; ?></li>
                    <?php endforeach; ?>
                </ul>
                <div class="course-module-step-item-progress<?php echo $lesson_progress_class; ?>"></div>
            </div>
        </div>
    </div>
  <?php 
       if(str_replace(' ', '', $extra_class) == "completed") {
	
    ?>
    <div class="course-module-step-item-content">
        <div class="px-25 pb-30">
            <?php get_template_part('template-parts/single-course/lesson-steps', '', [
                'user_id' => $user_id,
                'course_id' => $course_id,
                'lesson_id' => $lesson_id,
                'progress' => $progress,
            ]); ?>
        </div>
    </div>
 <?php
            } else {
            ?>

<style>
.course-module-step-item-heading-title:before{
	transform: rotate(-90deg) !important;
}

</style>

<?php
	}
?>
</div>
