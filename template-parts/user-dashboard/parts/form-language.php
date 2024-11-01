<?php

/**
 * @var array $args
 */

$user_id = $args['user_id'] ?? 0;
$data = $args['data'] ?? [];

$form_handler = \STA\Inc\FormHandleInvoker::user_language_settings_form_handler();
$success = $form_handler->is_success();
$global_errors = $form_handler->get_global_errors();
$errors = $form_handler->get_errors();

/**
 * @var SitePress $sitepress
 */
global $sitepress;
$language_list = $sitepress->get_active_languages();
$language_list = $sitepress->order_languages($language_list);
// \STA\Inc\Helpers::log($language_list);
?>

<form class="sta-form-general-preferences row" action="" method="post">
    <?php wp_nonce_field('sta_form'); ?>

    <?php if ($success): ?>
        <div class="col-12 mb-40">
            <div class="alert alert-success mb-0">
                <?php _e('Your language settings have been updated!', 'sta'); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (is_array($global_errors) && !empty($global_errors)): ?>
        <div class="col-12 mb-40">
            <div class="alert alert-danger mb-0">
                <ul>
                    <?php foreach ($global_errors as $message): ?>
                        <li><?php echo $message; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-12 col-xxl-6">
        <div class="mb-24 mb-lg-32">
            <label for="userLanguage" class="form-label"><?php _e('Preferred Language', 'sta'); ?></label>
            <select id="userLanguage" class="form-select mt-0 me-16" type="checkbox" name="preferred_language">
                <?php foreach ($language_list as $code => $item) {
                    printf('<option value="%1$s"%2$s>%3$s (%4$s)</option>', $item['default_locale'], selected($item['default_locale'], $data['preferred_language'] ?? ''), $item['native_name'], $code);
                } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-outline-green w-100 w-md-auto" name="sta_form" value="user_language_settings"><?php _e('Update', 'sta'); ?></button>
    </div>
</form>
