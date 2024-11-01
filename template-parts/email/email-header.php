<?php

$logo_id = \STA\Inc\EmailSettings::get_header_logo_image_id();

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <!-- padding left -->
        <?php \STA\Inc\EmailSettings::side_padding(); ?>

        <!-- content -->
        <td align="left" valign="top">
            <!-- padding top -->
            <?php \STA\Inc\EmailSettings::vertical_padding(30); ?>

            <!-- header -->
            <table style="line-height: 1;">
                <tr>
                    <td align="left" valign="top">
                        <a href="<?php echo home_url(); ?>"><?php echo wp_get_attachment_image($logo_id, 'full'); ?></a>
                    </td>
                </tr>
            </table>

            <!-- padding bottom -->
            <?php \STA\Inc\EmailSettings::vertical_padding(25); ?>
        </td>

        <!-- padding right -->
        <?php \STA\Inc\EmailSettings::side_padding(); ?>
    </tr>
</table>

