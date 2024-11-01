<?php

if (!is_user_logged_in()) {
    printf('<div class="container">Unauthorized!</div>');
    return;
}

$user_id = get_current_user_id();

$page_id = get_the_ID();
$base_url = untrailingslashit(get_the_permalink($page_id));

// $sta_subpage = get_query_var('sta_subpage');

// personal
$personal_details = \STA\Inc\UserDashboard::get_personal_details($user_id);
$personal_field_list = [
    'first_name' => __('First Name', 'sta'),
    'last_name' => __('Last Name', 'sta'),
    'email' => __('Email Address', 'sta'),
    'country' => __('Country', 'sta'),
    'primary_contact_number' => __('Primary Contact Number', 'sta'),
    'mobile_number' => __('Mobile Number', 'sta'),
    'profile_image' => __('Profile Image', 'sta'),
];

// work details
$work_details = \STA\Inc\UserDashboard::get_work_details($user_id);
$work_field_list = [
    'work_company_name' => __('Company Name', 'sta'),
    'work_company_type' => __('Company Type', 'sta'),
    'work_contact_number' => __('Contact Number', 'sta'),
    'work_website' => __('Website', 'sta'),
    'work_address' => __('Address', 'sta'),
];
?>
<div class="sta-user-dashboard-heading mb-40 d-none d-lg-block">
    <div class="container">
        <h1 class="mb-0"><?php the_title(); ?></h1>
    </div>
</div>
<div class="sta-user-dashboard-content">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-5 col-xxl-4 mb-56 mb-lg-0 d-none d-lg-block">
                <?php get_template_part('template-parts/user-dashboard/parts/profile'); ?>
            </div>
            <div class="col-12 col-lg-7 col-xxl-8">
                <div class="mb-16">
                    <a class="sta-user-dashboard-back" href="<?php echo $base_url; ?>"><?php echo get_the_title($page_id); ?></a>
                </div>
                <h3 class="mb-35"><?php _e('My Details', 'sta'); ?></h3>

                <!-- personal details form -->
                <?php $is_personal_form_active = \STA\Inc\FormHandleInvoker::is_personal_form_active(); ?>
                <div class="sta-user-details py-40<?php echo $is_personal_form_active ? ' form-active' : ''; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-40">
                        <h4 class="mb-0"><?php _e('Personal', 'sta'); ?></h4>
                        <a class="sta-user-details-btn-edit" href="javascript:void(0);" data-edit="<?php echo htmlentities(__('Edit personal details', 'sta')); ?>" data-cancel="<?php echo htmlentities(__('Cancel', 'sta')); ?>"></a>
                    </div>
                    <div class="sta-user-details-content">
                        <?php $personal_form_handler = \STA\Inc\FormHandleInvoker::personal_form_handler();
                        $success = $personal_form_handler->is_success();
                        if ($success): ?>
                            <div class="col-12 mb-40">
                                <?php get_template_part('template-parts/alert', '', [
                                    'message' => sprintf(
                                        __('<strong>Your profile was updated.</strong> View your achievements <a href="%1$s">here</a>', 'sta'),
                                        \STA\Inc\UserDashboard::training_achievement_page_url()
                                    ),
                                    'type' => 'success',
                                    'dismissible' => true,
                                ]); ?>
                            </div>
                        <?php endif; ?>

                        <?php get_template_part('template-parts/user-dashboard/parts/user-details', '', [
                            'data' => $personal_details,
                            'field_list' => $personal_field_list,
                        ]); ?>
                    </div>
                    <div class="sta-user-details-form">
                        <?php get_template_part('template-parts/user-dashboard/parts/form-personal', '', [
                            'user_id' => $user_id,
                            'data' => $personal_details,
                        ]); ?>
                    </div>
                </div>

                <!-- work details form -->
                <?php $is_user_work_details_form_active = \STA\Inc\FormHandleInvoker::is_user_work_details_form_active(); ?>
                <div id="sta-user-details-work" class="sta-user-details py-40<?php echo $is_user_work_details_form_active ? ' form-active' : ''; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-40">
                        <h4 class="mb-0"><?php _e('Work', 'sta'); ?></h4>
                        <a class="sta-user-details-btn-edit" href="javascript:void(0);" data-edit="<?php echo htmlentities(__('Edit work details', 'sta')); ?>" data-cancel="<?php echo htmlentities(__('Cancel', 'sta')); ?>"></a>
                    </div>
                    <div class="sta-user-details-content">
                        <?php $work_form_handler = \STA\Inc\FormHandleInvoker::user_work_details_form_handler();
                        $success = $work_form_handler->is_success();
                        if ($success): ?>
                            <div class="col-12 mb-40">
                                <?php get_template_part('template-parts/alert', '', [
                                    'message' => sprintf(
                                        __('<strong>Your profile was updated.</strong> View your achievements <a href="%1$s">here</a>', 'sta'),
                                        \STA\Inc\UserDashboard::training_achievement_page_url()
                                    ),
                                    'type' => 'success',
                                    'dismissible' => true,
                                ]); ?>
                            </div>
                        <?php endif; ?>

                        <?php get_template_part('template-parts/user-dashboard/parts/user-details', '', [
                            'data' => $work_details,
                            'field_list' => $work_field_list,
                        ]); ?>
                    </div>
                    <div class="sta-user-details-form">
                        <?php get_template_part('template-parts/user-dashboard/parts/form-work', '', [
                            'user_id' => $user_id,
                            'data' => $work_details,
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
