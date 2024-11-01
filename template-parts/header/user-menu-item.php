<?php

/**
 * @var array $args
 */

use STA\Inc\CarbonFields\ThemeOptions;

$extra_classes = $args['extra_classes'] ?? '';

$user = wp_get_current_user();
$user_id = $user->ID;

$li_classes = [
    'menu-item-user-menu',
    $extra_classes,
];
$li_classes = array_filter($li_classes);


$edit_profile_url = ThemeOptions::get_oauth_edit_profile_url();

if(ThemeOptions::get_ssid_method() == 1){
    $edit_profile_url = ThemeOptions::get_key_clock_edit_profile_url()."?lang=".$_COOKIE['wp-wpml_current_language'];
}


?>
<li class="<?php echo implode(' ', $li_classes); ?>">
    <button type="button" class="btn-toggle-user-menu-dropdown" aria-label="Show user profile">
        <?php echo \STA\Inc\UserDashboard::user_profile_image($user_id); ?>
        <span class="d-lg-none lh-1 d-block text-start ms-16">
                <span class="fs-18 fs-lg-20 fw-500 d-block mb-4"><?php echo $user->display_name; ?></span>
                <span class="fs-14 d-block"><?php echo $user->user_email; ?></span>
            </span>
    </button>
    <div class="user-menu-dropdown">
        <div class="user-menu-dropdown-inner p-lg-40">
            <div class="d-none d-lg-block">
                <div class="fs-20 fw-500"><?php echo $user->display_name; ?></div>
                <div class="fs-14"><?php echo $user->user_email; ?></div>
            </div>
            <?php // wp_nav_menu([
            //     'theme_location' => 'user_dropdown',
            //     'container' => '',
            //     'menu_class' => 'user-menu-dropdown-menu mt-lg-24 pt-lg-24',
            // ]); ?>

            <ul id="menu-user-dropdown-menu-1" class="user-menu-dropdown-menu mt-lg-24 pt-lg-24"><li class="menu-item-dropdown-back d-lg-none"><button type="button" class="btn-dropdown-back"><?php _e('Back', 'sta'); ?></button></li>
                <li class="menu-item menu-item-type-custom menu-item-object-custom "><a href="<?php echo $edit_profile_url ?>"><?php _e('Edit Your Profile', 'sta'); ?></a></li>
                <li class="sta-logout"><a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn btn-outline-green w-100"><?php _e('Log Out', 'sta'); ?></a></li>
            </ul>

        </div>
    </div>
</li>
