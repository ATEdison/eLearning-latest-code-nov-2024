<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-quick-quiz', 'Quick Quiz')
    ->add_fields(array(
        Field::make('separator', 's1', 'Quick Quiz'),
        Field::make('text', 'heading', 'Heading'),
        Field::make('association', 'quiz', 'Quiz')
            ->set_max(1)
            ->set_types([
                ['type' => 'post', 'post_type' => 'sfwd-quiz'],
            ]),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $quiz_id = $fields['quiz'][0]['id'];
        ?>
        <div class="nn-quick-quiz">
            <div class="container">
                <div class="nn-quick-quiz-box p-32">
                    <h3 class="nn-quick-quiz-heading mb-30 fs-18 fw-400"><?php get_template_part('template-parts/icons/icon-quick-quiz'); ?><?php echo $fields['heading']; ?></h3>
                    <div class="nn-quick-quiz-content"><?php echo do_shortcode(sprintf('[ld_quiz quiz_id="%s"]', $quiz_id)); ?></div>
                </div>
            </div>
            <?php get_template_part('template-parts/tmpl-quiz-result', '', [
                'user_id' => get_current_user_id(),
                'quiz_id' => $quiz_id,
            ]); ?>
        </div>
        <?php
    });
