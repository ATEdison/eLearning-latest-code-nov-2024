<?php

namespace STA\Inc;

use Carbon_Fields\Field;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\Voice;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use STA\Inc\CarbonFields\ThemeOptions;

class GoogleTextToSpeech {

    private static $instance;

    private const OPTION_VOICE_OPTIONS = '_sta_google_text_to_speech_voice_options';

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('save_post', [$this, 'on_save_post'], PHP_INT_MAX);
        add_action('admin_print_footer_scripts', [$this, 'sta_text_to_speech_script'], PHP_INT_MAX);
        add_action('wp_ajax_sta_text_to_speech', [$this, 'ajax_text_to_speech']);
    }

    /**
     * save text_to_speech data
     * @param int $post_id
     * @see template-parts/admin/text-to-speech-input.php
     */
    public function on_save_post($post_id) {
        // var_dump($_POST); die;
        $text_to_speech = $_POST['sta_text_to_speech'] ?? '';
        // var_dump($text_to_speech); die;
        update_post_meta($post_id, '_sta_text_to_speech', $text_to_speech);
    }

    public static function get_post_data($post_id) {
        return [
            'audio_url' => get_post_meta($post_id, '_sta_audio_url', true),
            'text' => get_post_meta($post_id, '_sta_text_to_speech', true),
        ];
    }

    public static function get_post_audio($post_id) {
        return get_post_meta($post_id, '_sta_audio_url', true);
    }

    public function ajax_text_to_speech() {
        if (!is_user_logged_in() || !current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'Unauthorized'], 401);
            return;
        }
        $nonce = $_POST['nonce'] ?? '';
        if (!wp_verify_nonce($nonce, 'sta_text_to_speech')) {
            wp_send_json_error(['message' => 'invalid nonce'], 400);
            return;
        }

        $post_id = $_POST['post_id'] ?? 0;
        $post_id = is_numeric($post_id) ? intval($post_id) : 0;
        if (!$post_id) {
            return;
        }

        $text = $_POST['text'] ?? '';

        $text = wp_unslash($text);

        $ssml = '<speak>' . $text . '</speak>';
        $ssml = trim($ssml);

        // $audio_url = '#';
        $audio_url = self::text_to_speech($post_id, $ssml);
        if (is_wp_error($audio_url)) {
            wp_send_json_error(['message' => $audio_url->get_error_message()], 400);
            return;
        }

        update_post_meta($post_id, '_sta_text_to_speech', $text);
        update_post_meta($post_id, '_sta_audio_url', $audio_url);

        wp_send_json_success([
            'post_id' => $post_id,
            'text' => $text,
            'audio_url' => $audio_url,
        ]);
    }

    public function sta_text_to_speech_script() {
        global $current_screen;
        // printf('xxx<pre style="position: absolute;background-color: #000;padding: 20px;color: #fff;">%s</pre>', var_export($current_screen, true));
        $whitelist_pages = [
            'edit.php?post_type=' . CptCourseLesson::$post_type,
            'edit.php?post_type=' . CptCourseTopic::$post_type,
        ];
        if (!in_array($current_screen->parent_file, $whitelist_pages)) {
            return;
        }
        $post_id = $_GET['post'] ?? 0;
        if (!$post_id) {
            return;
        }
        ?>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    var $holder = $('.sta-text-to-speech');
                    var $input = $('#sta_text_to_speech_input');
                    var $btn = $('.sta-btn-render-audio');
                    var postId = $('#post_ID').val();
                    var $message = $('.sta-text-to-speech-message');
                    var $audioUrl = $('.sta-text-to-speech-audio a');
                    var loading = false;

                    $(document).on('click', '.sta-btn-render-audio', function () {
                        ajaxTextToSpeech();
                    });

                    function ajaxTextToSpeech() {
                        if (loading) {
                            return;
                        }
                        loading = true;
                        $message.html('Loading...');

                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                nonce: '<?php echo wp_create_nonce('sta_text_to_speech'); ?>',
                                action: 'sta_text_to_speech',
                                text: $input.val(),
                                post_id: postId,
                            },
                            success: function (response) {
                                // console.log(response);
                                if (response.success) {
                                    $message.html('Audio is rendered.');
                                    $audioUrl.attr('href', response.data.audio_url).text(response.data.audio_url);
                                }
                            },
                            error: function (xhr) {
                                $message.html('Failed to render Audio.<code>' + JSON.stringify(xhr) + '</code>');
                            },
                            complete: function () {
                                loading = false;
                            }
                        });
                    }
                });
            })(jQuery);
        </script>
        <?php
    }

    public static function theme_options($lang) {
        return [
            // do not translate api field
            Field::make('textarea', 'google_text_to_speech_key', 'Service Account JSON Key')
                ->set_help_text('Create a new key <a target="_blank" href="https://cloud.google.com/iam/docs/creating-managing-service-account-keys">here</a>'),
            Field::make('select', ThemeOptions::meta_field('google_text_to_speech_voice', $lang), 'Voice')
                ->set_help_text('Demo can be found <a href="https://cloud.google.com/text-to-speech/" target="_blank">here</a>')
                ->set_options(self::class . '::voice_options'),
        ];
    }

    public static function get_voice_type() {
        $voice_type = carbon_get_theme_option(ThemeOptions::meta_field('google_text_to_speech_voice'));
        $voice_list = self::voice_list();
        return $voice_list[$voice_type];
    }

    public static function voice_options() {
        // $voice_list = self::voice_list(true);
        $voice_list = self::voice_list();
        $data = [];
        foreach ($voice_list as $name => $settings) {
            // $data[$name] = sprintf('%s - %s - %s', $settings['label'], $settings['language_code'], $settings['gender']);
            $data[$name] = sprintf('%s - %s', $settings['name'], $settings['gender_label']);
        }
        return $data;
    }

    private static function voice_list($refresh = false) {
        if (!$refresh) {
            $data = get_option(self::OPTION_VOICE_OPTIONS);
            if (is_array($data) && !empty($data)) {
                return $data;
            }
        }

        $data = self::api_load_voice_options();
        if (is_array($data) && !empty($data)) {
            // Helpers::log_to_file($data, false);
            update_option(self::OPTION_VOICE_OPTIONS, $data);
        }
        return $data;
    }

    private static function api_load_voice_options() {
        try {
            $client = self::client();
            // perform list voices request
            $response = $client->listVoices();
            $voices = $response->getVoices();

            /**
             * @var Voice $voice
             */
            $data = [];
            foreach ($voices as $voice) {
                // display the voice's name. example: tpc-vocoded
                // printf('Name: %s' . PHP_EOL, $voice->getName());

                // display the supported language codes for this voice. example: 'en-US'
                // foreach ($voice->getLanguageCodes() as $languageCode) {
                //     printf('Supported language: %s' . PHP_EOL, $languageCode);
                // }

                // SSML voice gender values from TextToSpeech\V1\SsmlVoiceGender
                // $ssmlVoiceGender = ['SSML_VOICE_GENDER_UNSPECIFIED', 'MALE', 'FEMALE', 'NEUTRAL'];

                // display the SSML voice gender
                // $gender = $voice->getSsmlGender();
                // printf('SSML voice gender: %s' . PHP_EOL, $ssmlVoiceGender[$gender]);

                // display the natural hertz rate for this voice
                // printf('Natural Sample Rate Hertz: %d' . PHP_EOL, $voice->getNaturalSampleRateHertz());

                $language_code = '';
                foreach ($voice->getLanguageCodes() as $code) {
                    $language_code = $code;
                }

                $ssmlVoiceGender = ['SSML_VOICE_GENDER_UNSPECIFIED', 'MALE', 'FEMALE', 'NEUTRAL'];
                $gender = $voice->getSsmlGender();
                // $item = [
                //     'name' => $voice->getName(),
                //     'language_code' => $language_code,
                //     'gender' => $ssmlVoiceGender[$gender],
                // ];
                $name = $voice->getName();
                $label = str_replace([$language_code . '-', strtolower($language_code) . '-'], '', $name);

                $data[$name] = [
                    'name' => $name,
                    'label' => $label,
                    'gender' => $gender,
                    'gender_label' => $ssmlVoiceGender[$gender],
                    'language_code' => $language_code,
                ];
            }

            // uasort($data, function ($a, $b) {
            //     if ($a == $b) {
            //         return 0;
            //     }
            //     return ($a < $b) ? -1 : 1;
            // });

            return $data;
        } catch (\Exception $ex) {
            return [];
        }
    }

    private static function client() {
        try {
            $credentials = json_decode(ThemeOptions::get_google_text_to_speech_key(), true);
            $client = new TextToSpeechClient([
                'credentials' => $credentials,
            ]);
            return $client;
        } catch (\Exception $ex) {
            return null;
        }
    }

    public static function text_to_speech($filename, $ssml) {
        try {
            $textToSpeechClient = self::client();

            $voice_type = self::get_voice_type();

            $input = new SynthesisInput();
            $input->setSsml($ssml);
            $voice = new VoiceSelectionParams();
            $voice->setName($voice_type['name']);
            $voice->setLanguageCode($voice_type['language_code']);
            $voice->setSsmlGender($voice_type['gender']);
            $audioConfig = new AudioConfig();
            $audioConfig->setAudioEncoding(AudioEncoding::MP3);

            $resp = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);
            return self::save_audio($filename, $resp->getAudioContent());
        } catch (\Exception $ex) {
            return new \WP_Error($ex->getCode(), $ex->getMessage());
        }
    }

    private static function save_audio($filename, $content) {
        $upload_basedir = self::upload_basedir();
        $filepath = sprintf('%s/%s.mp3', $upload_basedir, $filename);
        $file_url = sprintf('%s/%s.mp3?no-cache=%s', self::upload_baseurl(), $filename, strtolower(wp_generate_password(6, false)));
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        file_put_contents($filepath, $content);
        return $file_url;
    }

    private static function upload_baseurl() {
        return untrailingslashit(wp_upload_dir()['baseurl']) . '/sta-text-to-speech';
    }

    private static function upload_basedir() {
        $basedir = untrailingslashit(wp_upload_dir()['basedir']) . '/sta-text-to-speech';
        if (!file_exists($basedir)) {
            mkdir($basedir, 0755, true);
        }
        return $basedir;
    }
}
