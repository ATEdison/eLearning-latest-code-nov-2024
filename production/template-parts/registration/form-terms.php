<?php
/**
 * @var array $args
 */

// printf('<pre>%s</pre>', var_export($args, true));
$errors = $args['errors'] ?? [];

$data = $_POST;
$terms_1 = $data['terms_1'] ?? '';
$terms_2 = $data['terms_2'] ?? '';
?>

<div class="fs-14 fst-italic mb-32">
    <?php echo wpautop($args['terms_text']); ?>
</div>

<div class="sta-reg-agreements p-24 p-xl-32">
    <div class="row align-items-center">
        <div class="col-12 col-md-9 mb-32 mb-md-0">
            <div class="mb-12 sta-label-black">
                <input class="form-check-input" id="sta_reg_terms1" type="checkbox" name="terms_1" value="yes" <?php checked('yes', $terms_1); ?> required>
                <label class="form-check-label" for="sta_reg_terms1"><?php _e('I agree to the terms and conditions described above', 'sta'); ?></label>
                <?php \STA\Inc\FormHelpers::field_error('terms_1', $errors); ?>
            </div>
            <div>
                <input class="form-check-input" id="sta_reg_terms2" type="checkbox" name="terms_2" value="yes" <?php checked('yes', $terms_2); ?>>
                <label class="form-check-label" for="sta_reg_terms2"><?php _e('I wish to be contacted with news and updates regarding Saudi Expert', 'sta'); ?></label>
            </div>
        </div>
        <div class="col-12 col-md-3 text-end">
            <button type="submit" class="btn btn-outline-green w-100 w-md-auto" name="sta_form" value="user_register"><?php _e('Register', 'sta'); ?></button>
        </div>
    </div>
</div>
