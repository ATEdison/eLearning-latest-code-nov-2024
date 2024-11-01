<?php

/**
 * @var array $args
 */

$data = $args['data'];

// printf('<pre>%s</pre>', var_export($args, true));

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
    <tbody>
        <tr>
            <?php \STA\Inc\EmailSettings::side_padding(); ?>
            <td>
                <!-- heading -->
                <h2 style="margin: 0 0 30px 0;font-size: 20px;"><?php echo $data['heading']; ?></h2>

                <!-- border top -->
                <?php \STA\Inc\EmailSettings::horizontal_line(); ?>
                <?php \STA\Inc\EmailSettings::vertical_padding(30); ?>

                <!-- content -->
                <?php foreach ($data['items'] as $item_index => $item): ?>
                    <?php if ($item_index > 0) {
                        \STA\Inc\EmailSettings::vertical_padding(30);
                    } ?>
                    <!-- icon text -->
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
                        <tr>
                            <!-- icon -->
                            <td width="48" style="width: 48px;" align="left" valign="top">
                                <img src="<?php echo wp_get_attachment_image_url($item['image'], 'full'); ?>" alt="" style="width: 48px;height: auto;">
                            </td>
                            <!-- spacing -->
                            <td width="24" style="width: 24px;">&nbsp;</td>
                            <!-- text -->
                            <td align="left" valign="top">
                                <h4 style="font-size: 18px; margin: 0;"><?php echo $item['heading']; ?></h4>
                                <p style="margin: 0;"><?php echo $item['desc']; ?></p>
                            </td>
                        </tr>
                    </table>
                <?php endforeach; ?>

                <!-- border bottom -->
                <?php \STA\Inc\EmailSettings::vertical_padding(30); ?>
                <?php \STA\Inc\EmailSettings::horizontal_line(); ?>
                <?php \STA\Inc\EmailSettings::vertical_padding(30); ?>
            </td>
            <?php \STA\Inc\EmailSettings::side_padding(); ?>
        </tr>
    </tbody>
</table>
