<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-hero-home', 'Home Hero')
    ->add_tab('Home Hero', [
        Field::make('image', 'logo', 'Logo')->set_width(30),
        Field::make('file', 'bg_video', 'Background Video')->set_width(30),
        Field::make('image', 'bg_image', 'Background Image (fallback)')->set_width(30),
        Field::make('text', 'heading_mobile', 'Heading (Mobile)'),
        Field::make('rich_text', 'desc', 'Description'),
        Field::make('text', 'btn_label', 'Button Label'),
        Field::make('text', 'btn_url', 'Button URL'),
    ])
    ->add_tab('Map', [
        Field::make('complex', 'items', '')
            ->set_layout('tabbed-vertical')
            ->add_fields(array(
                Field::make('text', 'nav_label', 'Navigation Label'),
                Field::make('file', 'bg_video', 'Background Video')->set_width(50),
                Field::make('image', 'bg_image', 'Background Image (fallback)')->set_width(50),
                Field::make('complex', 'items', 'Markers')
                    ->set_layout('tabbed-vertical')
                    ->add_fields(array(
                        Field::make('html', 'sta_admin_saudi_map', 'Location'),
                        Field::make('text', 'map_heading', 'Heading'),
                        Field::make('textarea', 'map_subheading', 'Description'),
                    )),
            )),
    ])
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        // \STA\Inc\Helpers::log($fields);
        $map_data = [];
        foreach ($fields['items'] as $map_index => $item) {
            foreach ($item['items'] as $location) {
                $map_data[] = [
                    'map_id' => $map_index,
                    'position' => $location['sta_admin_saudi_map'],
                    'heading' => $location['map_heading'],
                    'subheading' => $location['map_subheading'],
                ];
            }
        }

        $bg_image = $fields['bg_image'] ?? null;
        $bg_video = $fields['bg_video'] ?? null;
        ?>
        <div class="sta-hero-home text-white d-flex align-items-stretch py-120 pb-150 py-sm-150 py-md-180 lazyload" data-bg="<?php echo wp_get_attachment_image_url($bg_image, 'full'); ?>">
            <div>
                <?php if ($bg_video): ?>
                    <video class="sta-hero-home-bg-video" autoplay loop muted playsinline preload="none" poster="<?php echo wp_get_attachment_image_url($bg_image, 'full'); ?>">
                        <source src="<?php echo wp_get_attachment_url($bg_video); ?>" data-src="<?php echo wp_get_attachment_url($bg_video); ?>" type="video/mp4">
                        <span class="visually-hidden">Your browser does not support the video tag.</span>
                    </video>
                <?php endif; ?>
                <?php //foreach ($fields['items'] as $item_index => $item): ?>
                <!--    <video class="sta-hero-home-bg-video--><?php //echo $item_index > 0 ? ' d-none' : ''; ?><!--" autoplay loop muted playsinline preload="none" poster="--><?php //echo wp_get_attachment_image_url($item['bg_image'], 'full'); ?><!--">-->
                <!--        <source src="--><?php //echo $item_index < 1 ? wp_get_attachment_url($item['bg_video']) : ''; ?><!--" data-src="--><?php //echo wp_get_attachment_url($item['bg_video']); ?><!--" type="video/mp4">-->
                <!--        <span class="visually-hidden">Your browser does not support the video tag.</span>-->
                <!--    </video>-->
                <?php //endforeach; ?>
            </div>
            <div class="sta-hero-home-content w-100 d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-between">
                        <!--<div class="col-12 col-lg sta-hero-home-content-map order-1 order-lg-2 mb-20 mb-sm-40 mb-lg-0">
                            <div class="row align-items-center text-lg-center flex-lg-column">
                                <div class="col-6 col-lg-12 order-2 order-lg-1 text-end text-lg-center mb-lg-32">
                                    <div class="sta-hero-home-map" data-map="<?php /*echo htmlentities(json_encode($map_data)); */ ?>">
                                        <?php /*echo file_get_contents(get_theme_file_path('/assets/images/saudi-map.svg')); */ ?>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-12 order-1 order-lg-2 px-lg-0">
                                    <div class="sta-hero-home-map-marker-desc"></div>
                                </div>
                            </div>
                        </div>-->
                        <div class="col-12 col-lg-6 order-2 order-lg-1">
                            <div class="mb-30 d-none d-md-block"><?php echo wp_get_attachment_image($fields['logo'], 'full'); ?></div>
                            <div class="mb-30 d-md-none"><h3 class="fs-40 mb-0"><?php echo $fields['heading_mobile']; ?></h3></div>
                            <div class="mb-40"><?php echo wpautop($fields['desc']); ?></div>
                            <?php if ($fields['btn_label']): ?>
                                <a href="<?php echo $fields['btn_url']; ?>" class="btn btn-outline-light w-100 w-md-auto"><?php echo $fields['btn_label']; ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sta-hero-home-navigation-wrapper">
                <div class="container-lg px-0 px-lg-20">
                    <div class="sta-hero-home-navigation d-lg-flex justify-content-between mx-lg-n20">
                        <?php foreach ($fields['items'] as $item_index => $item): ?>
                            <div
                                class="sta-hero-home-navigation-item px-lg-20<?php echo $item_index < 1 ? ' active' : ''; ?>"
                                data-image="<?php echo wp_get_attachment_image_url($item['bg_image'], 'full'); ?>"
                            >
                                <button type="button" class="h5 pb-25"><?php echo $item['nav_label']; ?></button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php get_template_part('template-parts/tmpl-hero-home-map-marker-desc'); ?>
        </div>
        <?php
    });
