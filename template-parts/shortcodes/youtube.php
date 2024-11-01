<?php
/**
 * @var array $args
 */

$video_url = $args['video_url'];
$caption = $args['caption'] ?? null;
?>

<div class="sta-youtube pb-35 mb-40">
    <div class="container">
        <div class="sta-youtube-iframe">
            <iframe class="lazyload" data-src="<?php echo \STA\Inc\Helpers::get_youtube_embed_url($video_url); ?>"></iframe>
        </div>
        <?php if ($caption): ?>
            <div class="sta-youtube-caption">
                <?php echo $caption; ?>
            </div>
        <?php endif; ?>
        <!--<div class="sta-youtube-download">
            <div class="d-flex justify-content-between align-items-center">
                <a href="#">Download transcript</a>
                <a href="#" class="btn btn-download"></a>
            </div>
        </div>-->
    </div>
</div>
