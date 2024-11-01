(function ($) {
  $(document).ready(function () {
    if ($('.wpProQuiz_content').length < 1) {
      return;
    }

    var templateQuizResultsFail = wp.template('sta-quiz-results-failed');
    var templateQuizResultsPassed = wp.template('sta-quiz-results-passed');
    var templateQuickQuizMessage = wp.template('sta-quick-quiz-message');

    $(document).on('learndash-quiz-finished', '.wpProQuiz_content', function (e) {
      var $holder = $(this);
      var isQuickQuiz = $holder.closest('.nn-quick-quiz').length > 0;

      if (isQuickQuiz) {
        quickQuizResult($holder, e.values);
        return;
      }

      // console.log(arguments);
      // console.log(e);
      switch (e.values.status) {
        case 'failed':
          quizResultsFailed($holder, e.values);
          break;
        case 'passed':
          quizResultsPassed($holder, e.values);
          break;
      }
    });

    initQuizAttemptResults();

    // test
    // quizResultsFailed($('.wpProQuiz_content'), { results: { comp: { correctQuestions: 1 } } });
    // quizResultsPassed($('.wpProQuiz_content'), { results: { comp: { correctQuestions: 1 } } });

    function initQuizAttemptResults() {
      if (!window.hasOwnProperty('nnQuizAttemptResults')) {
        return;
      }
      $('.wpProQuiz_content').each(function () {
        var $holder = $(this);
        var quizMeta = JSON.parse($holder.attr('data-quiz-meta'));
        var quizPostId = quizMeta.quiz_post_id;
        if (!window.nnQuizAttemptResults.hasOwnProperty(quizPostId)) {
          return;
        }
        var quizAttemptResult = window.nnQuizAttemptResults[quizPostId];

        $holder.find('.wpProQuiz_text').css('display', 'none');
        $holder.find('.wpProQuiz_results').css('display', '');
        $holder.addClass('ld-quiz-result-passed');
        if (typeof continue_details !== 'undefined') {
          $holder.find('.quiz_continue_link').html(continue_details);
          $holder.find('.quiz_continue_link').show();
        }

        $holder.trigger({
          type: 'learndash-quiz-finished',
          values: {
            status: 'passed',
            item: $holder,
            results: {
              comp: { correctQuestions: quizAttemptResult.score },
            },
          },
        });
      });
    }

    function quizResultsPassed($holder, values) {
      // console.log('quizResultsFailed', $holder, values);
      var $quizResults = $holder.find('.sta-quiz-results');
      var questionCount = $holder.find('.wpProQuiz_list > li').length;
      var correctCount = values.results.comp.correctQuestions;
      $quizResults.html(templateQuizResultsPassed({
        correct: correctCount,
        count: questionCount,
      }));
      $quizResults.addClass('active');
    }

    function quizResultsFailed($holder, values) {
      // console.log('quizResultsFailed', $holder, values);
      var $quizResults = $holder.find('.sta-quiz-results');
      var questionCount = $holder.find('.wpProQuiz_list > li').length;
      var correctCount = values.results.comp.correctQuestions;
      $quizResults.html(templateQuizResultsFail({
        correct: correctCount,
        count: questionCount,
      }));
      $quizResults.addClass('active');
    }

    function quickQuizResult($holder, values) {
      // console.log('quizResultsFailed', $holder, values);
      var $quizResults = $holder.find('.sta-quiz-results');
      var questionCount = $holder.find('.wpProQuiz_list > li').length;
      var correctCount = values.results.comp.correctQuestions;
      $quizResults.html(templateQuickQuizMessage({
        correct: correctCount,
        count: questionCount,
      }));
      $quizResults.addClass('active');
    }
  });
})(jQuery);
