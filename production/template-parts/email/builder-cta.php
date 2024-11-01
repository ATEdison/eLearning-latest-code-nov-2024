<?php

/**
 * @var array $args
 */

$data = $args['data'];
$placeholders = $args['placeholders'];

// printf('<pre>%s</pre>', var_export($args, true));

$btn_url = $data['btn_url'];
$btn_url = \STA\Inc\EmailSettings::apply_placeholders($btn_url, $placeholders);
$btn_label = $data['btn_label'];

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
    <tbody>
        <tr>
            <?php \STA\Inc\EmailSettings::side_padding(); ?>
            <td>
                <a href="<?php echo $btn_url; ?>" style="color:#000;text-decoration:none;box-sizing:border-box;border-radius:4px;display:inline-flex;font-style:normal;font-weight: 700;font-size:inherit;margin:0;outline:none;padding:12px 24px;text-align:center;vertical-align:middle;white-space:nowrap;border: 2px solid #000;line-height: 1;"><?php echo $btn_label; ?></a>
            </td>
            <?php \STA\Inc\EmailSettings::side_padding(); ?>
        </tr>
    </tbody>
</table>
<?php \STA\Inc\EmailSettings::vertical_padding(40); ?>
