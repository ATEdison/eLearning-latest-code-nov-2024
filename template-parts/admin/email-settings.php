<?php

/**
 * @var array $args
 */

$type = $args['type'];
$placeholders = $args['placeholders'];

$placeholders_string = implode(']</code>, <code>[', $placeholders);
$placeholders_string = $placeholders_string ? '<code>[' . $placeholders_string . ']</code>' : '';

?>

<div style="margin-bottom: 20px;">
    <div style="margin-bottom: 20px;">Note: Content must be saved first before being able to send test emails</div>
    <input type="email" name="sta_test_email" placeholder="Email" aria-label="Email">
    <button type="button" class="button sta-send-test-email" name="type" value="<?php echo $type; ?>">Send Test Email</button>
    <div class="sta-test-email-message"></div>
</div>

<?php if ($placeholders_string): ?>
    <div>Available placeholders: <?php echo $placeholders_string; ?></div>
<?php endif; ?>
