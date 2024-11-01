<?php

/**
 * @var $args
 */

$icon_url = $args['icon_url'];
$heading = $args['heading'];
$btn_label = $args['btn_label'];
$btn_url = $args['btn_url'];

?>

<div class="sta-agent-quick-link p-24 p-lg-30 p-xl-40">
    <div class="sta-agent-quick-link-image mb-24"><img src="<?php echo $icon_url; ?>" alt=""></div>
    <h4 class="h7 mb-32"><?php echo $heading; ?></h4>
    <a class="sta-agent-quick-link-link" href="<?php echo $btn_url; ?>"><?php echo $btn_label; ?></a>
</div>
