<?php

if (!is_user_logged_in()) {
    printf('<div class="container">Unauthorized!</div>');
    return;
}

$user_id = get_current_user_id();
$page_id = get_the_ID();
// $base_url = untrailingslashit(get_the_permalink($page_id));

$sta_subpage = get_query_var('sta_subpage');

?>
<div class="sta-user-dashboard-heading mb-40">
    <div class="container">
        <h1 class="mb-0"><?php the_title(); ?></h1>
    </div>
</div>
<div class="sta-user-dashboard-content">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-5 col-xxl-4 mb-56 mb-lg-0">
                <?php get_template_part('template-parts/user-dashboard/parts/profile'); ?>
            </div>
            <div class="col-12 col-lg-7 col-xxl-8">
                <?php if ($sta_subpage): ?>
                    <div class="alert alert-danger mb-40">
                        <?php printf(__('Page <code>%s</code> does not exist!', 'sta'), $sta_subpage); ?>
                    </div>
                <?php endif; ?>
                <div class="mb-48">
                    <?php
                    $user_tier = \STA\Inc\TierSystem::get_user_tier($user_id);
                    // $badge_list = \STA\Inc\BadgeSystem::get_user_earned_badges($user_id);

                    get_template_part('template-parts/user-dashboard/parts/linklist', '', [
                        'heading' => __('Saudi Expert', 'sta'),
                        'items' => [
                            [
                                'id' => 'training_achievement',
                                'heading' => __('Training & Achievement', 'sta'),
                                'desc' => sprintf(
                                    __('You\'re currently at %1$s with %2$s and %3$s', 'sta'),
                                    $user_tier['label'],
                                    \STA\Inc\Translator::modules($user_tier['total_completed_courses']),
                                    \STA\Inc\Translator::achievements(\STA\Inc\BadgeSystem::count_user_badges($user_id)),
                                ),
                                'link' => \STA\Inc\UserDashboard::training_achievement_page_url(),
                            ],
                            [
                                'id' => 'leaderboards',
                                'heading' => __('Leaderboards', 'sta'),
                                'desc' => __('Check your country and global ranking', 'sta'),
                                'link' => \STA\Inc\UserDashboard::leaderboards_page_url(),
                            ],
                        ],
                    ]); ?>
                </div>
                <!--<div class="mb-48">-->
                    <?php
                    // $notification_count = \STA\Inc\NotificationSystem::get_user_new_notification_count($user_id);
                    // get_template_part('template-parts/user-dashboard/parts/linklist', '', [
                    //     'heading' => __('Personal', 'sta'),
                    //     'items' => [
                    //         [
                    //             'id' => 'notifications',
                    //             'heading' => __('Notifications', 'sta'),
                    //             'desc' => sprintf(
                    //                 __('You\'ve got %1$s', 'sta'),
                    //                 \STA\Inc\Translator::new_notifications($notification_count),
                    //             ),
                    //             'link' => \STA\Inc\UserDashboard::notifications_page_url(),
                    //             'extra_class' => $notification_count ? 'has-notifications' : '',
                    //             'attrs' => [
                    //                 'value' => $notification_count,
                    //             ],
                    //         ],
                    //         // [
                    //         //     'id' => 'my_details',
                    //         //     'heading' => __('My Details', 'sta'),
                    //         //     'desc' => __('Update your personal and work details', 'sta'),
                    //         //     'link' => $base_url . '/my-details',
                    //         // ],
                    //     ],
                    // ]);
                    ?>
                <!--</div>-->
                <!--<div>-->
                <?php
                // get_template_part('template-parts/user-dashboard/parts/linklist', '', [
                //     'heading' => __('Account', 'sta'),
                //     'items' => [
                //         [
                //             'id' => 'general_preferences',
                //             'heading' => __('General Preferences', 'sta'),
                //             'desc' => __('Language and other settings', 'sta'),
                //             'link' => $base_url . '/general-preferences',
                //         ],
                //         [
                //             'id' => 'communications',
                //             'heading' => __('Communications', 'sta'),
                //             'desc' => __('Manage your notifications and how you wish to be contacted', 'sta'),
                //             'link' => $base_url . '/communications',
                //         ],
                //         [
                //             'id' => 'security',
                //             'heading' => __('Security', 'sta'),
                //             'desc' => __('Change your password and manage login activity', 'sta'),
                //             'link' => $base_url . '/security',
                //         ],
                //         [
                //             'id' => 'referrals',
                //             'heading' => __('Referrals', 'sta'),
                //             'desc' => __('Invite friends and colleagues to earn extra rewards', 'sta'),
                //             'link' => $base_url . '/referrals',
                //         ],
                //     ],
                // ]);
                ?>
                <!--</div>-->
            </div>
        </div>
    </div>
</div>
