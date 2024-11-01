<?php

/**
 * @var array $args
 */

$label = $args['label'];
$field_name = $args['field_name'];
$image_id = $args['image_id'] ?? 0;
$errors = $args['errors'] ?? [];
// $image_id = null;
?>

<label for="personalProfilePicture" class="form-label"><?php echo $label; ?></label>
<div class="p-24 p-xl-32 sta-image-upload d-xxl-flex">
    <input type="file" name="<?php echo $field_name . '_file'; ?>" accept="image/jpeg,image/png">
    <div class="sta-image-upload-preview mb-30 mb-xxl-0 me-xxl-30">
        <?php echo \STA\Inc\UserDashboard::profile_image($image_id); ?>
        <div class="sta-image-upload-preview-new"></div>
    </div>
    <div class="d-flex align-items-center">
        <div>
            <div class="mb-25">
                <button type="button" class="btn btn-trash me-10"></button>
                <button type="button" class="btn btn-outline-green btn-upload-image"><?php _e('Upload Photo', 'sta'); ?></button>
            </div>
            <div><?php _e('Square (1:1) format in JPEG or PNG format and. Maximum dimensions of 1080x1080 recommended. Maximum file size is 1MB.', 'sta'); ?></div>
        </div>
    </div>
</div>
<?php \STA\Inc\FormHelpers::field_error($field_name, $errors); ?>
