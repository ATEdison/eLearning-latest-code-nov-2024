<?php

namespace STA\Inc;

class Helpers {

    public static function build_form_validation_rules($rules) {
        $rules_string = '';
        foreach ($rules as $key => $value) {
            if ($key == 'required') {
                $rules_string .= 'required ';
                continue;
            }

            $rules_string .= sprintf('%s="%s" ', $key, $value);
        }

        return $rules_string;
    }

    public static function get_video_embed_url($url) {
        if (!$url) {
            return null;
        }
        if (strpos($url, 'youtube.com')) {
            return self::get_youtube_embed_url($url);
        } elseif (strpos($url, 'vimeo.com')) {
            return self::get_vimeo_embed_url($url);
        }
        return $url;
    }

    public static function get_vimeo_embed_url($url) {
        $video_id = self::get_vimeo_video_id($url);

        // echo '<iframe src="http://player.vimeo.com/video/'.$id.'?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        return sprintf('https://player.vimeo.com/video/%s', $video_id);
    }

    private static function get_vimeo_video_id($url) {
        // <iframe src="https://player.vimeo.com/video/468795610?autoplay=1" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
        //<p><a href="https://vimeo.com/468795610">CB_JustMovedIn_45_WEBMASTER_ONLINE_TVC_201014</a> from <a href="https://vimeo.com/user84446178">Dig Agency</a> on <a href="https://vimeo.com">Vimeo</a>.</p>
        if (preg_match('@vimeo.com/(\d+)@', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function get_youtube_embed_url($url) {
        $video_id = self::get_youtube_video_id($url);
        if (!$video_id) {
            return null;
        }

        return sprintf('https://www.youtube.com/embed/%s', $video_id);
    }

    private static function get_youtube_video_id($url) {
        if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
            return $id[1];
        }
        if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
            return $id[1];
        }
        if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id)) {
            return $id[1];
        }
        if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
            return $id[1];
        }
        if (preg_match('/youtube\.com\/verify_age\?next_url=\/watch%3Fv%3D([^\&\?\/]+)/', $url, $id)) {
            return $id[1];
        }

        return null;
    }

    public static function log($data) {
        printf('<pre style="position: fixed;top: 50px;left: 0;z-index: 99999999;max-width:50vw;max-height: 80vh;background-color: #fff;color: #000; padding: 20px;overflow: auto;word-break: break-all;">%s</pre>', var_export($data, true));
    }

    public static function log_to_file($data, $append = true) {
        ob_start();
        var_dump($data);
        $data = ob_get_clean();
        file_put_contents(get_template_directory() . '/debug.log', $data . PHP_EOL . PHP_EOL, $append ? FILE_APPEND : 0);
    }
}
