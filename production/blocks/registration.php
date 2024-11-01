<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

/**
 * @see wp_login_form()
 * @see learndash_load_login_modal_html()
 */
Block::make('sta-registration', 'Registration Form')
    ->add_tab('Your role', array(
        Field::make('text', 'role_heading', 'Heading'),
        Field::make('text', 'role_heading_mobile', 'Heading (Mobile)'),
        Field::make('rich_text', 'role_desc', 'Description'),
    ))
    ->add_tab('Your profile', array(
        Field::make('text', 'profile_heading', 'Heading'),
        Field::make('text', 'profile_heading_mobile', 'Heading (Mobile)'),
        Field::make('rich_text', 'profile_desc', 'Description'),
    ))
    ->add_tab('Your work', array(
        Field::make('text', 'work_heading', 'Heading'),
        Field::make('text', 'work_heading_mobile', 'Heading (Mobile)'),
        Field::make('rich_text', 'work_desc', 'Description'),
    ))
    ->add_tab('Terms of use', array(
        Field::make('text', 'terms_heading', 'Heading'),
        Field::make('text', 'terms_heading_mobile', 'Heading (Mobile)'),
        Field::make('rich_text', 'terms_desc', 'Description'),
        Field::make('rich_text', 'terms_text', 'Terms'),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $register = $_GET['register'] ?? '';
        $whitelist_register_responses = ['verified', 'check_mail'];

        $progress = [
            'role' => 'role_',
            'profile' => 'profile_',
            'work' => 'work_',
            'terms' => 'terms_',
        ];
        /**
         * @see /plugins/sfwd-lms/themes/ld30/templates/modules/login-modal.php
         * @see wp-login.php
         * @see register_new_user()
         */
        // echo do_shortcode('[learndash_login]');
        $form_handler = \STA\Inc\FormHandleInvoker::user_register_form_handler();
        // $success = $form_handler->is_success();
        $global_errors = $form_handler->get_global_errors();
        $errors = $form_handler->get_errors();
        ?>
        <div class="sta-registration pb-80">
            <div class="container-lg">

                <?php if ($register && in_array($register, $whitelist_register_responses)): ?>
                    <div class="pt-80">
                        <div class="alert alert-success">
                            <?php switch ($register) {
                                case 'verified':
                                    _e('Your account has been verified!', 'sta');
                                    break;
                                case 'check_mail':
                                    _e('Thank you for registration. Please check your inbox and follow the instructions to verify your email!', 'sta');
                                    break;
                                default:
                                    break;
                            } ?>
                        </div>
                    </div>
                <?php else: ?>
                    <form class="sta-form-registration" method="post" action="" enctype="multipart/form-data" data-step="1">
                        <?php wp_nonce_field('sta_form'); ?>
                        <input type="hidden" name="redirect_to" value="<?php esc_url(add_query_arg(['checkmail' => '1'], get_the_permalink())); ?>" />

                        <div class="d-xl-none mx-n20 sta-form-registration-progress-mobile py-20 border-bottom px-20">
                            <ul>
                                <?php foreach ($progress as $prefix) {
                                    printf('<li><span class="icon"></span>%s</li>', $fields[$prefix . 'heading_mobile']);
                                } ?>
                            </ul>
                        </div>

                        <?php if (is_array($global_errors) && !empty($global_errors)): ?>
                            <div class="pt-40">
                                <div class="alert alert-danger mb-0">
                                    <ul class="mb-0">
                                        <?php foreach ($global_errors as $message): ?>
                                            <li><?php echo $message; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div>
                            <?php foreach ($progress as $slug => $prefix): ?>
                                <div class="row sta-reg-step">
                                    <div class="col-12 col-xl-4 d-flex align-items-stretch">
                                        <div class="pb-40 pb-xl-80 pt-xl-80 ps-xl-45 sta-reg-left w-100<?php echo $slug == 'role' ? ' pt-40' : ' pt-56'; ?>">
                                            <span class="sta-reg-left-icon"></span>
                                            <h3 class="sta-reg-heading <?php printf('sta-reg-heading-%s', $slug); ?>"><?php echo $fields[$prefix . 'heading']; ?></h3>
                                            <div><?php echo wpautop($fields[$prefix . 'desc']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-8 d-flex align-items-stretch">
                                        <div class="pb-24 pb-xl-48 pt-xl-80 w-100<?php echo $slug != 'terms' ? ' border-bottom' : ''; ?>">
                                            <?php get_template_part('template-parts/registration/form', $slug, [
                                                'fields' => $fields,
                                                'errors' => $errors,
                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php
    });
