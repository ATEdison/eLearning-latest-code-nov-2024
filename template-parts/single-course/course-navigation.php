<?php
/**
 * @var array $args
 */

$course_id = $args['course_id'];
$step_id = get_queried_object_id();
$step_parent_id = learndash_course_get_single_parent_step($course_id, $step_id, \STA\Inc\CptCourseLesson::$post_type);

$user_id = get_current_user_id();
$progress = \STA\Inc\CptCourse::get_user_progress($user_id, $course_id);

$section_list = learndash_30_get_course_sections($course_id);
?>

<div class="sta-course-navigation">
    <div class="sta-course-navigation-inner p-24 p-md-40">
        <div class="mb-32">
            <a class="sta-course-navigation-back" href="<?php echo get_permalink($course_id); ?>"><?php _e('Back to module', 'sta'); ?></a>
        </div>
        <h3 class="fs-27"><?php echo get_the_title($course_id); ?></h3>

        <!-- last activity -->
        <?php get_template_part('template-parts/single-course/last-activity', '', [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'progress' => $progress,
            'is_sidebar' => true,
            'class' => 'mb-35',
        ]); ?>

        <!-- table of content -->
        <div class="sta-course-navigation-toc pb-32 border-bottom">
            <ul>
                <?php foreach ($section_list as $section): ?>
                    <li class="sta-course-navigation-toc-section">
                        <div class="fw-500 mb-12 fs-16"><?php echo $section->post_title; ?></div>
                        <ul>
                            <?php foreach ($section->steps as $lesson_id):
                                $lesson_steps = learndash_course_get_children_of_step($course_id, $lesson_id);
                                $lesson_summary = \STA\Inc\CptCourseLesson::lesson_summary($lesson_id);

                                $lesson_progress = \STA\Inc\CptCourseLesson::get_user_lesson_progress($user_id, $lesson_id, $course_id);
                                $is_completed = $lesson_progress['is_completed'];
                                $is_in_progress = $lesson_progress['is_in_progress'];
                                $lesson_progress_class = $is_completed ? 'completed' : ($is_in_progress ? 'in-progress' : '');
                                $lesson_progress_class = $lesson_progress_class ? ' ' . $lesson_progress_class : '';

                                $lesson_class = [
                                    is_single($lesson_id) || $step_parent_id == $lesson_id ? 'expanded' : '',
                                ];
                                $lesson_class = array_filter($lesson_class);
                                $lesson_class = array_unique($lesson_class);
                                $lesson_class = implode(' ', $lesson_class);
                                $lesson_class = $lesson_class ? ' ' . $lesson_class : '';
                                ?>
                                <li class="sta-course-navigation-toc-lesson<?php echo $lesson_class; ?>">
                                    <div class="sta-course-navigation-toc-lesson-heading">
                                        <div class="sta-course-navigation-toc-lesson-heading-inner">
                                            <button class="btn-expand-toc-lesson"></button>
                                            <a href="<?php echo get_permalink($lesson_id); ?>"><?php echo get_the_title($lesson_id); ?></a>
                                            <?php if (is_array($lesson_summary) && !empty($lesson_summary)): ?>
                                                <ul class="course-module-step-item-summary">
                                                    <?php foreach ($lesson_summary as $item): ?>
                                                        <li><?php echo $item; ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                            <div class="sta-course-navigation-toc-progress<?php echo $lesson_progress_class; ?>"></div>
                                        </div>
                                    </div>
                                    <div class="sta-course-navigation-toc-lesson-steps">
                                        <ul>
                                            <?php foreach ($lesson_steps as $lesson_step_id):
                                                $is_step_completed = learndash_user_progress_is_step_complete($user_id, $course_id, $lesson_step_id);
                                                $lesson_step_post_type = get_post_type($lesson_step_id);
                                                $step_class = [
                                                    $lesson_step_post_type == \STA\Inc\CptCourseTopic::$post_type ? 'topic' : '',
                                                    $lesson_step_post_type == \STA\Inc\CptCourseQuiz::$post_type ? 'quiz' : '',
                                                    $step_id == $lesson_step_id ? 'active' : '',
                                                ];
                                                $step_class = array_filter($step_class);
                                                $step_class = array_unique($step_class);
                                                $step_class = implode(' ', $step_class);
                                                ?>
                                                <li class="<?php echo $step_class; ?>">
                                                    <a href="<?php echo get_permalink($lesson_step_id); ?>"><?php echo get_the_title($lesson_step_id); ?></a>
                                                    <div class="sta-course-navigation-toc-progress<?php echo $is_step_completed ? ' completed' : ''; ?>"></div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- quizzes -->
        <div class="py-32 border-bottom sta-course-navigation-quizzes">
            <div class="fw-500 mb-30 fs-16"><?php _e('Quizzes', 'sta'); ?></div>
            <?php $quizzes = learndash_get_course_quiz_list($course_id, $user_id); ?>
            <ul>
                <?php foreach ($quizzes as $quiz): ?>
                    <li>
                        <?php // printf('<pre>%s</pre>', var_export($quiz, true)); ?>
                        <a href="<?php echo get_permalink($quiz['post']); ?>"><?php echo $quiz['post']->post_title; ?></a>
                        <div class="sta-course-navigation-toc-progress<?php echo $quiz['status'] == 'completed' ? ' completed' : ''; ?>"></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
