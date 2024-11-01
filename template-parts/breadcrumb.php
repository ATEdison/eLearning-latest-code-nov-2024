<?php

/**
 * @var array $args
 */

$data = $args['data'];
$enable_mobile_toggle_course_navigation = $args['enable_mobile_toggle_course_navigation'] ?? false;
$extra_class = $args['class'] ?? '';
$extra_class = $extra_class ? ' ' . $extra_class : '';

$item_count = count($data);
?>

<div class="row sta-breadcrumb<?php echo $extra_class; ?>">
    <div class="col-12 px-0 px-lg-20">
        <div class="sta-breadcrumb-content py-22 py-xl-30 py-xxl-40 px-20 px-lg-0 d-flex">
            <?php if ($enable_mobile_toggle_course_navigation): ?>
                <button type="button" class="btn btn-toggle-course-navigation d-lg-none me-16"></button>
            <?php endif; ?>
            <div class="d-flex align-items-center">
                <ul>
                    <?php foreach ($data as $item_index => $item) {
                        if ($item_index < $item_count - 1) {
                            printf('<li><a href="%1$s">%2$s</a></li>', $item['url'], $item['title']);
                            continue;
                        }
                        printf('<li>%s</li>', $item['title']);
                    } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
