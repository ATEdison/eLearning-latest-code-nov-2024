<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-download-resources', 'Download Resources')
    ->add_fields(array(
        Field::make('separator', 's1', 'Download Resources'),
        Field::make('text', 'heading', 'Heading'),
        Field::make('textarea', 'desc', 'Description'),
        Field::make('complex', 'resources', 'Resources')
            ->set_layout('tabbed-vertical')
            ->add_fields(array(
                Field::make('file', 'file', 'File'),
                Field::make('text', 'title', 'Title'),
            )),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        ?>
        <div class="sta-download-resources mb-40">
            <div class="container">
                <div class="shadow p-24 p-md-32 rounded-5">
                    <h6 class="sta-download-resources-heading mb-24 mb-lg-32"><?php echo $fields['heading']; ?></h6>
                    <div class="mb-32"><?php echo wpautop($fields['desc']); ?></div>
                    <ul class="sta-download-resources-resources">
                        <?php foreach ($fields['resources'] as $item) {
                            printf('<li><a href="%1$s" target="_blank">%2$s</a><a href="%1$s" class="btn btn-download" target="_blank"></a></li>', wp_get_attachment_url($item['file']), $item['title']);
                        } ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    });
