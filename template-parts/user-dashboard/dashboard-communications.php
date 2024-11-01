<?php

if (!is_user_logged_in()) {
    printf('<div class="container">Unauthorized!</div>');
    return;
}

$user_id = get_current_user_id();

$page_id = get_the_ID();
$base_url = untrailingslashit(get_the_permalink($page_id));

// personal
$contact_channels_settings = \STA\Inc\UserDashboard::get_user_contact_channels_settings($user_id);
$newsletters_settings = \STA\Inc\UserDashboard::get_user_newsletters_settings($user_id);
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
                <h3 class="mb-35"><?php _e('Communications', 'sta'); ?></h3>

                <!-- contact channels form -->
                <div class="sta-user-details py-40 form-active">
                    <h4 class="mb-40"><?php _e('Contact channels', 'sta'); ?></h4>
                    <div class="sta-user-details-form">
                        <?php get_template_part('template-parts/user-dashboard/parts/form-contact-channels', '', [
                            'user_id' => $user_id,
                            'data' => $contact_channels_settings,
                        ]); ?>
                    </div>
                </div>

                <!-- Newsletters form -->
                <div class="sta-user-details py-40 form-active">
                    <h4 class="mb-40"><?php _e('Newsletters', 'sta'); ?></h4>
                    <div class="sta-user-details-form">
                        <?php get_template_part('template-parts/user-dashboard/parts/form-newsletters-settings', '', [
                            'user_id' => $user_id,
                            'data' => $newsletters_settings,
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
