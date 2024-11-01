<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-course-grid', 'Course Grid (Public)')
    ->add_fields(array(
        Field::make('separator', 's1', 'Course Grid (Public)'),
        \STA\Inc\CarbonFields::field_bg_color(),
        Field::make('text', 'heading', 'Heading'),
        Field::make('rich_text', 'desc', 'Description'),
        Field::make('checkbox', 'related', 'Display related courses'),
        Field::make('association', 'items', 'Display specific courses')
            ->set_types([
                ['type' => 'post', 'post_type' => 'sfwd-courses'],
            ]),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $section_extra_class = '';
        if (isset($fields['bg_color']) && $fields['bg_color']) {
            $section_extra_class .= ' ' . $fields['bg_color'];
        }

        $related = isset($fields['related']) && $fields['related'];
        ?>
        <div class="sta-course-grid pt-60 pb-20 pt-lg-80 pb-lg-40<?php echo $section_extra_class; ?>">
            <div class="container">
                <h2 class="h3 ff-gotham mb-40"><?php echo $fields['heading']; ?></h2>
                <?php if ($fields['desc']): ?>
                    <div class="row mb-26">
                        <div class="col-12 col-xl-8 text-content"><?php echo wpautop($fields['desc']); ?></div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <?php if ($related):
                        $course_id = get_the_ID();
                        $query_args = [
                            'post_type' => 'sfwd-courses',
                            'posts_per_page' => 3,
                            'post_status' => 'publish',
                        ];

                        $categories = wp_get_post_terms($course_id, 'ld_course_category', ['fields' => 'ids']);
                        if (is_array($categories) && !empty($categories)) {
                            $query_args['tax_query'] = [
                                [
                                    'taxonomy' => 'ld_course_category',
                                    'field' => 'term_id',
                                    'terms' => wp_get_post_terms($course_id, 'ld_course_category'),
                                    'operator' => 'IN',
                                ]
                            ];
                        }

                        $query = new WP_Query($query_args);

                        if (!$query->have_posts()) {
                            $query_args = [
                                'post_type' => 'sfwd-courses',
                                'posts_per_page' => 3,
                                'post_status' => 'publish',
                            ];
                            $query = new WP_Query($query_args);
                        }

                        while ($query->have_posts()): $query->the_post(); ?>
                            <div class="col-12 col-md-6 col-xl-4 mb-40 d-flex align-items-stretch">
                                <?php get_template_part('template-parts/course-preview'); ?>
                            </div>
                        <?php endwhile;
                        wp_reset_query();
                        wp_reset_postdata(); ?>
                    <?php else: ?>
                        <?php foreach ($fields['items'] as $item): ?>
                            <div class="col-12 col-md-6 col-xl-4 mb-40 d-flex align-items-stretch">
                                <?php get_template_part('template-parts/course-preview', '', ['post_id' => $item['id']]); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    });
