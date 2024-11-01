<?php

/**
 * @var array $args
 */

$data = $args['data'];
$placeholders = $args['placeholders'];

// printf('<pre>%s</pre>', var_export($args, true));
$text = $data['text'];
if (!$text) {
    return;
}

$text = wpautop($text);

// preg_match_all('@<(h1|h2|h3|h4|h5|h6|p)>@', $text, $matches);
// echo htmlentities(json_encode($matches));
// echo htmlentities($text);

$text = \STA\Inc\EmailSettings::apply_placeholders($text, $placeholders);

$text = preg_replace('@<h1>@', '<h1 style="font-size:30px;margin: 0 0 30px 0;">', $text);
$text = preg_replace('@<h2>@', '<h2 style="font-size:20px;margin: 0 0 30px 0;">', $text);
$text = \STA\Inc\EmailSettings::p_style($text);


?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <?php \STA\Inc\EmailSettings::side_padding(); ?>
        <td align="left" valign="top">
            <?php echo $text; ?>
        </td>
        <?php \STA\Inc\EmailSettings::side_padding(); ?>
    </tr>
</table>
