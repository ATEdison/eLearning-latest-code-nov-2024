<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

Block::make('sta-contact', 'Get In Touch')
    ->add_fields(array(
        Field::make('separator', 's1', 'Get In Touch'),
        Field::make('text', 'heading', 'Heading'),
        Field::make('rich_text', 'desc', 'Description'),
        Field::make('text', 'phone', 'Phone Number'),
        Field::make('text', 'email', 'Email'),
        Field::make('text', 'whatsapp', 'WhatsApp'),
        Field::make('text', 'twitter', 'Twitter'),
        Field::make('text', 'facebook', 'Facebook'),
        Field::make('text', 'instagram', 'Instagram'),
        Field::make('association', 'form_id', 'Form')
            ->set_max(1)
            ->set_types([
                ['type' => 'post', 'post_type' => 'wpcf7_contact_form'],
            ]),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $form_id = $fields['form_id'][0]['id'];
        $contact_type_list = ['phone', 'email', 'whatsapp', 'twitter', 'facebook', 'instagram'];
        ?>
        <div class="sta-contact py-60 py-lg-80 overflow-hidden">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-4 mb-40 mb-lg-0">
                        <h2 class="h3 ff-gotham mb-25"><?php echo $fields['heading']; ?></h2>
                        <div class="text-content mb-40"><?php echo wpautop($fields['desc']); ?></div>
                        <ul class="sta-contact-social">
                            <?php foreach ($contact_type_list as $item) {
                                $value = $fields[$item] ?? '';
                                if (!$value) {
                                    continue;
                                }

                                if ($item == 'phone') {
                                    $value = 'tel:' . $value;
                                } else if ($item == 'email') {
                                    $value = 'mailto:' . $value;
                                }

                                printf('<li><a target="_blank" class="sta-contact-social-item sta-contact-social-item-%2$s" href="%1$s"></a></li>', $value, $item);
                            } ?>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-8">
                        <?php echo do_shortcode(sprintf('[contact-form-7 id="%s"]', $form_id)); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    });
