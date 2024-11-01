<?php

/**
 * @var array $args
 */
$fields = $args['fields'];

// $error = isset($_REQUEST['error']) ? sanitize_text_field($_REQUEST['error']) : '';
// $reset_password_request_sent = isset($_REQUEST['checkemail']) ? sanitize_text_field($_REQUEST['checkemail']) : '';
// $reset_password_request_sent = $reset_password_request_sent == 'confirm';

$confirm = $_GET['confirm'] ?? '';
?>

<h1 class="mb-40 h2"><?php echo $fields['heading']; ?></h1>
<?php if ($fields['desc']): ?>
    <div class="mb-30 text-content"><?php echo wpautop($fields['desc']); ?></div>
<?php endif; ?>

<?php if ($confirm): ?>
    <div class="alert alert-success mb-30">
        <?php _e('If an account with this email address exists, we will send you a link to rest your password.', 'sta'); ?>
    </div>
<?php endif; ?>

<?php if (!$confirm): ?>
    <form class="mb-30" method="post" action="<?php echo esc_url(network_site_url('wp-login.php?action=lostpassword', 'login_post')); ?>">
        <?php if (isset($_GET['login']) && $_GET['login'] == 'failed'): ?>
            <div class="alert alert-danger mb-30">
                <?php _e('Invalid email or password!', 'sta'); ?>
            </div>
        <?php endif; ?>
        <div class="mb-30">
            <label class="form-label" for="loginEmail"><?php _e('Email Address', 'sta'); ?></label>
            <input class="form-control" type="text" id="loginEmail" name="user_login">
        </div>
        <button class="btn btn-outline-green w-100" type="submit" name="wp-submit" value="<?php esc_attr_e('Get New Password', 'sta'); ?>"><?php _e('Submit', 'sta'); ?></button>
        <input type="hidden" name="redirect_to" value="<?php add_query_arg(['ld-resetpw' => 'true'], get_permalink()); ?>" />
    </form>
<?php endif; ?>

<a href="<?php echo get_permalink(\STA\Inc\CarbonFields\ThemeOptions::get_login_page_id()); ?>"><?php _e('Return to login', 'sta'); ?></a>
