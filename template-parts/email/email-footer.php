<?php

$data = \STA\Inc\EmailSettings::get_footer_settings();

$logo_id = $data['logo_id'];
$text = wpautop($data['text']);
$text = \STA\Inc\EmailSettings::p_style($text, 'color:rgba(0,0,0,0.6);margin:0 0 10px 0;');

$social = \STA\Inc\CarbonFields\ThemeOptions::get_social();
$youtube_url = $social['youtube'] ?? '';
$twitter_url = $social['twitter'] ?? '';
$instagram_url = $social['instagram'] ?? '';
$facebook_url = $social['facebook'] ?? '';
?>

<!-- footer top -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#000" style="background-color: #000;color: #fff;">
    <tr>
        <!-- padding left -->
        <?php \STA\Inc\EmailSettings::side_padding(); ?>

        <!-- content -->
        <td align="left" valign="top">
            <!-- padding top -->
            <?php \STA\Inc\EmailSettings::vertical_padding(30); ?>

            <!-- content -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed;">
                <tr>
                    <td align="left" valign="top">
                        <table align="left" border="0" cellspacing="0" cellpadding="0" width="180" style="width: 180px;">
                            <tr>
                                <td>
                                    <a href="<?php echo home_url(); ?>"><?php echo wp_get_attachment_image($logo_id, 'full'); ?></a>
                                </td>
                            </tr>
                        </table>
                        <table align="right" valign="middle" border="0" cellspacing="0" cellpadding="0" width="140" style="width: 140px;">
                            <tr>
                                <td align="right" valign="middle">
                                    <?php \STA\Inc\EmailSettings::vertical_padding(15); ?>
                                    <?php if ($youtube_url): ?>
                                        <a href="<?php echo $youtube_url; ?>" style="margin: 0 15px 0 0;"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/email-youtube.png" width="16" height="16"></a>
                                    <?php endif; ?>
                                    <?php if ($twitter_url): ?>
                                        <a href="<?php echo $twitter_url; ?>" style="margin: 0 15px 0 0;"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/email-twitter.png" width="16" height="16"></a>
                                    <?php endif; ?>
                                    <?php if ($instagram_url): ?>
                                        <a href="<?php echo $instagram_url; ?>" style="margin: 0 15px 0 0;"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/email-instagram.png" width="16" height="16"></a>
                                    <?php endif; ?>
                                    <?php if ($facebook_url): ?>
                                        <a href="<?php echo $facebook_url; ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/email-facebook.png" width="16" height="16"></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- padding bottom -->
            <?php \STA\Inc\EmailSettings::vertical_padding(30); ?>
        </td>

        <!-- padding right -->
        <?php \STA\Inc\EmailSettings::side_padding(); ?>
    </tr>
</table>

<!-- footer bottom -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed;">
    <tr>
        <!-- padding left -->
        <?php \STA\Inc\EmailSettings::side_padding(); ?>

        <!-- content -->
        <td align="left" valign="top">
            <!-- padding top -->
            <?php \STA\Inc\EmailSettings::vertical_padding(30); ?>

            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout: fixed;font-size: 13px;line-height:1.38;">
                <tr>
                    <td>
                        <?php echo $text; ?>
                        <a href="<?php echo home_url(); ?>"><?php echo home_url(); ?></a>
                    </td>
                </tr>
            </table>

            <!-- padding bottom -->
            <?php \STA\Inc\EmailSettings::vertical_padding(30); ?>
        </td>

        <!-- padding right -->
        <?php \STA\Inc\EmailSettings::side_padding(); ?>
    </tr>
</table>

