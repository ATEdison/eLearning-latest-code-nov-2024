<?php
/**
 * @var array $args
 */

$post_id = $args['post_id'] ?? get_the_ID();
$user_id = is_user_logged_in() ? get_current_user_id() : 0;
$for_member = !!($args['for_member'] ?? false) && $user_id;

$post_permalink = get_the_permalink($post_id);

$attributes = [];
$extra_classes = [];

$course_categories = null;
if ($for_member) {
    $extra_classes[] = 'course-preview-member';
}

$course_categories = wp_get_post_terms($post_id, 'ld_course_category');
$category_slugs = wp_list_pluck($course_categories, 'slug');
$attributes[] = sprintf('data-categories="%s"', htmlentities(json_encode($category_slugs)));

$has_thumbnail = has_post_thumbnail($post_id);
if (!$has_thumbnail) {
    $extra_classes[] = 'no-thumbnail';
}

$progress = \STA\Inc\CptCourse::get_user_progress($user_id, $post_id);
$unlocked = $progress['unlocked'] ?? null;
$is_lapsed = $progress['is_lapsed'] ?? null;
$has_progress = $is_lapsed || ($for_member && is_array($progress) && !empty($progress) && $progress['completed'] > 0);

if ($has_progress) {
    $attributes[] = sprintf('data-status="%s"', $progress['status']);
}

$user_can_enroll_course = \STA\Inc\CptCourse::user_can_enroll_course($user_id, $post_id);
if ($user_can_enroll_course) {
    $extra_classes[] = 'can-enroll-course';
}

$attributes = implode(' ', $attributes);
$attributes = $attributes ? ' ' . $attributes : '';

$extra_classes = implode(' ', $extra_classes);
$extra_classes = $extra_classes ? ' ' . $extra_classes : '';
?>

<div class="course-preview d-flex flex-column align-items-stretch<?php echo $extra_classes; ?>"<?php echo $attributes; ?>>
    <?php if ($unlocked || !is_user_logged_in()): ?>
        <a class="course-preview-link" href="<?php echo $post_permalink; ?>"></a>
    <?php endif; ?>
    <div class="course-preview-image">
        <?php echo get_the_post_thumbnail($post_id, 'thumb_485x'); ?>
        <?php if ($has_progress): ?>
            <div class="course-preview-image-status"><?php echo \STA\Inc\CptCourse::get_course_status_label(($progress['status'])); ?></div>
        <?php endif; ?>
    </div>
    <div class="p-20 p-md-40 d-flex flex-column flex-grow-1">
        <h3 class="course-preview-title h7 mb-24">
            <?php ob_start(); ?>
            <?php echo get_the_title($post_id); ?>
            <?php if ($for_member): ?>
                <span class="course-progress-icon"></span>
            <?php endif; ?>
            <?php $heading = ob_get_clean(); ?>

            <?php if ($unlocked): ?>
                <a href="<?php echo $post_permalink; ?>"><?php echo $heading; ?></a>
            <?php else: ?>
                <?php echo $heading; ?>
            <?php endif; ?>
        </h3>
        <?php if ($for_member):
            $has_categories = is_array($course_categories) && !empty($course_categories);
            $duration = \STA\Inc\CptCourse::get_duration($post_id);
            if ($has_categories || $duration): ?>
                <div class="course-preview-meta mb-24 pb-24">
                    <?php if ($has_categories): ?>
                        <div class="course-preview-categories">
                            <ul>
                                <?php
                                /**
                                 * @var WP_Term $item
                                 */
                                foreach ($course_categories as $item): ?>
                                    <li><?php echo $item->name; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if ($duration): ?>
                        <div class="course-preview-duration"><?php echo $duration; ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="course-preview-excerpt flex-grow-1 lh-1-8"><?php echo get_the_excerpt($post_id); ?></div>
        <?php if ($has_progress): ?>
            <div class="mt-40">
                <?php get_template_part('template-parts/course-progress', '', ['progress' => $progress]); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
