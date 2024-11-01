<?php

/**
 * @var array $args
 */

$course_id = $args['course_id'];
$extra_class = $args['class'] ?? '';
$extra_class = $extra_class ? ' ' . $extra_class : '';

$course_settings = learndash_get_setting($course_id);
// printf('<pre>%s</pre>', var_export($course_settings, true));
$course_materials = $course_settings['course_materials'] ?? '';
?>

<div class="course-tabs<?php echo $extra_class; ?>">
    <ul class="nav nav-tabs d-flex" id="course_tab" role="tablist">
        <li class="nav-item flex-grow-1" role="presentation">
            <button class="nav-link active w-100 nav-link-course" id="course_tab_nav_desc" data-bs-toggle="tab" data-bs-target="#course_tab_content_desc" type="button" role="tab" aria-controls="course_tab_content_desc" aria-selected="true">
                <?php _e('Course', 'sta'); ?>
            </button>
        </li>
        <?php if ($course_materials): ?>
            <li class="nav-item flex-grow-1" role="presentation">
                <button class="nav-link w-100 nav-link-materials" id="course_tab_nav_materials" data-bs-toggle="tab" data-bs-target="#course_tab_content_materials" type="button" role="tab" aria-controls="course_tab_content_materials" aria-selected="false">
                    <?php _e('Materials', 'sta'); ?>
                </button>
            </li>
        <?php endif; ?>
    </ul>
    <div class="tab-content py-24 py-lg-40" id="course_tab_content">
        <div class="tab-pane fade show text-content active" id="course_tab_content_desc" role="tabpanel" aria-labelledby="course_tab_nav_desc">
            <?php echo wpautop(do_shortcode(\STA\Inc\CptCourse::get_description($course_id))); ?>
        </div>
        <?php if ($course_materials): ?>
            <div class="tab-pane fade text-content" id="course_tab_content_materials" role="tabpanel" aria-labelledby="course_tab_nav_materials">
                <?php echo $course_materials; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
