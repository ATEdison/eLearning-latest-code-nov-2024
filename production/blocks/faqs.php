<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

function faqs_accordion($fields, $prefix_id) {
    $faq_id = $prefix_id . 'accordion';
    ?>
    <div class="accordion sta-accordion" id="<?php echo $faq_id; ?>">
        <?php foreach ($fields['items'] as $item_index => $item):
            $heading_id = $prefix_id . 'heading_' . $item_index;
            $content_id = $prefix_id . 'content_' . $item_index;
            $is_active = $item_index < 1;
            $btn_class = 'accordion-button';
            $content_class = 'accordion-collapse collapse';
            if ($is_active) {
                $content_class .= ' show';
            } else {
                $btn_class .= ' collapsed';
            }

            $categories = isset($item['tags']) && $item['tags'] ? preg_split('/\r\n|[\r\n]/', $item['tags']) : [];
            $categories = is_array($categories) ? $categories : [];
            $categories = array_filter($categories);
            $categories = array_unique($categories);
            $categories = array_map('sanitize_title', $categories);
            ?>
            <div class="accordion-item" data-categories="<?php echo htmlentities(json_encode($categories)); ?>">
                <h3 class="accordion-header" id="<?php echo $heading_id; ?>">
                    <button class="<?php echo $btn_class; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $content_id; ?>" aria-expanded="false" aria-controls="<?php echo $content_id; ?>">
                        <?php echo $item['question']; ?>
                    </button>
                </h3>
                <div id="<?php echo $content_id; ?>" class="<?php echo $content_class; ?>" aria-labelledby="<?php echo $heading_id; ?>" data-bs-parent="#<?php echo $faq_id; ?>">
                    <div class="accordion-body">
                        <?php echo wpautop($item['answer']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

Block::make('sta-faqs', 'FAQs')
    ->add_fields(array(
        Field::make('separator', 's1', 'FAQs'),
        \STA\Inc\CarbonFields::field_bg_color(),
        Field::make('text', 'heading', 'Heading'),
        Field::make('complex', 'items', 'FAQs')
            ->set_layout('tabbed-vertical')
            ->add_fields(array(
                Field::make('textarea', 'tags', 'Tags')->set_help_text('One per line'),
                Field::make('text', 'question', 'Question'),
                Field::make('rich_text', 'answer', 'Answer'),
            )),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        global $sta_faq_count;
        $sta_faq_count = $sta_faq_count ? $sta_faq_count : 0;
        $sta_faq_count++;
        $prefix_id = sprintf('sta_faqs_%s_', $sta_faq_count);

        $extra_classes = [];
        if ($fields['bg_color']) {
            $extra_classes[] = $fields['bg_color'];
        }
        $extra_classes = implode(' ', $extra_classes);
        $extra_classes = $extra_classes ? ' ' . $extra_classes : '';

        $categories = [];
        foreach ($fields['items'] as $item) {
            $item_categories = preg_split('/\r\n|[\r\n]/', $item['tags']);
            $item_categories = is_array($item_categories) ? $item_categories : [];
            $item_categories = array_filter($item_categories);
            $item_categories = array_unique($item_categories);
            if (!is_array($item_categories) || empty($item_categories)) {
                continue;
            }
            $categories = array_merge($categories, $item_categories);
            $categories = array_unique($categories);
        }

        $categories = array_filter($categories);
        sort($categories);
        // var_dump($categories);
        $has_categories = is_array($categories) && !empty($categories);

        if ($has_categories) {
            $categories = array_merge(['All'], $categories);
        }
        ?>
        <div class="sta-faqs py-60 py-lg-80<?php echo $extra_classes; ?>">
            <div class="container">
                <?php if ($has_categories): ?>
                    <div class="row">
                        <div class="col-12 col-lg-4 mb-20">
                            <h2 class="h3 ff-gotham mb-40"><?php echo $fields['heading']; ?></h2>
                            <ul class="sta-faqs-filter">
                                <?php foreach ($categories as $item_index => $item):
                                    $html_id = $prefix_id . 'category_' . sanitize_title($item);
                                    $is_active = $item_index < 1;
                                    ?>
                                    <li>
                                        <input type="radio" id="<?php echo $html_id; ?>" name="<?php echo $prefix_id . 'category_filter'; ?>" value="<?php echo sanitize_title($item); ?>" <?php checked($is_active); ?>>
                                        <label for="<?php echo $html_id; ?>"><?php echo $item; ?></label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-8">
                            <?php faqs_accordion($fields, $prefix_id); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <h2 class="h3 ff-gotham"><?php echo $fields['heading']; ?></h2>
                    <?php faqs_accordion($fields, $prefix_id); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    });
