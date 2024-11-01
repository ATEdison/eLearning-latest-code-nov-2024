<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-hero-banner', 'Hero Banner')
    ->add_fields(array(
        Field::make('separator', 's1', 'Hero Banner'),
        Field::make('image', 'bg', 'Background Image'),
        Field::make('text', 'sub_heading', 'Sub-heading'),
        Field::make('text', 'heading', 'Heading'),
        Field::make('rich_text', 'desc', 'Description'),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        ?>
        <div class="sta-hero-banner d-flex align-items-center lazyload py-100 py-lg-150" data-bg="<?php echo wp_get_attachment_image_url($fields['bg'], 'full'); ?>">
            <div class="container w-100">
                <div class="row">
                    <div class="col-12">
                        <?php if ($fields['sub_heading']): ?>
                            <div class="fs-20 fs-lg-24 fw-500 mb-30"><?php echo $fields['sub_heading']; ?></div>
                        <?php endif; ?>
                        <h1 class="mb-0"><?php echo $fields['heading']; ?></h1>
                    </div>
                    <?php if ($fields['desc']): ?>
                        <div class="col-12 col-lg-8 col-xxl-6 mt-30 mt-md-40 mt-xxl-60">
                            <div class="sta-hero-banner-desc fs-20 fs-lg-32 lh-1-5"><?php echo $fields['desc']; ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    });
