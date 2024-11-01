<?php

if (!is_user_logged_in()) {
    printf('<div class="container">Unauthorized!</div>');
    return;
}

$user_id = get_current_user_id();

$page_id = get_the_ID();
$base_url = untrailingslashit(get_the_permalink($page_id));

// $sta_subpage = get_query_var('sta_subpage');
$current_page = isset($_GET['paged']) && is_numeric($_GET['paged']) ? intval($_GET['paged']) : 0;
$current_page = max(1, $current_page);
[$notification_list, $page_count] = \STA\Inc\NotificationSystem::get_user_notifications($user_id, $_GET);
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
                <h3 class="mb-5"><?php _e('Notifications', 'sta'); ?></h3>
                <div class="sta-notification-list">
                    <?php foreach ($notification_list as $item):
                        $is_new = isset($item['is_new']) && $item['is_new'];
                        $message = stripslashes($item['message']);
                        $excerpt = strlen($message) > 160 ? substr($message, 0, 150) . '...' : $message;
                        ?>
                        <div class="sta-notification-list-item py-24 py-lg-40<?php echo $is_new ? ' new' : ''; ?>" data-id="<?php echo $item['id']; ?>">
                            <div class="sta-notification-list-item-post-time mb-16 fs-14 fw-500">
                                <span><?php printf('%s | %s', $item['post_date'], $item['post_time']); ?></span>
                            </div>
                            <h5><?php echo $item['title']; ?></h5>
                            <div class="mb-24">
                                <div class="sta-notification-list-item-excerpt"><?php echo $excerpt; ?></div>
                                <div class="sta-notification-list-item-message"><?php echo wpautop($message); ?></div>
                            </div>
                            <a class="sta-notification-list-item-read-more" href="javascript:void(0);" data-more="<?php _e('Read more', 'sta'); ?>" data-less="<?php _e('Read less', 'sta'); ?>"></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($page_count > 1): ?>
                    <div class="sta-notification-list-pagination mt-40">
                        <ul class="pagination m-0 p-0">
                            <?php for ($page_index = 1; $page_index <= $page_count; $page_index++) {
                                if ($current_page != $page_index) {
                                    printf('<li class="page-item"><a class="page-link" href="%1$s">%2$s</a></li>', add_query_arg(['paged' => $page_index]), $page_index);
                                    continue;
                                }
                                printf('<li class="page-item active"><span class="page-link">%s</span></li>', $page_index);
                            } ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
