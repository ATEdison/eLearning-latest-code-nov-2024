<?php

/**
 * @var array $args
 */
$badge_details = \STA\Inc\BadgeSystem::get_badge_details($args);

$slug = $args['slug'];
$image_id = $badge_details['image_id'] ?? null;
$desc = $badge_details['desc'] ?? '';

if ($image_id) {
    printf(
        '<li class="sta-badge sta-badge-%1$s col-auto mb-16 px-8 lh-0"><span title="%3$s">%2$s</span></li>',
        $slug,
        wp_get_attachment_image($image_id, 'badge_80'),
        htmlentities($desc),
    );
    return;
}

// badge placeholder
printf(
    '<li class="sta-badge sta-badge-%1$s col-auto mb-16 px-8 lh-0"><span title="%3$s" data-text="%2$s"></span></li>',
    $slug,
    $slug,
    $slug,
);
