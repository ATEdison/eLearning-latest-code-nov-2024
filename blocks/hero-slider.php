<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-hero-slider', 'Hero Slider')
    ->add_fields(array(
        Field::make('separator', 's1', 'Hero Slider'),
        Field::make('complex', 'items', 'Slides')
            ->set_layout('tabbed-vertical')
            ->add_fields(array(
                Field::make('text', 'nav_label', 'Navigation Label'),
                Field::make('image', 'bg', 'Background Image'),
                Field::make('image', 'logo', 'Logo'),
                Field::make('text', 'heading_mobile', 'Heading (Mobile)'),
                Field::make('rich_text', 'desc', 'Description'),
                Field::make('text', 'btn_label', 'Button Label'),
                Field::make('text', 'btn_url', 'Button URL'),
                Field::make('image', 'map', 'Map'),
                Field::make('text', 'map_heading', 'Map Heading'),
                Field::make('text', 'map_subheading', 'Map Sub-heading'),
            )),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        ?>
        <div class="sta-hero-slider text-white">
            <div class="sta-hero-slider-slider">
                <?php foreach ($fields['items'] as $item): ?>
                    <div>
                        <div class="sta-hero-slider-slide d-flex align-items-center lazyload py-120 pb-150 py-sm-150 py-md-180" data-bg="<?php echo wp_get_attachment_image_url($item['bg'], 'full'); ?>">
                            <div class="container">
                                <div class="row justify-content-between">
                                    <div class="col-12 col-lg-auto order-1 order-lg-2 mb-20 mb-sm-40 mb-lg-0 d-lg-flex justify-content-lg-end">
                                        <div class="row align-items-center text-lg-center flex-lg-column">
                                            <div class="col-6 col-lg-auto order-2 order-lg-1 text-end text-lg-center">
                                                <?php echo wp_get_attachment_image($item['map'], 'full'); ?>
                                            </div>
                                            <div class="col-6 col-lg-auto order-1 order-lg-2">
                                                <div class="text-white fw-500 fs-lg-20"><?php echo $item['map_heading']; ?></div>
                                                <div class="sta-hero-slider-slide-item-map-subheading"><?php echo $item['map_subheading']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6 order-2 order-lg-1">
                                        <div class="mb-30 d-none d-md-block"><?php echo wp_get_attachment_image($item['logo'], 'full'); ?></div>
                                        <div class="mb-30 d-md-none"><h3 class="fs-40 mb-0"><?php echo $item['heading_mobile']; ?></h3></div>
                                        <div class="mb-40"><?php echo wpautop($item['desc']); ?></div>
                                        <?php if ($item['btn_label']): ?>
                                            <a href="<?php echo $item['btn_url']; ?>" class="btn btn-outline-light w-100 w-md-auto"><?php echo $item['btn_label']; ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="sta-hero-slider-navigation-wrapper">
                <div class="container-lg px-0 px-lg-20">
                    <div class="sta-hero-slider-navigation d-lg-flex justify-content-between mx-lg-n20">
                        <?php foreach ($fields['items'] as $item_index => $item):
                            $is_active = $item_index < 1; ?>
                            <div class="sta-hero-slider-navigation-item px-lg-20<?php echo $is_active ? ' active' : ''; ?>">
                                <button type="button" class="h5 pb-25"><?php echo $item['nav_label']; ?></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    });
