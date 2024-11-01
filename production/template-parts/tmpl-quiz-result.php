<?php

/**
 * @var array $args
 */

$user_id = $args['user_id'] ?? null;
$quiz_id = $args['quiz_id'] ?? null;

$restart_quiz = $_GET['restart_quiz'] ?? null;
if ($restart_quiz != $quiz_id) {
    $data = learndash_get_user_quiz_attempt($user_id, ['quiz' => $quiz_id, 'pass' => 1]);
    $data = is_array($data) ? $data : [];
    $data = array_values($data);
    $last_item = array_pop($data);

    if ($last_item) {
        ?>
        <script type="text/javascript">
            window.nnQuizAttemptResults = window.nnQuizAttemptResults || {};
            window.nnQuizAttemptResults[<?php echo $quiz_id; ?>] = <?php echo wp_json_encode($last_item); ?>;
        </script>
        <?php
    }
}

global $nn_added_quiz_result_template;
if ($nn_added_quiz_result_template) {
    return;
}
$nn_added_quiz_result_template = true;
?>

<script type="text/html" id="tmpl-sta-quiz-results-failed">
    <div class="sta-quiz-results-content">
        <h4 class="mb-24"><?php _e('Your results', 'sta'); ?></h4>
        <div class="sta-quiz-results-message">
            <?php echo \STA\Inc\CarbonFields\ThemeOptions::get_quiz_failed_message(); ?>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-sta-quiz-results-passed">
    <div class="sta-quiz-results-content passed">
        <h4 class="mb-24"><?php _e('Congratulations!', 'sta'); ?></h4>
        <div class="sta-quiz-results-message">
            <?php echo \STA\Inc\CarbonFields\ThemeOptions::get_quiz_passed_message(); ?>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-sta-quick-quiz-message">
    <div class="sta-quiz-results-content">
        <h4 class="mb-24"><?php _e('Your results', 'sta'); ?></h4>
        <div class="sta-quiz-results-message">
            <?php echo \STA\Inc\CarbonFields\ThemeOptions::get_quick_quiz_message(); ?>
        </div>
    </div>
</script>
