<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

/**
 * @see wp_login_form()
 * @see learndash_load_login_modal_html()
 * @see wp_lostpassword_url()
 */
Block::make('sta-reset-password', 'Reset Password')
    ->add_tab('Reset Password', [
        Field::make('image', 'bg', 'Background Image'),
        Field::make('text', 'heading', 'Heading'),
        Field::make('rich_text', 'desc', 'Description'),
    ])
    ->add_tab('Set your password', [
        Field::make('text', 'sp_heading', 'Heading'),
        Field::make('rich_text', 'sp_desc', 'Description'),
    ])
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $login = $_GET['login'] ?? '';
        $key = $_GET['key'] ?? '';
        $user = check_password_reset_key($key, $login);
        $is_valid_key = $user instanceof WP_User;

        // echo do_shortcode('[learndash_login]');
        ?>
        <div class="sta-login">
            <div class="container-fluid">
                <div class="row justify-content-between position-relative">
                    <div class="col-12 col-lg-6 sta-login-left py-200 py-lg-0">
                        <div class="row">
                            <div class="col-12 col-xl-8">
                                <div class="sta-login-form py-lg-150 py-xxl-200">
                                    <?php if ($is_valid_key) {
                                        get_template_part('template-parts/reset-password', '', [
                                            'fields' => $fields,
                                            'login' => $login,
                                            'key' => $key,
                                        ]);
                                    } else {
                                        get_template_part('template-parts/lost-password', '', ['fields' => $fields]);
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 sta-login-right lazyload" data-bg="<?php echo wp_get_attachment_image_url($fields['bg'], 'full'); ?>"></div>
                </div>
            </div>
        </div>
        <?php
    });
