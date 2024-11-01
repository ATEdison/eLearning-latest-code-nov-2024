<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-text-two-columns', 'Text - Two Columns')
    ->add_fields(array(
        Field::make('separator', 's1', 'Text - Two Columns'),
        Field::make('select', 'bg', 'Background')
            ->add_options(array(
                '' => 'None',
                'bg-primary' => 'Green',
            )),
        Field::make('text', 'heading', 'Heading'),
        Field::make('rich_text', 'col_1', 'Column 1'),
        Field::make('rich_text', 'col_2', 'Column 2'),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $section_class = 'sta-text-two-columns py-60 pb-lg-90';
        if ($fields['bg']) {
            $section_class .= ' ' . $fields['bg'];
        }
        ?>
        <div class="<?php echo $section_class; ?>">
            <div class="container">
                <h2 class="mb-32 mb-lg-40"><?php echo $fields['heading']; ?></h2>
                <div class="row">
                    <div class="col-12 col-lg-6 text-content"><?php echo wpautop($fields['col_1']); ?></div>
                    <div class="col-12 col-lg-6 text-content"><?php echo wpautop($fields['col_2']); ?></div>
                </div>
            </div>
        </div>
        <?php
    });
