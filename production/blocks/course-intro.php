<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-course-intro', 'Course Intro')
    ->add_fields(array(
        Field::make('separator', 's1', 'Course Intro'),
        Field::make('text', 'heading', 'Heading'),
        Field::make('rich_text', 'desc', 'Description'),
        Field::make('text', 'btn_label', 'Button Label'),
        Field::make('text', 'btn_url', 'Button URL'),
        Field::make('complex', 'public_resources', 'Public Resources')
            ->set_layout('tabbed-vertical')
            ->add_fields(array(
                Field::make('file', 'file', 'File'),
                Field::make('text', 'heading', 'Heading'),
                Field::make('textarea', 'desc', 'Description'),
            )),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        ?>
        <div class="sta-course-intro py-80">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-6 mb-60 mb-lg-0">
                        <h2 class="h5 mb-35 ff-gotham"><?php echo $fields['heading']; ?></h2>
                        <div class="text-content mb-40"><?php echo wpautop($fields['desc']); ?></div>
                        <?php if ($fields['btn_label']): ?>
                            <a href="<?php echo $fields['btn_url']; ?>" class="btn btn-outline-green w-100 w-md-auto"><?php echo $fields['btn_label']; ?></a>
                        <?php endif; ?>
                    </div>
                    <?php if (is_array($fields['public_resources']) && !empty($fields['public_resources'])): ?>
                        <div class="col-12 col-lg-6 ps-xl-50 ps-xxl-130">
                            <h3 class="h4 mb-56"><?php printf('%s Resources', get_the_title()); ?></h3>
                            <div>
                                <?php foreach ($fields['public_resources'] as $item): ?>
                                    <div class="sta-course-public-resource p-25 mb-40 rounded-5">
                                        <div class="position-relative">
                                            <h4 class="h5 mb-8"><?php echo $item['heading']; ?></h4>
                                            <div class="sta-course-public-resource-desc fw-500"><?php echo $item['desc']; ?></div>
                                            <a class="sta-course-public-resource-download" href="<?php echo wp_get_attachment_url($item['file']); ?>" target="_blank"><span class="visually-hidden">Download</span></a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    });
