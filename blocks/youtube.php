<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-youtube', 'STA Youtube')
    ->add_fields(array(
        Field::make('separator', 's1', 'STA Youtube'),
        Field::make('text', 'video_url', 'Youtube URL'),
        Field::make('text', 'caption', 'Caption'),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        get_template_part('template-parts/shortcodes/youtube', '', $fields);
    });
