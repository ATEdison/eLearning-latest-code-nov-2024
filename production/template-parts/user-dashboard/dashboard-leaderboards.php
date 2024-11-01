<?php

if (!is_user_logged_in()) {
    printf('<div class="container">Unauthorized!</div>');
    return;
}

$page_id = get_the_ID();
$base_url = untrailingslashit(get_the_permalink($page_id));

// $sta_subpage = get_query_var('sta_subpage');
$user_id = get_current_user_id();

$user_rank_data = \STA\Inc\RankingSystem::get_user_rank_data($user_id);
// @TODO: use cache instead of always refresh
$global_rank = \STA\Inc\RankingSystem::get_user_global_rank($user_id, $user_rank_data, true);
$country_rank = \STA\Inc\RankingSystem::get_user_country_rank($user_id, $user_rank_data, true);

?>
<div class="sta-user-dashboard-heading mb-60 mb-lg-80">
    <div class="container">
        <div class="mb-25">
            <a class="sta-user-dashboard-back" href="<?php echo $base_url; ?>"><?php echo get_the_title($page_id); ?></a>
        </div>
        <h1 class="mb-32"><?php echo \STA\Inc\CarbonFields\PageMyDashboard::get_leaderboard_heading($page_id); ?></h1>
        <div class="text-content"><?php echo wpautop(\STA\Inc\CarbonFields\PageMyDashboard::get_leaderboard_description($page_id)); ?></div>
    </div>
