<?php


global $post;

$user_id = get_current_user_id();
$lesson_id = $post->ID;
$course_id = learndash_get_course_id($lesson_id, true);

$course_content = get_post_field('post_content', $course_id);
if (has_blocks($course_content)) {
    $blocks = parse_blocks($course_content);
    // only render banner for logged-in users
    if ($blocks[0]['blockName'] == 'carbon-fields/sta-hero-banner') {
        echo render_block($blocks[0]);
    }
}

$progress = \STA\Inc\CptCourseLesson::get_user_lesson_progress($user_id, $lesson_id, $course_id);
$is_completed = $progress['is_completed'];
$progress_status = $is_completed ? 'completed' : 'in_progress';

$prev_lesson_id = learndash_previous_post_link(null, 'id', $post);
$next_lesson_id = learndash_next_post_link(null, 'id', $post);

[$lesson_index, $lesson_count] = \STA\Inc\CptCourseLesson::get_lesson_index($course_id, $lesson_id);

$qz_cnt = \STA\Inc\CptCourse::get_course_quiz_count($course_id);
$lesn_cnt = \STA\Inc\CptCourse::get_course_lesson_count($course_id);


$breadcrumb_data = [
    [
        'title' => get_the_title($course_id),
        'url' => get_permalink($course_id),
    ],
    [
        'title' => get_the_title($lesson_id),
    ],
];
?>
<div class="sta-lesson w-100 d-lg-flex align-items-lg-stretch">
    <!-- breadcrumb mobile -->
    <div class="d-lg-none">
        <div class="container-fluid">
            <?php get_template_part('template-parts/breadcrumb', '', [
                'enable_mobile_toggle_course_navigation' => true,
                'data' => $breadcrumb_data,
            ]); ?>
        </div>
    </div>
    <div class="container-fluid sta-course-container">
        <?php get_template_part('template-parts/single-course/course-navigation', '', [
            'course_id' => $course_id,
        ]); ?>
        <div class="sta-course-content" id="course-content">
            <!-- breadcrumb -->
            <div>
                <?php get_template_part('template-parts/breadcrumb', '', [
                    'data' => $breadcrumb_data,
                    'class' => 'd-none d-lg-block',
                ]); ?>
            </div>

            <!-- progress -->
            <div class="sta-lesson-navigation py-24 py-md-30 py-lg-40 mb-24 mb-md-0">
                <div class="row justify-content-between align-items-center">
                    <div class="col-auto">
			 <div class="fs-18 fw-500"><?php printf(__('Step %1$s of %2$s', 'sta'), $lesson_index, ($qz_cnt + $lesn_cnt));?></div>
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <div class="sta-status-label <?php echo $progress_status; ?>"><?php echo \STA\Inc\CptCourse::get_course_status_label($progress_status); ?></div>
                        <?php
                        // if ($prev_lesson_id) {
                        //     printf('<a href="%1$s" class="btn sta-prev-post-link me-8"></a>', get_permalink($prev_lesson_id));
                        // }
                        // if ($next_lesson_id) {
                        //     printf('<a href="%1$s" class="btn sta-next-post-link"></a>', get_permalink($next_lesson_id));
                        // }
                        ?>
                    </div>
                </div>
            </div>

            <!-- content -->
            <div class="sta-lesson-content">
                <!-- title -->
                <h3 class="mb-16 mb-lg-24"><?php the_title(); ?></h3>

                <!-- meta -->
                <div class="sta-lesson-meta row mb-30 align-items-center justify-content-between">
                    <div class="col-12 col-md-auto sta-lesson-meta-date mb-16 mb-md-0"><?php echo date_i18n(__('F d, Y', 'sta'), $post->post_date_gmt); ?></div>
                    <div class="col-12 col-md-auto">
                        <ul>
                            <?php $audio_url = \STA\Inc\GoogleTextToSpeech::get_post_audio($lesson_id);
                            if ($audio_url): ?>
                                <li class="sta-lesson-meta-item sta-lesson-meta-item-listen">
                                    <a class="sta-audio" href="#" data-src="<?php echo htmlentities($audio_url); ?>"><?php _e('Listen', 'sta'); ?></a>
                                </li>
                            <?php endif; ?>
                            <li class="sta-lesson-meta-item sta-lesson-meta-item-download">
                                <a target="_blank" href="<?php echo \STA\Inc\PDFConverter::download_url($lesson_id); ?>"><?php _e('Download', 'sta'); ?></a>
                            </li>
                            <!--<li class="sta-lesson-meta-item sta-lesson-meta-item-favorite"><a href="#"><?php /*_e('Favorite', 'sta'); */ ?></a></li>-->
                        </ul>
                    </div>
                </div>
                <div class="mb-40 text-content">
                    <?php the_content(); ?>
                </div>

                <!-- steps -->
                <!-- <div class="mb-40 border-bottom pb-20">
                    <?php // get_template_part('template-parts/single-course/lesson-steps', '', ['user_id' => $user_id,'course_id' => $course_id,'lesson_id' => $lesson_id,'progress' => $progress,]); ?>
                </div> -->

                <!-- mark complete -->
                <?php get_template_part('template-parts/single-course/mark-complete', '', [
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'step_id' => $lesson_id,
                    'class' => 'text-md-end',
                ]); ?>
            </div>
        </div>
    </div>
</div>

<?php
// references for dev
/**
 * @var SFWD_CPT_Instance $sfwd_cpt_instance
 */
// $sfwd_cpt_instance = SFWD_CPT_Instance::$instances[\STA\Inc\CptCourseLesson::$post_type];
// echo $sfwd_cpt_instance->template_content('');
?>
