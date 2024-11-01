<?php

/**
 * @var array $args
 */

 use STA\Inc\LearndashCustom;
 
$user_id = $args['user_id'];
$course_id = $args['course_id'];
$progress = $args['progress'];
$is_lapsed = $progress['is_lapsed'];
if ($is_lapsed) {
    return;
}

$extra_class = $args['class'] ?? '';
$extra_class = $extra_class ? ' ' . $extra_class : '';

$next_step_id = $progress['next_step_id'];

$course_points = \STA\Inc\PointsSystem::get_user_course_points($user_id, $course_id);
$total_points = \STA\Inc\PointsSystem::get_user_total_points($user_id);
$user_tier = \STA\Inc\TierSystem::get_user_tier($user_id);
$badge_list = \STA\Inc\BadgeSystem::get_course_earned_badges($user_id, $course_id);

$heading = sprintf(__('Your current tier is %s', 'sta'), $user_tier['label']);
if ($user_tier['is_new']) {
    $heading = sprintf(__('Upgraded to %s', 'sta'), $user_tier['label']);
}
$pass_all_core_courses = \STA\Inc\CptCourse::is_user_passed_all_cat_courses($user_id, 'core');
$pass_all_courses = $pass_all_core_courses && \STA\Inc\CptCourse::is_user_passed_all_courses($user_id);


$pass_all_partner_courses = \STA\Inc\CptCourse::is_user_passed_all_cat_courses($user_id, 'partner');
$pass_all_courses_tkp = \STA\Inc\CptCourse::is_user_passed_all_courses($user_id);

 $courses_list = learndash_user_get_enrolled_courses($user_id, array(), true);
// print_r($courses_list);

$course_total = 0;
$course_completed = 0;

foreach($courses_list as $c=> $course){

	if(get_post_status($course) == 'private'){
		$course_total += count(learndash_course_status($course, $user_id, $return_slug = false));
		if(learndash_course_status($course, $user_id, $return_slug = false) == 'Completed'){
			$course_completed += 1;
		}
	}
}

?>

