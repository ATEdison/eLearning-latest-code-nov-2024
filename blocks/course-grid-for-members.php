<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

/**
 * @see wp_login_form()
 * @see learndash_load_login_modal_html()
 * @see wp_lostpassword_url()
 */
Block::make('sta-course-grid-members', 'Course Grid (Members)')
    ->add_fields(array(
        Field::make('separator', 's1', 'Course Grid (Members)'),
        Field::make('text', 'heading', 'Heading'),
        Field::make('text', 'banner_heading', 'Banner Heading'),
        Field::make('rich_text', 'banner_desc', 'Banner Description'),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        ?>
        <div class="sta-course-grid-members py-60">
            <div class="container-xl">
                <div class="row justify-content-between mb-48">
                    <div class="col-12 col-md-auto mb-24 mb-md-0">
                        <h2 class="h3 ff-gotham mb-0"><?php echo $fields['heading']; ?></h2>
                    </div>
                    <div class="col-12 col-md-auto course-grid-filter">
                        <?php $categories = get_terms(['taxonomy' => 'ld_course_category']); ?>
                        <ul class="course-grid-filter-category">
                            <li>
                                <input id="course_category_all" type="radio" name="course_category" value="__all__" checked>
                                <label for="course_category_all"><?php _e('All', 'sta'); ?></label>
                            </li>
                            <?php foreach ($categories as $term):
                                $html_id = sprintf('course_category_%s', $term->slug); ?>
                                <li>
                                    <input id="<?php echo $html_id; ?>" type="radio" name="course_category" value="<?php echo $term->slug; ?>">
                                    <label for="<?php echo $html_id; ?>"><?php echo $term->name; ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- core modules -->
                <div class="row justify-content-center">
                    <?php
                    // core modules
                    $query_args = [
                        'post_type' => 'sfwd-courses',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'tax_query' => [
                            [
                                'taxonomy' => 'ld_course_category',
                                'field' => 'slug',
                                'terms' => ['core'],
                                'operator' => 'IN',
                            ],
                        ],
                    ];

                    $query = new WP_Query($query_args);

                    while ($query->have_posts()): $query->the_post(); ?>
                        <div class="col-12 col-md-6 col-lg-4 mb-32 mb-lg-40 d-flex align-items-stretch">
                            <?php get_template_part('template-parts/course-preview', '', ['for_member' => true]); ?>
                        </div>
                    <?php endwhile;
                    wp_reset_query();
                    wp_reset_postdata(); ?>
                </div>
                <div class="sta-cta-get-accredited mb-40">
                    <h3 class="mb-16 lh-1"><?php echo $fields['banner_heading']; ?></h3>
                    <div class="text-content"><?php echo wpautop($fields['banner_desc']); ?></div>
                </div>
                <!-- other modules -->
                <div class="row">
                    <?php
                    // other modules
                    $query_args = [
                        'post_type' => 'sfwd-courses',
                        'posts_per_page' => -1,
                        'post_status' => 'publish',
                        'tax_query' => [
                            [
                                'taxonomy' => 'ld_course_category',
                                'field' => 'slug',
                                'terms' => ['core'],
                                'operator' => 'NOT IN',
                            ],
                        ],
                    ];

                    $query = new WP_Query($query_args);

                    while ($query->have_posts()): $query->the_post(); ?>
                        <div class="col-12 col-md-6 col-lg-4 mb-32 mb-lg-40 d-flex align-items-stretch">
                            <?php get_template_part('template-parts/course-preview', '', ['for_member' => true]); ?>
                        </div>
                    <?php endwhile;
                    wp_reset_query();
                    wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
        <?php
    });
