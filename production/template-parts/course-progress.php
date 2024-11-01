<?php

/**
 * @var array $args
 */

$progress = $args['progress'];
$is_lapsed = $progress['is_lapsed'];
?>

<div class="course-preview-progress" data-value="<?php echo $is_lapsed ? 'lapsed' : $progress['completed_percentage']; ?>">
    <div class="progress mb-8">
        <div class="progress-bar" role="progressbar" style="width: <?php echo $is_lapsed ? 100 : $progress['completed_percentage']; ?>%" aria-valuenow="<?php echo $progress['completed_percentage']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div>
        <?php if ($is_lapsed) {
            _e('Retake this module to maintain achievement', 'sta');
        } else {
            printf(__('%s%% complete', 'sta'), $progress['completed_percentage']);
        } ?>
    </div>
</div>