<div class="sta-course-progress completed shadow p-24 pb-120 p-md-40 pe-md-100<?php echo $extra_class; ?>">
    <div class="sta-course-progress-inner">
        <h4 class="mb-24"><?php _e('Congratulations!', 'sta'); ?></h4>
        <div class="mb-40">
            <?php _e("You've completed this module and have earned the following:", 'sta'); ?>
        </div>

   <?php 
            $user = get_userdata(get_current_user_id());
            // Get all the user roles for this user as an array.
            $user_roles = $user->roles;
            // print_r($user_roles);
            if(!in_array('stauser', $user_roles, true )) {
        ?> 


        <div class="d-sm-flex mb-30">
            <div class="w-72 me-24 mb-10 mb-sm-0">
                <div class="sta-tier-badge unlocked <?php printf('sta-tier-badge-%s', $user_tier['slug']); ?>"><span></span></div>
            </div>
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-8"><?php echo $heading; ?></h5>
                    <div>
                        <?php printf(
                            __('You earned %1$s, and now have a total of %2$s.', 'sta'),
                            \STA\Inc\Translator::points($course_points),
                            \STA\Inc\Translator::points($total_points)
                        ); ?>
                    </div>
                </div>
            </div>
        </div>


        <!-- achievements -->
        <?php if (is_array($badge_list) && !empty($badge_list)): ?>
            <div class="d-sm-flex">
                <div class="w-72 me-24 d-flex justify-content-center align-items-start mb-10 mb-sm-0">
                    <img class="sta-heading-icon me-0" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/trophy.svg" alt="" width="48" height="48">
                </div>
                <div>
                    <h5 class="mb-8"><?php _e('Achievements', 'sta'); ?></h5>
                    <div class="mb-24"><?php _e('You received new badges.', 'sta'); ?></div>
                    <?php get_template_part('template-parts/badge-list', '', ['badge_list' => $badge_list]); ?>
                </div>
            </div>
        <?php endif; ?>


        <!-- up next -->
     

            <?php if (!$pass_all_core_courses): ?>
            <?php $next_course_id = \STA\Inc\CptCourse::next_core_course_id($user_id); ?>
            <div class="d-sm-flex mt-20">
                <div class="w-72 me-24 d-flex justify-content-center align-items-start mb-10 mb-sm-0">
                    <span class="upnext-icon"><?php get_template_part('template-parts/icons/icon-up-next'); ?></span>
                </div>
                <div>
                    <h5 class="mb-8"><?php _e('Up next', 'sta'); ?></h5>
                    <div class="mb-24"><?php _e("You're one step closer to achieving your first level. Continue with your training below.", 'sta'); ?></div>

                   <a class="btn btn-primary" href="<?php echo get_permalink($next_course_id); ?>"><span class="me-5"><?php echo __('Next module','sta'); ?></span>
                        <span class="nextm-icon"><?php get_template_part('template-parts/icons/icon-arrow-right'); ?></span> 
                    </a>
                </div>
            </div>
        <?php elseif (!$pass_all_courses): ?>
            <?php $next_course_id = \STA\Inc\CptCourse::next_course_id($user_id); ?>
            <div class="d-sm-flex mt-20">
                <div class="w-72 me-24 d-flex justify-content-center align-items-start mb-10 mb-sm-0">
                   <span class="upnext-icon"> <?php get_template_part('template-parts/icons/icon-up-next'); ?> </span>
                </div>
                <div>
                    <h5 class="mb-8"><?php _e('Up next', 'sta'); ?></h5>
                    <div class="mb-24"><?php _e("You've completed all core modules and gained your first level achievement! Continue with additional modules to deepen your expertise.", 'sta'); ?></div>
                    
                    <a class="btn btn-primary" href="<?php echo get_permalink($next_course_id); ?>"><span class="me-5"><?php echo __('Next module','sta'); ?></span>
                        <span class="nextm-icon"><?php get_template_part('template-parts/icons/icon-arrow-right'); ?></span> 
                    </a>                  

                </div>
            </div>
        <?php else: ?>
            <div class="d-sm-flex mt-20">
                <div class="w-72 me-24 d-flex justify-content-center align-items-start mb-10 mb-sm-0">
                    <?php get_template_part('template-parts/icons/icon-all-modules-completed'); ?>
                </div>
                <div>
                    <h5 class="mb-8"><?php _e('All modules completed', 'sta'); ?></h5>
                    <div class="mb-24"><?php _e("Congratulations! You've completed all available training right now. Feel free to retake the modules at any time to refresh your knowledge.", 'sta'); ?></div>

                        <a class="btn btn-primary" href="<?php echo home_url('/training'); ?>"><?php echo __('View all modules','sta'); ?></a>                       
                    
                </div>
            </div>
        <?php endif; ?>


        <?php } else { ?>

            <?php if ($course_completed == $course_total): ?>
		<div class="d-sm-flex mt-20">
                <div class="w-72 me-24 d-flex justify-content-center align-items-start mb-10 mb-sm-0">
                    <?php get_template_part('template-parts/icons/icon-all-modules-completed'); ?>
                </div>
                <div>
                    <h5 class="mb-8"><?php _e('All modules completed', 'sta'); ?></h5>
                    <div class="mb-24"><?php _e("Congratulations! You've completed all available training right now. Feel free to retake the modules at any time to refresh your knowledge.", 'sta'); ?></div>

                       <a class="btn btn-primary" href="<?php echo home_url('/tourism-knowledge-program'); ?>"><?php echo __('View all modules','sta'); ?></a>   
                      
                </div>
            </div>                   
        <?php else: ?>
            <?php $next_course_id = \STA\Inc\CptCourse::next_core_course_id($user_id); ?>
            <div class="d-sm-flex mt-20">
                <div class="w-72 me-24 d-flex justify-content-center align-items-start mb-10 mb-sm-0">
                    <span class="upnext-icon"><?php get_template_part('template-parts/icons/icon-up-next'); ?></span>
                </div>
                <div>
                    <h5 class="mb-8"><?php _e('Up next', 'sta'); ?></h5>
                    <div class="mb-24"><?php _e("You're one step closer to achieving your first level. Continue with your training below.", 'sta'); ?></div>

                    <a class="btn btn-primary" href="<?php echo home_url('/tourism-knowledge-program'); ?>"><span class="me-5"><?php echo __('Next module','sta'); ?></span>
                        <span class="nextm-icon"><?php get_template_part('template-parts/icons/icon-arrow-right'); ?></span>
                    </a>

                </div>
            </div>        <?php endif; ?>

        <?php } ?>

        
    </div>
</div>