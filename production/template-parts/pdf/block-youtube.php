<?php

/**
 * @var array $args
 */

$block = $args;

// printf('<pre>%s</pre>', var_export($block, true));

$video_url = $block['attrs']['data']['video_url'];
$caption = $block['attrs']['data']['caption'];
?>
<p style="text-align: center;"><a href="<?php echo $video_url; ?>"><?php echo $video_url; ?></a><br><?php echo $caption; ?></p>
