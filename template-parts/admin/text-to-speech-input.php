<?php
$post_id = $_GET['post'] ?? 0;
if (!$post_id) {
    return;
}

$data = \STA\Inc\GoogleTextToSpeech::get_post_data($post_id);

$audio_url = $data['audio_url'] ?? '';
$text_to_speech = $data['text'];
?>

<div class="sta-text-to-speech">
    <div class="sta-text-to-speech-audio" style="margin-bottom: 20px;">Audio URL: <a href="<?php echo $audio_url; ?>" target="_blank"><?php echo $audio_url; ?></a></div>
    <label for="sta_text_to_speech_input" class="cf-field__label" style="display: block;">Text to speech</label>
    <textarea id="sta_text_to_speech_input" name="sta_text_to_speech" class="cf-text__input" rows="10"><?php echo esc_textarea($text_to_speech); ?></textarea>
    <div>
        <em class="cf-field__help">check markup <a target="_blank" href="https://cloud.google.com/text-to-speech/docs/ssml">here</a></em>
    </div>
    <button type="submit" class="sta-btn-render-audio button button-primary" style="margin-top: 15px;">Render Audio</button>
    <div class="sta-text-to-speech-message"></div>
</div>
