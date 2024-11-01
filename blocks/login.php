<?php

use Carbon_Fields\Block;
use Carbon_Fields\Field;

/**
 * @see wp_login_form()
 * @see learndash_load_login_modal_html()
 */
Block::make('sta-login', 'Login')
    ->add_fields(array(
        Field::make('separator', 's1', 'Login'),
        Field::make('image', 'bg', 'Background Image'),
        Field::make('text', 'heading', 'Heading'),
    ))
    ->set_category('sta', 'STA Blocks')
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        $login_error = $_GET['login'] ?? '';
        $user_homepage_url = get_permalink(\STA\Inc\CarbonFields\ThemeOptions::get_user_homepage_id());
        ?>
        <div class="sta-login">
            <div class="container-fluid">
                <div class="row justify-content-between position-relative">
                    <div class="col-12 col-lg-6 sta-login-left py-200 py-lg-0">
                        <div class="row">
                            <div class="col-12 col-xl-8">
                                <div class="sta-login-form py-lg-150 py-xxl-200">
                                    <?php if (is_user_logged_in()):
                                        $user = wp_get_current_user(); ?>
                                        <div><?php printf(__('Hi %s, you have already logged in.', 'sta'), $user->display_name); ?></div>
                                        <a href="<?php echo $user_homepage_url; ?>"><?php _e('Back to home', 'sta'); ?></a>
                                    <?php else: ?>
                                        <h1 class="mb-40 h2"><?php echo $fields['heading']; ?></h1>
                                        <form class="mb-30" method="post" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>">
                                            <?php echo apply_filters('login_form_top', '', []); ?>
                                            <?php if ($login_error): ?>
                                                <div class="alert alert-danger mb-30">
                                                    <?php switch ($login_error) {
                                                        case 'sta_unverified':
                                                            echo \STA\Inc\Translator::user_unverified();
                                                            break;
                                                        default:
                                                            _e('Invalid email or password!', 'sta');
                                                            break;

                                                    } ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="mb-30">
                                                <label class="form-label" for="loginEmail"><?php _e('Email Address', 'sta'); ?></label>
                                                <input class="form-control" type="text" id="loginEmail" name="log">
                                            </div>
                                            <div class="mb-25">
                                                <label class="form-label" for="loginPassword"><?php _e('Password', 'sta'); ?></label>
                                                <input class="form-control" type="password" id="loginPassword" name="pwd">
                                            </div>
                                            <div class="mb-30">
                                                <input id="loginRememberMe" type="checkbox" class="form-check-input" value="forever">
                                                <label for="loginRememberMe" class="form-check-label"><?php _e('Keep me signed in on this device', 'sta'); ?></label>
                                            </div>
                                            <button class="btn btn-outline-green w-100" type="submit" name="wp-submit" value="<?php esc_attr_e('Login', 'sta'); ?>"><?php _e('Login', 'sta'); ?></button>
                                            <input type="hidden" name="redirect_to" value="<?php echo $user_homepage_url; ?>" />
                                        </form>
                                        <div>
                                            <a href="<?php echo get_permalink(\STA\Inc\CarbonFields\ThemeOptions::get_reset_password_page_id()); ?>"><?php _e('Forgotten your password?', 'sta'); ?></a>
                                        </div>
                                        <div>
                                            <?php printf('%1$s <a href="%2$s">%3$s</a>', __('Don\'t have an account?', 'sta'), get_permalink(\STA\Inc\CarbonFields\ThemeOptions::get_register_page_id()), __('Register here', 'sta')); ?>
                                        </div>
                                    <?php endif; ?>
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
