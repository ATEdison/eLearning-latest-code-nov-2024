<?php

/**
 * @var array $args
 */

$block = $args;

// printf('<pre>%s</pre>', var_export($block, true));

$image_id = $block['attrs']['id'];
$html = trim($block['innerHTML']);

preg_match('@<figcaption>(.*)</figcaption>@', $html, $matches);

$caption = $matches[1] ?? '';

// var_dump($caption);

// $image_url = wp_get_attachment_image_url($image_id, 'full');
// $image_url = str_replace(home_url(), '', $image_url);
$image_path = get_attached_file($image_id);
$type = pathinfo($image_path, PATHINFO_EXTENSION);
$data = file_get_contents($image_path);
$image_url = 'data:image/' . $type . ';base64,' . base64_encode($data);
?>
<p style="text-align: center;">
    <img src="<?php echo $image_url; ?>" style="margin-bottom: 0;">
    <?php if ($caption): ?>
        <br><span style="display: inline-block;margin-top: 0;font-style: italic;"><?php echo $caption; ?></span>
    <?php endif; ?>
</p>
