<?php


global $post;

$user_id = get_current_user_id();
$quiz_id = $post->ID;
$course_id = learndash_get_course_id($quiz_id, true);
$lesson_id = learndash_course_get_single_parent_step($course_id, $quiz_id, \STA\Inc\CptCourseLesson::$post_type);

$course_content = get_post_field('post_content', $course_id);
if (has_blocks($course_content)) {
    $blocks = parse_blocks($course_content);
    // only render banner for logged-in users
    if ($blocks[0]['blockName'] == 'carbon-fields/sta-hero-banner') {
        echo render_block($blocks[0]);
    }
}

$is_completed = learndash_user_progress_is_step_complete($user_id, $course_id, $quiz_id);
$progress_status = $is_completed ? 'completed' : 'in_progress';

[$prev_link, $next_link] = \STA\Inc\CptCourseLesson::get_step_prev_next_link($quiz_id, $course_id, $lesson_id);

[$quiz_index, $quiz_count] = \STA\Inc\CptCourseLesson::get_step_index($quiz_id, $course_id, $lesson_id);

$qz_cnt = \STA\Inc\CptCourse::get_course_quiz_count($course_id);
$lesn_cnt = \STA\Inc\CptCourse::get_course_lesson_count($course_id);

$breadcrumb_data = [
    [
        'title' => get_the_title($course_id),
        'url' => get_permalink($course_id),
    ],
];

if ($lesson_id) {
    $breadcrumb_data[] = [
        'title' => get_the_title($lesson_id),
        'url' => get_permalink($lesson_id),
    ];
}

$breadcrumb_data[] = [
    'title' => get_the_title($quiz_id),
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
                        <div class="fs-18 fw-500"><?php printf(__('Step %1$s of %2$s', 'sta'), $quiz_index, ($qz_cnt + $lesn_cnt)); ?></div>
                    </div>
                    <div class="col-auto d-flex align-items-center">
                        <div class="sta-status-label <?php echo $progress_status; ?>"><?php echo \STA\Inc\CptCourse::get_course_status_label($progress_status); ?></div>
                        <?php
                        // if ($prev_link) {
                        //     printf('<a href="%1$s" class="btn sta-prev-post-link me-8"></a>', $prev_link);
                        // }
                        // if ($next_link) {
                        //     printf('<a href="%1$s" class="btn sta-next-post-link"></a>', $next_link);
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
                </div>
                <div class="text-content mb-40">
                    <?php the_content(); ?>
                </div>
                <?php
                // quiz
                /**
                 * @var SFWD_CPT_Instance $sfwd_cpt_instance
                 * @see learndash_quiz_shortcode
                 */
                // $sfwd_cpt_instance = SFWD_CPT_Instance::$instances[\STA\Inc\CptCourseQuiz::$post_type];
                // echo $sfwd_cpt_instance->template_content('');
                $quiz_pro_id = get_post_meta($post->ID, 'quiz_pro_id', true);
                $quiz_pro_id = absint($quiz_pro_id);
                if (empty($quiz_pro_id)) {
                    $quiz_settings = learndash_get_setting($post->ID);
                    if (isset($quiz_settings['quiz_pro'])) {
                        $quiz_settings['quiz_pro'] = absint($quiz_settings['quiz_pro']);
                        if (!empty($quiz_settings['quiz_pro'])) {
                            $quiz_pro_id = $quiz_settings['quiz_pro'];
                        }
                    }
                }

                $content = wptexturize(
                // do_shortcode( '[ld_quiz quiz_id="' . $post->ID . '" course_id="' . absint( $course_id ) . '" quiz_pro_id="' . absint( $quiz_pro_id ) . '"]' )
                    learndash_quiz_shortcode(
                        array(
                            'quiz_id' => $post->ID,
                            'course_id' => absint($course_id),
                            'quiz_pro_id' => absint($quiz_pro_id),
                        ),
                        '',
                        true
                    )
                );
                echo $content;
                ?>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('template-parts/tmpl-quiz-result', '', [
    'user_id' => get_current_user_id(),
    'quiz_id' => $quiz_id,
]); ?>
