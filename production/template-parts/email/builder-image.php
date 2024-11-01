<?php

/**
 * @var array $args
 */

$data = $args['data'];

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <td align="left" valign="top">
            <?php echo wp_get_attachment_image($data['image'], 'full'); ?>
        </td>
    </tr>
</table>
<?php \STA\Inc\EmailSettings::vertical_padding(50); ?>
