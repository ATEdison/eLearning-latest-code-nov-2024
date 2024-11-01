<?php
/**
 * @var array $args
 */

$fields = $args['fields'];
$login = $args['login'] ?? '';
$key = $args['key'] ?? '';

$form_handler = \STA\Inc\FormHandleInvoker::user_reset_password_form_handler();
// $success = $form_handler->is_success();
$global_errors = $form_handler->get_global_errors();
$errors = $form_handler->get_errors();
?>

<h1 class="mb-40 h2"><?php echo $fields['sp_heading']; ?></h1>
<?php if ($fields['sp_desc']): ?>
    <div class="mb-30 text-content"><?php echo wpautop($fields['sp_desc']); ?></div>
<?php endif; ?>

<form class="sta-form-reset-password" method="post" action="">
    <?php wp_nonce_field('sta_form'); ?>
    <input type="hidden" name="login" value="<?php echo esc_attr($login); ?>">
    <input type="hidden" name="key" value="<?php echo esc_attr($key); ?>">

    <?php if (is_array($global_errors) && !empty($global_errors)): ?>
        <div class="alert alert-danger mb-30">
            <ul class="mb-0">
                <?php foreach ($global_errors as $message): ?>
                    <li><?php echo $message; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="mb-30">
        <?php get_template_part('template-parts/form/field-password', '', [
            'label' => __('New Password', 'sta'),
            'errors' => $errors,
        ]); ?>
    </div>
    <div class="mb-30">
        <label class="form-label" for="rp_password_confirm"><?php _e('Re-enter New Password', 'sta'); ?></label>
        <input class="form-control" type="password" id="rp_password_confirm" name="password_confirm" required>
    </div>
    <button class="btn btn-outline-green w-100" type="submit" name="sta_form" value="user_reset_password"><?php _e('Save and login', 'sta'); ?></button>
</form>
