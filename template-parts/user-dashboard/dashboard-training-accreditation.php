<?php

if (!is_user_logged_in()) {
    printf('<div class="container">Unauthorized!</div>');
    return;
}

$user_id = get_current_user_id();
$page_id = get_the_ID();
$base_url = untrailingslashit(get_the_permalink($page_id));

// $sta_subpage = get_query_var('sta_subpage');
// @TODO: do not refresh user_tier here
$user_tier = \STA\Inc\TierSystem::get_user_tier($user_id, true);

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
                <h3 class="mb-30"><?php _e('Training & Achievement', 'sta'); ?></h3>

                <!-- certificate -->
                <?php if (!\STA\Inc\CarbonFields\ThemeOptions::should_hide_certificate_section()): ?>
                    <div class="sta-user-dashboard-ta-block p-24 p-lg-40 border-success mb-40">
                        <div class="row align-items-center justify-content-between mb-40">
                            <div class="col-12 col-xxl-4 mb-20 mb-xxl-0">
                                <img loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-green.svg" width="120" height="61" alt="">
                            </div>
                            <div class="col-12 col-xxl-8 text-xxl-end"><?php printf(__('Saudi Expert since %s', 'sta'), date_i18n('d F Y')); ?></div>
                        </div>
                        <div class="sta-user-dashboard-linklist-download">
                            <ul>
                                <?php $download_list = [
                                    [
                                        'heading' => __('Acknowledgement Certificate', 'sta'),
                                        'desc' => __('Download your Saudi Expert Acknowledgement certificate', 'sta'),
                                        'link' => home_url('/pdf/certificate'),
                                    ],
                                    [
                                        'heading' => __('Email Signature', 'sta'),
                                        'desc' => __('Download your personalised Saudi Expert signature for use in your emails', 'sta'),
                                        'link' => wp_get_attachment_url(\STA\Inc\CarbonFields\ThemeOptions::get_email_signature_file_id()),
                                    ],
                                    [
                                        'heading' => __('Website Tile', 'sta'),
                                        'desc' => __('Download your personalised Saudi Expert tile for use in your own website', 'sta'),
                                        'link' => wp_get_attachment_url(\STA\Inc\CarbonFields\ThemeOptions::get_website_tile_file_id()),
                                    ],
                                ];
                                foreach ($download_list as $item): ?>
                                    <li>
                                        <div class="d-md-flex flex-md-nowrap justify-content-md-between align-items-md-center">
                                            <div class="flex-grow-1 pe-md-20">
                                                <h6 class="mb-10"><?php echo $item['heading']; ?></h6>
                                                <div class="mb-20 mb-md-0"><?php echo $item['desc']; ?></div>
                                            </div>
                                            <div class="">
                                                <a href="<?php echo $item['link']; ?>" class="btn btn-outline-black w-100 w-md-auto" download><?php _e('Download', 'sta'); ?></a>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- tier -->
                <?php $border_class = sprintf('border-sta-%s', $user_tier['slug']); ?>
                <div class="sta-user-dashboard-ta-block p-24 pb-8 p-lg-40 pb-lg-24 <?php echo $border_class; ?>">
                    <?php get_template_part('template-parts/user-dashboard/parts/user-tier', '', ['user_id' => $user_id]); ?>

                    <div>
                        <h4 class="mb-40">
                            <img class="sta-heading-icon" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/trophy.svg" alt="" width="48" height="48">
                            <span><?php _e('Achievements', 'sta'); ?></span>
                        </h4>
                        <?php $badge_list = \STA\Inc\BadgeSystem::get_user_earned_badges($user_id);
                        get_template_part('template-parts/badge-list', '', ['badge_list' => $badge_list]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
