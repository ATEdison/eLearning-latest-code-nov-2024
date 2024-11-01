<?php

/**
 * @var array $args
 */
$user_id = $args['user_id'];

$user_tier = \STA\Inc\TierSystem::get_user_tier($user_id);
$next_tier = $user_tier['next_tier'] ?? null;
$total_completed_courses = $user_tier['total_completed_courses'];
$next_tier_require_courses = $user_tier['next_tier_require_courses'];
$next_tier_label = $next_tier['label'] ?? '';
$tier_start_point = 0;
$tier_end_point = $next_tier['course_count'] ?? $user_tier['course_count'];
$progress_percentage = floor($total_completed_courses / $tier_end_point * 100);

$tier_system = \STA\Inc\TierSystem::tier_system();
?>

<div class="row mb-45">
    <div class="col-12 col-xxl-7 mb-20 mb-xxl-0">
        <div class="d-flex align-items-center">
            <div class="d-none d-lg-block">
                <img class="sta-heading-icon" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/badge.svg" alt="" width="48" height="48">
            </div>
            <div>
                <h4 class="mb-8"><?php printf('%s: %s', __('Current Tier', 'sta'), $user_tier['label']); ?></h4>
                <h5 class="mb-0"><?php echo \STA\Inc\Translator::modules($total_completed_courses); ?></h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-xxl-5 text-xxl-end">
        <div class="d-flex justify-content-xxl-end mb-24">
            <?php foreach ($tier_system as $item) {
                if ($item['slug'] == 'no-tier') {
                    continue;
                }
                $class = [
                    'sta-tier-badge',
                    sprintf('sta-tier-badge-%1$s', $item['slug']),
                    $total_completed_courses >= $item['course_count'] ? 'unlocked' : '',
                ];
                $class = array_filter($class);
                printf('<div class="%1$s"><span></span></div>', implode(' ', $class));
            } ?>
        </div>
        <?php if ($next_tier): ?>
            <div><?php printf(_n('%s module required to reach %s.', '%s modules required to reach %s.', $next_tier_require_courses, 'sta'), $next_tier_require_courses, $next_tier_label); ?></div>
        <?php else: ?>
            <div><?php _e('You have reached the highest tier!', 'sta'); ?></div>
        <?php endif; ?>
        <!--<div><?php /*printf(__('Read more about <a href="%s">tier benefits</a>.', 'sta'), '#'); */?></div>-->
    </div>
</div>
<div class="sta-user-dashboard-progress mb-50">
    <div class="d-flex justify-content-between mb-16 fs-14 lh-1">
        <div><?php echo \STA\Inc\Translator::modules($total_completed_courses); ?></div>
        <div><?php echo \STA\Inc\Translator::modules($tier_end_point); ?></div>
    </div>
    <div class="progress">
        <div class="progress-bar bg-secondary" role="progressbar" style="width: <?php echo $progress_percentage; ?>%" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