</div>
<div class="sta-user-dashboard-content">
    <div class="container">
        <div class="row mb-60 mb-lg-80">
            <div class="col-12 col-lg-6 mb-30 mb-lg-0">
                <div class="p-24 px-lg-40 py-lg-30 shadow rounded-5 d-flex align-items-center">
                    <div>
                        <img class="sta-heading-icon" loading="lazy" src="<?php echo get_template_directory_uri(); ?>/assets/images/global-rank.svg" width="48px" height="48px" alt="">
                    </div>
                    <div>
                        <div class="sta-rank-heading fw-500"><?php _e('Global Rank', 'sta'); ?></div>
                        <div class="h3 mb-0 lh-1"><?php echo $global_rank; ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="p-24 px-lg-40 py-lg-30 shadow rounded-5 d-flex align-items-center">
                    <div>
                        <?php printf('<span class="fi fi-%s sta-heading-icon"></span>', strtolower($user_rank_data['country'])); ?>
                    </div>
                    <div>
                        <div class="sta-rank-heading fw-500"><?php printf(__('Rank in %s', 'sta'), \STA\Inc\UserDashboard::get_country_label($user_rank_data['country'])); ?></div>
                        <div class="h3 mb-0 lh-1"><?php echo $country_rank; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <form class="row mb-40">
            <div class="col-12 col-lg-8">
                <div class="row">
                    <div class="col-12 col-lg-6 mb-32 mb-lg-0 leaderboard-filter leaderboard-filter-country">
                        <label for="leaderboard_filter_region" class="form-label"><span><?php _e('Country', 'sta'); ?></span></label>
                        <select id="leaderboard_filter_region" type="text" class="form-select" name="country">
                            <option value="">Please select</option>
                            <?php $country_options = \STA\Inc\UserDashboard::country_options();
                            $selected = $data['work_country'] ?? '';
                            foreach ($country_options as $value => $label) {
                                printf(
                                    '<option value="%1$s" %2$s>%3$s</option>',
                                    $value,
                                    selected($selected, $value, false),
                                    $label
                                );
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
        </form>
        <div class="sta-leaderboard">
            <!-- heading -->
            <div class="sta-leaderboard-heading">
                <div class="row py-24 align-items-center d-none d-lg-flex fs-20 fw-500 lh-1">
                    <div class="col-1 col-lg-1-5"><?php _e('Position', 'sta'); ?></div>
                    <div class="col-11 col-lg-10-5">
                        <div class="row">
                            <div class="col-12 col-lg-1-5"><?php _e('Country', 'sta'); ?></div>
                            <div class="col-12 col-lg-5 col-xxl-5-5 col-xxxl-6 px-0"><?php _e('Name', 'sta'); ?></div>
                            <div class="col-12 col-lg-2-5 col-xxl-2"><?php _e('Tier', 'sta'); ?></div>
                            <div class="col-12 col-lg-1-5"><?php _e('Badges', 'sta'); ?></div>
                            <div class="col-12 col-lg-1-5 col-xxxl-1"><?php _e('Score', 'sta'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- rows -->
            <?php [$data, $has_more] = \STA\Inc\RankingSystem::get_leaderboard(); ?>
            <div class="sta-leaderboard-data mb-40">
                <?php foreach ($data as $item): ?>
                    <div class="sta-leaderboard-item">
                        <div class="row py-24 align-items-lg-center">
                            <div class="col-auto col-sm-2 col-lg-1-5 sta-leaderboard-item-rank h4 mb-0" data-value="<?php echo $item['rank']; ?>">
                                <span><?php echo $item['rank']; ?></span>
                            </div>
                            <div class="col col-sm-10 col-lg-10-5">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-12 col-lg-1-5 lh-0 d-none d-lg-block"><?php printf('<span class="fi fi-%s filarge"></span>', $item['country']); ?></div>
                                    <div class="col-12 col-lg-5 col-xxl-5-5 col-xxxl-6 sta-leaderboard-item-name mb-12 mb-lg-0 px-0">
                                        <div class="user-profile-placeholder-wrapper me-24"><?php echo $item['avatar']; ?></div>
                                        <div>
                                            <div class="fs-18 fw-500 text-break"><?php echo $item['name']; ?></div>
                                            <?php printf('<div class="fi fi-%s d-lg-none"></div>', strtolower($item['country'])); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto col-lg-2-5 col-xxl-2 sta-leaderboard-item-tier px-10 px-lg-20" data-value="<?php echo $item['tier']['slug']; ?>">
                                        <span></span><span></span><span></span>
                                        <span class="fs-18 fw-500 ms-8 ms-lg-24 bg-transparent"><?php echo $item['tier']['label']; ?></span>
                                    </div>
                                    <div class="col-auto col-lg-1-5 sta-leaderboard-item-total-badges fs-18 fw-500 px-10 px-lg-20"><?php echo $item['total_badges']; ?></div>
                                    <div class="col-auto col-lg-1-5 col-xxxl-1 h4 mb-0 px-10 px-lg-20"><?php echo $item['points']; ?><span class="d-lg-none"> pts</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- load more -->
            <div class="text-center sta-leaderboard-load-more<?php echo $has_more ? '' : ' d-none'; ?>">
                <button type="button" class="btn btn-outline-green w-100 w-md-auto px-md-200"><?php _e('Load more', 'sta'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/html" id="tmpl-sta-leader-board-item">
    <div class="sta-leaderboard-item">
        <div class="row py-24 align-items-lg-center">
            <div class="col-auto col-sm-2 col-lg-1-5 sta-leaderboard-item-rank h4 mb-0" data-value="{{data.rank}}">
                <span>{{data.rank}}</span>
            </div>
            <div class="col col-sm-10 col-lg-10-5">
                <div class="row align-items-center justify-content-between">
                    <div class="col-12 col-lg-1-5 lh-0 d-none d-lg-block"><span class="fi fi-{{data.country}} filarge"></span></div>
                    <div class="col-12 col-lg-5 col-xl-5 col-xxxl-6 sta-leaderboard-item-name mb-12 mb-lg-0">
                        <div class="user-profile-placeholder-wrapper me-24">{{{data.avatar}}}</div>
                        <div>
                            <div class="fs-18 fw-500">{{data.name}}</div>
                            <div class="fi fi-{{data.country}} d-lg-none"></div>
                        </div>
                    </div>
                    <div class="col-auto col-lg-2-5 col-xxl-2 sta-leaderboard-item-tier px-10 px-lg-20" data-value="{{data.tier.slug}}">
                        <span></span><span></span><span></span>
                        <span class="fs-18 fw-500 ms-8 ms-lg-24 bg-transparent">{{data.tier.label}}</span>
                    </div>
                    <div class="col-auto col-lg-1-5 sta-leaderboard-item-total-badges fs-18 fw-500 px-10 px-lg-20">{{data.total_badges}}</div>
                    <div class="col-auto col-lg-1-5 col-xxxl-1 h4 mb-0 px-10 px-lg-20">{{data.points}}<span class="d-lg-none"> pts</span></div>
                </div>
            </div>
        </div>
    </div>
</script>
