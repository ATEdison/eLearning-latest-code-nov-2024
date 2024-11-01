<?php


global $post;

$user_id = get_current_user_id();
$course_id = $post->ID;

$course_content = $post->post_content;
if (has_blocks($course_content)) {
    $blocks = parse_blocks($course_content);
    // only render banner for logged-in users
    if ($blocks[0]['blockName'] == 'carbon-fields/sta-hero-banner') {
        echo render_block($blocks[0]);
    }
}

$progress = \STA\Inc\CptCourse::get_user_progress($user_id, $course_id);
$is_completed = $progress['is_completed'];
?>

<div class="sta-course py-32 py-md-50 py-lg-80">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-4 order-lg-2 mb-32 mb-lg-0">
                <?php get_template_part('template-parts/single-course/summary', '', [
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'progress' => $progress,
                ]); ?>
            </div>
            <div class="col-12 col-lg-8 order-lg-1">
                <?php
                // progress
                get_template_part('template-parts/single-course/progress', $is_completed ? 'completed' : '', [
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'progress' => $progress,
                    'class' => 'mb-40',
                ]);

                // last activity
                get_template_part('template-parts/single-course/last-activity', '', [
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'progress' => $progress,
                    'class' => 'mb-40',
                ]);

                // tabs
                get_template_part('template-parts/single-course/tabs', '', [
                    'course_id' => $course_id,
                    'class' => 'mb-40',
                ]);

                // module content
                get_template_part('template-parts/single-course/module-content', '', [
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'progress' => $progress,
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<?php
// references for dev
/**
 * @var SFWD_CPT_Instance $sfwd_cpt_instance
 */
// $sfwd_cpt_instance = SFWD_CPT_Instance::$instances[\STA\Inc\CptCourse::$post_type];
// echo $sfwd_cpt_instance->template_content('');
?>
