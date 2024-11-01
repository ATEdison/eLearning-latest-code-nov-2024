<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-text-image', 'Text & Image')
    ->add_fields(array(
        Field::make('separator', 's1', 'Text & Image'),
        Field::make('select', 'layout', 'Layout')
            ->set_width(50)
            ->add_options(array(
                'image-right' => 'Image on the right',
                'image-left' => 'Image on the left',
            ))
            ->set_default_value('image-right'),
        Field::make('select', 'bg', 'Background')
            ->set_width(50)
            ->add_options(array(
                '' => 'None',
                'bg-sta-blue-viloet' => 'Blue Violet',
                'bg-primary' => 'Green',
                'bg-sta-pink' => 'Pink',
            )),
        Field::make('image', 'image', 'Image'),
        Field::make('text', 'heading', 'Heading'),
        Field::make('rich_text', 'text', 'Text'),
        Field::make('text', 'btn_label', 'Button Label'),
        Field::make('text', 'btn_url', 'Button URL'),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $layout = isset($fields['layout']) && $fields['layout'] ? $fields['layout'] : 'image-right';
        $section_class = 'sta-text-image';
        if ($fields['bg']) {
            $section_class .= ' ' . $fields['bg'];
        }

        $col_1_class = 'sta-text-image-text col-12 col-lg-6 py-60 py-lg-80 d-flex align-items-center';
        $col_2_class = 'sta-text-image-image col-12 col-lg-6';
        if ($layout == 'image-left') {
            $col_1_class .= ' order-2';
            $col_2_class .= ' order-1';
        }
        ?>
        <div class="<?php echo $section_class; ?>">
            <div class="container-fluid">
                <div class="row justify-content-between mx-n32">
                    <div class="<?php echo $col_1_class; ?>">
                        <div>
                            <h2 class="h3 ff-gotham mb-40"><?php echo $fields['heading']; ?></h2>
                            <div class="mb-60 text-content"><?php echo wpautop($fields['text']); ?></div>
                            <span class="btn_sign_in_up">
                             
                            <?php if ($fields['btn_label']): ?>
                                <a class="" href="<?php echo $fields['btn_url']; ?>"><?php echo $fields['btn_label']; ?></a>
                            <?php endif; ?>
                          
                            </span>
                        </div>
                    </div>
                    <div class="<?php echo $col_2_class; ?>"><?php echo wp_get_attachment_image($fields['image'], 'full'); ?></div>
                </div>
            </div>
        </div>
        <?php
    });
