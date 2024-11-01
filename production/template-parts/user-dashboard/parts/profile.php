<?php

$user = wp_get_current_user();
$user_id = $user->ID;

$user_country_code = \STA\Inc\UserDashboard::get_user_country_code($user_id);
?>

<div class="sta-user-dashboard-profile p-24 p-md-40 lh-1">
    <div class="text-center">
        <div class="sta-user-dashboard-profile-avatar mb-24"><?php echo \STA\Inc\UserDashboard::user_profile_image($user_id); ?></div>
        <h4 class="mb-8"><?php echo $user->display_name; ?></h4>
        <div class="mb-16"><?php echo $user->user_email; ?></div>
        <?php if ($user_country_code): ?>
            <div class="sta-user-dashboard-profile-nationality mb-32"><span class="<?php printf('fi fi-%s', strtolower($user_country_code)); ?>"></span></div>
        <?php endif; ?>
        <div class="sta-user-dashboard-profile-registration-date mb-24">
            <?php printf(
                __('Registered %s', 'sta'),
                date_i18n('M Y', strtotime($user->user_registered))
            ); ?>
        </div>
        <div><a href="<?php echo \STA\Inc\CarbonFields\ThemeOptions::get_oauth_edit_profile_url(); ?>"><?php _e('Edit your profile', 'sta'); ?></a></div>
    </div>
    <div class="accordion sta-user-dashboard-profile-accordion mt-32" id="accordionProfile">
        <div class="accordion-item">
            <h5 class="accordion-header" id="headingCompleteYourProfile">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompleteYourProfile" aria-expanded="true" aria-controls="collapseCompleteYourProfile">
                    <span class="icon-user me-16"></span><?php _e('Complete your profile', 'sta'); ?>
                </button>
            </h5>
            <?php $quests = [
                // [
                //     'title' => __('Keep updated with our newsletter', 'sta'),
                //     'link' => \STA\Inc\UserDashboard::communications_page_url(),
                //     'is_completed' => \STA\Inc\BadgeSystem::is_user_subscribed_to_newsletters($user_id),
                // ],
                // [
                //     'title' => __('Invite your colleagues', 'sta'),
                //     'link' => \STA\Inc\UserDashboard::referrals_page_url(),
                //     'is_completed' => \STA\Inc\BadgeSystem::is_user_invited_colleagues($user_id),
                // ],
                // [
                //     'title' => __('Upload your profile picture', 'sta'),
                //     'link' => \STA\Inc\UserDashboard::user_profile_page_url(),
                //     'is_completed' => \STA\Inc\BadgeSystem::is_user_earned_upload_profile_photo_badge($user_id),
                // ],
                [
                    'title' => __('Gain your Silver Achievement', 'sta'),
                    'link' => null,
                    // 'is_completed' => false,
                    'is_completed' => \STA\Inc\BadgeSystem::is_user_gained_a_silver_badge($user_id),
                ],
            ]; ?>
            <?php if (!empty($quests)): ?>
                <div id="collapseCompleteYourProfile" class="accordion-collapse collapse show" aria-labelledby="headingCompleteYourProfile" data-bs-parent="#accordionProfile">
                    <div class="accordion-body">
                        <ul class="sta-user-dashboard-profile-quests">
                            <?php foreach ($quests as $item): ?>
                                <li>
                                    <?php if ($item['is_completed'] || !$item['link']): ?>
                                        <span data-completed="<?php echo $item['is_completed'] ? '1' : '0'; ?>"><?php echo $item['title']; ?></span>
                                    <?php else: ?>
                                        <a href="<?php echo $item['link']; ?>" data-completed="<?php echo $item['is_completed'] ? '1' : '0'; ?>"><?php echo $item['title']; ?></a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
