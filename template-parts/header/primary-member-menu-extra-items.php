<?php
$post_id = get_the_ID();
$dashboard_page_id = \STA\Inc\CarbonFields\ThemeOptions::get_user_dashboard_page_id();
// $notification_count = NotificationSystem::get_user_new_notification_count($user_id);
$training_page_url = \STA\Inc\UserDashboard::training_achievement_page_url();

$li_classes = [
    'menu-item-dashboard',
    $post_id == $dashboard_page_id ? 'current-menu-item' : '',
];
$li_classes = array_filter($li_classes);

$request_uri = $_SERVER['REQUEST_URI'] ?? null;
$uris = [];
$uris = explode("/",$request_uri);  

if(in_array('ar', $uris)) {
$dasboard_url = site_url('ar/my-dashboard/training-achievement/');           
}elseif(in_array('zh-hans', $uris)){
    $dasboard_url = site_url('zh-hans/my-dashboard/training-achievement/');
}else{
    $dasboard_url = site_url('my-dashboard/training-achievement/');
}

// $user = wp_get_current_user();
// print_r(get_current_user_id());


$user = get_userdata(get_current_user_id());
// Get all the user roles for this user as an array.
$user_roles = $user->roles;
//print_r($user_roles);
if(!in_array( 'stauser', $user_roles, true )) {

    
?> 

<li class="<?php echo implode(' ', $li_classes); ?>">
    <a href="<?php echo $dasboard_url; ?>">
        <?php _e('My Dashboard', 'sta'); ?>
        <?php
        // if ($notification_count > 0) {
        //     printf('<span>%s</span>', $notification_count);
        // }
        ?>
    </a>
</li>
<?php

  }

?>

<?php get_template_part('template-parts/header/user-menu-item', '', ['extra_classes' => 'd-lg-none']); ?>



