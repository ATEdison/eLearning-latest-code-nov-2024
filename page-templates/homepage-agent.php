<?php
/**
 * Template Name: Homepage - Agent
 */

get_header(); ?>

<?php if (!is_user_logged_in()): ?>
    <div class="py-200">
        <div class="container">Unauthorized</div>
    </div>
<?php else:
    $post_id = get_the_ID();

    $user = wp_get_current_user();
    $user_id = $user->ID;

    // @TODO: do not refresh user_tier here
    $user_tier = \STA\Inc\TierSystem::get_user_tier($user_id, true);
    $next_tier = $user_tier['next_tier'] ?? [];
    $next_tier_require_courses = $user_tier['next_tier_require_courses'];

    $course_count = learndash_get_courses_count();
    $completed_course_percentage = floor($user_tier['total_completed_courses'] / $course_count * 100);
    $desc = \STA\Inc\CarbonFields\PageHomeAgent::get_description($post_id);
    ?>
    <div class="sta-hero-banner d-flex align-items-center lazyload py-100 py-lg-150" data-bg="<?php echo get_template_directory_uri(); ?>/assets/images/home-agent-bg.jpg">
        <div class="container w-100">
            <div class="row">
                <div class="col-12 col-lg-8 col-xxl-6 sta-welcome-prompt-hidden">
                    <h1 class="mb-0"><?php printf(\STA\Inc\CarbonFields\PageHomeAgent::get_heading($post_id), $user->display_name); ?></h1>
                    <?php if ($desc): ?>
                        <div class="sta-hero-banner-desc text-content mt-30 mt-md-40 mt-xxl-60"><?php echo wpautop($desc); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- quick links -->
    <div class="sta-agent-quick-links sta-welcome-prompt-hidden">
        <div class="container">
            <div class="row">
                <?php $data = [
                    [
                        'icon_url' => get_template_directory_uri() . '/assets/images/badge.svg',
                        'heading' => \STA\Inc\CarbonFields\PageHomeAgent::get_continue_training_heading($post_id),
                        'btn_label' => \STA\Inc\CarbonFields\PageHomeAgent::get_continue_training_btn_label($post_id),
                        // 'btn_url' => home_url('/training'),
                        'btn_url' => \STA\Inc\CarbonFields\PageHomeAgent::get_continue_training_btn_url($post_id),
                    ],
                    [
                        'icon_url' => get_template_directory_uri() . '/assets/images/trophy.svg',
                        'heading' => \STA\Inc\CarbonFields\PageHomeAgent::get_view_dashboard_heading($post_id),
                        'btn_label' => \STA\Inc\CarbonFields\PageHomeAgent::get_view_dashboard_btn_label($post_id),
                        // 'btn_url' => \STA\Inc\UserDashboard::training_accreditation_page_url(),
                        'btn_url' => \STA\Inc\CarbonFields\PageHomeAgent::get_view_dashboard_btn_url($post_id),
                    ],
                    // [
                    //     'icon_url' => get_template_directory_uri() . '/assets/images/referral.svg',
                    //     'heading' => \STA\Inc\CarbonFields\PageHomeAgent::get_invite_colleagues_heading($post_id),
                    //     'btn_label' => \STA\Inc\CarbonFields\PageHomeAgent::get_invite_colleagues_btn_label($post_id),
                    //     // 'btn_url' => \STA\Inc\UserDashboard::referrals_page_url(),
                    //     'btn_url' => \STA\Inc\CarbonFields\PageHomeAgent::get_invite_colleagues_btn_url($post_id),
                    // ]
                ]; ?>
                <?php foreach ($data as $item): ?>
                    <div class="col-12 col-lg-4 mb-32">
                        <?php get_template_part('template-parts/agent-quick-link', '', $item); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- training status -->
    <div class="sta-agent-training-status py-80 pt-55 pt-lg-80">
        <div class="container">
            <!-- heading -->
            <div class="row justify-content-between align-items-center mb-40">
                <div class="col-12 col-md-8 mb-32 mb-md-0">
                    <h3 class="mb-0"><?php _e('Your training status', 'sta'); ?></h3>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <a class="btn btn-outline-green w-100 w-md-auto" href="<?php echo home_url('/training'); ?>"><?php _e('View all modules', 'sta'); ?></a>
                </div>
            </div>

            <!-- briefs -->
            <div class="pt-24 sta-agent-training-status-status lh-1">
                <div class="row">
                    <!-- badge -->
                    <div class="col-12 col-md-auto mb-24 me-lg-40">
                        <div class="sta-agent-training-level d-flex align-items-center">
                            <div class="sta-agent-training-status-status-icon">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/badge.svg" alt="">
                            </div>
                            <div class="ms-8">
                                <div class="fs-20 fw-500 mb-8"><?php echo $user_tier['label']; ?></div>
                                <div class="sta-agent-training-level-stars <?php echo $user_tier['slug']; ?>">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                            <div class="ms-auto ms-md-40 text-end text-md-start">
                                <?php $user_total_points = \STA\Inc\PointsSystem::get_user_total_points($user_id); ?>
                                <div class="fs-30 fw-500 mb-4"><?php printf('%s pts', $user_total_points); ?></div>
                                <?php $text = __('You are already at the highest tier.', 'sta');
                                if ($next_tier_require_courses > 0) {
                                    $text = sprintf(
                                        __('%1$s to go until %2$s', 'sta'),
                                        \STA\Inc\Translator::modules($next_tier_require_courses),
                                        $next_tier['label'],
                                    );
                                }
                                printf('<div>%s</div>', $text);
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- achievements -->
                    <div class="col-12 col-md-auto mb-24 me-lg-40">
                        <div class="d-flex">
                            <div class="sta-agent-training-status-status-icon">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/trophy.svg" alt="">
                            </div>
                            <div class="ms-16">
                                <div class="fs-20 fw-500 mb-8"><?php echo \STA\Inc\Translator::achievements(\STA\Inc\BadgeSystem::count_user_badges($user_id)); ?></div>
                                <a href="<?php echo \STA\Inc\UserDashboard::training_achievement_page_url(); ?>"><?php _e('View', 'sta'); ?></a>
                            </div>
                        </div>
                    </div>
                    <!-- modules completed -->
                    <div class="col-12 col-md-auto mb-24 me-lg-40">
                        <div class="d-flex align-items-center">
                            <div class="sta-agent-training-status-status-icon">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/module.svg" alt="">
                            </div>
                            <div class="ms-16">
                                <div class="fs-20 fw-500 mb-8"><?php printf(__('%1$s%% of total modules completed', 'sta'), $completed_course_percentage); ?></div>
                                <a href="<?php echo home_url('/training'); ?>"><?php _e('View', 'sta'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- courses -->
            <div class="sta-agent-training-status-courses row pt-32 pt-lg-56">
                <?php $user_course_list = learndash_user_get_enrolled_courses($user_id);
                foreach ($user_course_list as $course_id):
                    $progress = \STA\Inc\CptCourse::get_user_progress($user_id, $course_id);
                    $unlocked = $progress['unlocked'] ?? false;
                    if (!$unlocked) {
                        continue;
                    }
                    $completed_percentage = $progress['completed_percentage'] ?? 0;
                    $course_categories = wp_get_post_terms($course_id, 'ld_course_category', ['fields' => 'names']);

                    $attributes = [
                        $unlocked ? sprintf('data-percentage="%s"', $completed_percentage) : '',
                    ];

                    $attributes = array_filter($attributes);
                    $attributes = implode(' ', $attributes);
                    $attributes = $attributes ? ' ' . $attributes : '';
                    $post_permalink = get_permalink($course_id);
                    ?>
                    <div class="col-12 col-lg-6 mb-32 mb-lg-40">
                        <div class="sta-user-course-progress-preview p-24 d-flex align-items-center justify-content-between"<?php echo $attributes; ?>>
                            <a class="sta-user-course-progress-preview-link" href="<?php echo $post_permalink; ?>"></a>
                            <div class="d-flex">
                                <div class="sta-user-course-progress-preview-progress" data-value="">
                                    <span><?php printf('%s%%', $completed_percentage); ?></span>
                                    <div class="sta-circular-percentage" data-value="<?php echo $completed_percentage; ?>"></div>
                                </div>
                                <div class="ms-16">
                                    <div class="sta-user-course-progress-preview-title"><?php echo get_the_title($course_id); ?></div>
                                    <?php if (is_array($course_categories) && !empty($course_categories)): ?>
                                        <div class="sta-user-course-progress-preview-categories"><?php echo implode(', ', $course_categories); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="course-progress-icon"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php get_template_part('template-parts/welcome-prompt'); ?>

<?php get_footer();
