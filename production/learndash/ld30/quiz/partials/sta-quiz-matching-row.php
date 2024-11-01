<?php

/**
 * @var array $args
 */

$msacw_value = $args['msacw_value'] ?? 0;
$answer_text = $args['answer_text'] ?? 0;
?>
<div class="wpProQuiz_mextrixTr row gx-24">
    <div class="col-4 col-sm-auto">
        <div class="wpProQuiz_maxtrixSortText"><?php echo $answer_text; ?></div>
    </div>
    <div class="col-8 col-sm-auto flex-sm-fill wpProQuiz_mextrixTr-right">
        <ul class="wpProQuiz_maxtrixSortCriterion" data-placeholder="<?php echo htmlentities(__('Drag your answer here', 'sta')); ?>"></ul>
    </div>
</div>
