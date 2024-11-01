<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-hero-banner 2', 'Hero Banner 2')
    ->add_fields(array(
        Field::make('separator', 's1', 'Hero Banner 2'),
        Field::make('image', 'image', 'Image')
            ->set_width(50),
        Field::make('select', 'pattern', 'Pattern')
            ->set_width(50)
            ->add_options(array(
                'pattern-1' => 'Pattern 1',
                'pattern-2' => 'Pattern 2',
                'pattern-3' => 'Pattern 3',
            )),
        Field::make('text', 'heading', 'Heading'),
        Field::make('rich_text', 'desc', 'Description'),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $pattern = $fields['pattern'] ?? '';
        ?>
        <div class="sta-hero-banner-2 lazyload pt-56 pt-lg-80 pb-20" data-bg="<?php echo get_template_directory_uri(); ?>/assets/images/hero-banner-2-bg.png">
            <div class="container-fluid">
                <div class="row mx-n32">
                    <div class="sta-hero-banner-2-right col-12 col-lg-8 order-lg-2 pe-0 mb-56 mb-lg-0">
                        <div class="sta-hero-banner-2-right-inner <?php echo $pattern; ?>">
                            <?php echo wp_get_attachment_image($fields['image'], 'full'); ?>
                        </div>
                    </div>
                    <div class="sta-hero-banner-2-left col-12 col-lg-4 order-lg-1 d-lg-flex align-items-lg-center">
                        <div>
                            <h1 class="mb-24 mb-lg-32"><?php echo $fields['heading']; ?></h1>
                            <?php if ($fields['desc']): ?>
                                <div class="sta-hero-banner-2-desc"><?php echo $fields['desc']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    });
