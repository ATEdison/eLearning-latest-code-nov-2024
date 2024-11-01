<?php

$user_id = is_user_logged_in() ? get_current_user_id() : 0;

if (!\STA\Inc\UserWelcome::should_display_welcome_prompt($user_id)) {
    return;
}

$step_list = \STA\Inc\CarbonFields\ThemeOptions::welcome_prompt_steps();

?>
<div class="sta-welcome-prompt">
    <div class="sta-welcome-prompt-content">
        <div class="sta-welcome-prompt-step-list mb-0">
            <?php foreach ($step_list as $step_index => $step):
                $video_url = $step['video_url'];
                $embed_url = \STA\Inc\Helpers::get_video_embed_url($video_url);
                $image_id = $step['image'];
                ?>
                <div class="sta-welcome-prompt-step">
                    <?php if ($video_url): ?>
                        <div class="sta-welcome-prompt-video" data-src="<?php echo $embed_url; ?>">
                            <?php if ($step_index < 1): ?>
                                <iframe
                                    class="lazyload"
                                    data-src="<?php echo $embed_url . '?autoplay=1&enablejsapi=1&version=3&playerapiid=ytplayer'; ?>"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                ></iframe>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="sta-welcome-prompt-image">
                            <?php echo wp_get_attachment_image($image_id, 'full'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="p-24 pt-64 text-center">
                        <h4 class="mb-24"><?php echo $step['heading']; ?></h4>
                        <div>
                            <?php echo wpautop($step['desc']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="sta-welcome-prompt-step-list-navigation"></div>
        <div class="px-24">
            <div class="sta-welcome-prompt-cta d-sm-flex justify-content-between justify-content-md-end py-24">
                <button class="btn btn-outline-green w-100 w-md-auto me-16 btn-sta-welcome-prompt-skip w-100 w-sm-auto mb-16 mb-sm-0" type="button"><?php _e('Skip welcome', 'sta'); ?></button>
                <button class="btn btn-outline-green w-100 w-md-auto btn-sta-welcome-prompt-next w-100 w-sm-auto" type="button" data-next="<?php echo htmlentities(__('Next', 'sta')); ?>" data-finish="<?php echo htmlentities(__('Finish', 'sta')); ?>"></button>
            </div>
        </div>
    </div>
</div>
