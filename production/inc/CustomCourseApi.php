<?php

namespace STA\Inc;

use WP_Query;

class CustomCourseApi
{

    private static $instance;

    public static function instance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function test_fun()
    {

        return "Test Fun";
    }

    public static function getAssociatedList($postid)
    {
        // Get the Associated Courses
        $type = apply_filters('wpml_element_type', get_post_type($postid));
        $trid = apply_filters('wpml_element_trid', false, $postid, $type);

        $translations = apply_filters('wpml_get_element_translations', array(), $trid, $type);

        // Filter to get only published posts
        $published_courses = array_filter($translations, function($translation) {
            return isset($translation->post_status) && $translation->post_status === 'publish';
        });

        // Return only published courses
        $result = array_values($published_courses); // Optional: Re-index array

        // echo "<pre>";print_r($result);exit;

        return $result;
    }

    public static function replace_unicode_escape_sequence($match)
    {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
    }

    public static function unicode_decode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
    }

    public static function get_translate($value, $lang)
    {
       // Fetch translation for "Lesson"
       $translated_singular = apply_filters('wpml_translate_single_string', 'Lesson', 'sta', 'Lesson', $lang);
       $translated_plural = apply_filters('wpml_translate_single_string', 'Lessons', 'sta', 'Lessons', $lang);

       // Use _n() for pluralization
       return sprintf(_n($translated_singular, $translated_plural, $value, 'sta'), $value);
    }

    public static function get_courses($request)
    {
        // echo "<pre>"; print_r($request); exit;

        $languages = apply_filters('wpml_active_languages', NULL, array('skip_missing' => 0));


        if (!isset($request)) {
            $result = [];

            foreach ($languages as $k => $lang) {

                if ($lang['language_code'] == 'zh-hans') {
                    $lang['language_code'] = 'zh';
                }
                // $api = '/wp-json/custom/elearning/v1/courses/language/' . $lang;
                $api = '/wp-json/custom/elearning/v1/courses/language?language=' . $lang['language_code'];

                $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST].$api";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                $courses = curl_exec($ch);

                $result[$k] = json_decode($courses);
            }
            // return new WP_REST_Response($response, 200);

            return $result;
        } else {

            return self::get_course_by_language($request);
        }
    }


    public static function get_course_by_language($request)
    {      

        $result = [];
        $asso_courses = [];
        $asso_lessons = [];
        $asso_topics = [];
        $asso_quizzes = [];
        $related_courses = [];

        if($request['language'] != 'en'){
            $result['response'][] = "No results Found";
            return $result;
        }

        $course_list_arr = self::get_course_by_filter($request);

        if (!empty($course_list_arr['response'])) {

            $result['numberOfResults'] = $course_list_arr['numberOfResults'];

            // echo "<pre>"; print_r($course_list_arr); exit;

            $course_category_list = [];
            $course_destination_list = [];

            foreach ($course_list_arr['response'] as $j => $course_list_1) {

                $asso_courses = self::getAssociatedList($course_list_1['id']);

                $n = 0;
                foreach ($asso_courses as $lsn => $course_list) {

                 $course_tag_list = [];

                    if ($lsn == 'zh-hans') {
                        $lsn = 'zh';
                    }

                    if (isset($request['language'])) {
                        $lsn = $request['language'];
                    }

                    if ($course_list->language_code == 'zh-hans') {
                        $course_list->language_code = 'zh';
                    }

                    if ($course_list->language_code ==  $lsn) {

                        $result['results'][$j]['id'] = $course_list->element_id;
                        $result['results'][$j]['title'] = html_entity_decode(get_the_title($course_list->element_id));
                        $result['results'][$j]['excerpt'] = get_the_excerpt($course_list->element_id);
                        $result['results'][$j]['content'] = self::unicode_decode(html_entity_decode(\STA\Inc\CptCourse::get_description($course_list->element_id), ENT_COMPAT, 'UTF-8'));
                        $result['results'][$j]['duration'] = CptCourse::get_duration($course_list->element_id);
                        $result['results'][$j]['thumbnailImageSource'] = get_the_post_thumbnail_url($course_list->element_id, 'full');
                        $result['results'][$j]['url'] = get_permalink($course_list->element_id);

                        $content_type_post = get_post_type($course_list->element_id, 'post_type', true);
                        if($content_type_post == 'sfwd-courses'){
                            $content_type = __('Course', 'sta');
                        }

                        $translated_content_type = apply_filters( 'wpml_translate_single_string', $content_type, 'sta', $content_type, $lsn );

                        $result['results'][$j]['contentType'] = $translated_content_type;

                        $post = get_post($course_list->element_id);
                        $content = $post->post_content;
                        // $content = apply_filters('the_content', $content);

                        $content1 = self::unicode_decode(html_entity_decode(CptCourse::get_description($course_list->element_id), ENT_COMPAT, 'UTF-8'));

                        $dom = new \DOMDocument();
                        $dom->loadHTML($content1);
                        libxml_clear_errors(); // Clear the errors after loading HTML

                        // Get the <h1> element
                        $video_urls = $dom->getElementsByTagName('code');

                        if ($video_urls->length > 0) {
                            $urls = $video_urls->item(0);
                            // echo $h1->nodeValue; // Output: Hello, World!
                            $pattern = '/\[presto_player\s+src="([^"]+)"\]/';
                            if (preg_match($pattern, $urls->nodeValue, $matches)) {
                                $src_value = $matches[1];
                                $result['results'][$j]['video_url'] = $src_value;
                            }
                        }

                        // // Hero Image Source
                        if (has_blocks($content)) {
                            $blocks = parse_blocks($content);
                            if ($blocks[0]['blockName'] == 'carbon-fields/sta-hero-banner') {
                                foreach ($blocks[0]['attrs'] as $hero_bg) {
                                    $result['results'][$j]['heroImageSource'] = wp_get_attachment_url($hero_bg['bg']);
                                }
                            }
                        }

                        // Course Categories
                        // $tags = wp_get_post_terms($course_list->element_id, 'ld_course_category');
                        // if (!empty($tags) && !is_wp_error($tags)) {
                        //     foreach ($tags as $tag) {
                        //         $course_category_list[] = $tag->name;
                        //     }
                        // }

                        // // Course tags
                        $tags = wp_get_post_terms($course_list->element_id, 'ld_course_tag');
                        if (!empty($tags) && !is_wp_error($tags)) {
                            foreach ($tags as $tag) {
                                $course_tag_list[] = $tag->name;
                            }
                        }

                        // Course Destinations
                        // $tags = wp_get_post_terms($course_list->element_id, 'course_destinations');
                        // echo "<pre>";
                        // print_r($tags);
                        // exit;
                        // if (!empty($tags) && !is_wp_error($tags)) {
                        //     foreach ($tags as $tag) {
                        //         $course_destination_list[] = $tag->name;
                        //     }
                        // }

                        // $result['results'][$j]['categories'] = $course_category_list;
                        $result['results'][$j]['tags'] = $course_tag_list;
                        // $result['results'][$j]['destinations'] = $course_destination_list;

                    }

                    $lessons = learndash_get_course_lessons_list($course_list->element_id);

                    
                    // Get the lessons associated with the course
                    // // Extract lesson IDs
                    if (!empty($lessons)) {
                        $i = 0;
                        $count = 0;
                        foreach ($lessons as $l => $less) {
                            $asso_lessons = self::getAssociatedList($less['post']->ID);
                            // if($lsn == $l){
                            foreach ($asso_lessons as $lsn_l => $lesson_list) {
                                // Get the lessons associated with the course
                                if ($lesson_list->language_code == 'zh-hans') {
                                    $lesson_list->language_code = 'zh';
                                }

                                if ($lsn != 'zh') {
                                    if ($lesson_list->language_code == $lsn) {
                                        $result['results'][$j]['lessons'][$i]['id'] = $lesson_list->element_id;
                                        $result['results'][$j]['lessons'][$i]['title'] = html_entity_decode(get_the_title($lesson_list->element_id));
                                        $result['results'][$j]['lessons'][$i]['description'] = html_entity_decode(get_the_excerpt($lesson_list->element_id));
                                    }
                                } else {
                                    // echo "<pre>"; print_r($lesson_list);
                                    $result['results'][$j]['lessons'][$i]['id'] = $lesson_list->element_id;
                                    $result['results'][$j]['lessons'][$i]['title'] = html_entity_decode(get_the_title($lesson_list->element_id));
                                    $result['results'][$j]['lessons'][$i]['description'] = html_entity_decode(get_the_excerpt($lesson_list->element_id));
                                }

                                $topics = learndash_course_get_topics($course_list->element_id, $lesson_list->element_id, $query_args = array());

                                // // Extract lesson IDs
                                if (!empty($topics)) {
                                    $p = 0;
                                    foreach ($topics as $t => $tops) {
                                        $asso_topics = self::getAssociatedList($tops->ID);
                                        foreach ($asso_topics as $lsn_t => $topics_list) {

                                            if ($topics_list->language_code == 'zh-hans') {
                                                $topics_list->language_code = 'zh';
                                            }
                                            if ($topics_list->language_code ==  $lsn) {
                                                $result['results'][$j]['lessons'][$i]['topics'][$p]['id'] = $topics_list->element_id;
                                                $result['results'][$j]['lessons'][$i]['topics'][$p]['title'] = html_entity_decode(get_the_title($topics_list->element_id));
                                                $result['results'][$j]['lessons'][$i]['topics'][$p]['description'] = html_entity_decode(get_the_excerpt($topics_list->element_id));
                                            }
                                        }
                                        $p++;
                                    }

                                    // echo "<pre>"; print_r($asso_topics);
                                }
                            }
                            // }
                            $i++;
                            $count++;

                        }

                        $lesson_str = sprintf(_n('%s Lesson', '%s Lessons', $count, 'sta'), $count);

                        if($lsn != 'en'){
                            // Fetch translation for "Lesson"
                            $translated_singular = apply_filters('wpml_translate_single_string', 'Lesson', 'sta', 'Lesson', $lsn);
                            $translated_plural = apply_filters('wpml_translate_single_string', 'Lessons', 'sta', 'Lessons', $lsn);
                             // Use _n() for pluralization
                            $lesson_str =  sprintf(_n('%s ' .$translated_singular, '%s ' .$translated_plural, $count, 'sta'), $count);
                        }
                        
                        $result['results'][$j]['numberOfLessons'] = $lesson_str;
                    }

                   

                    //    array$result['Lessons']

                    // Quiz
                    $quizList = learndash_get_course_quiz_list($course_list->element_id, $user_id = null);

                    if (!empty($quizList)) {
                        foreach ($quizList as $q => $quiz) {
                            $asso_quizzes = self::getAssociatedList($quiz['post']->ID);
                            foreach ($asso_quizzes as $lsn_q => $quiz_list) {

                                if ($quiz_list->language_code == 'zh-hans') {
                                    $quiz_list->language_code = 'zh';
                                }

                                if ($quiz_list->language_code ==  $lsn) {
                                    $result['results'][$j]['quizzes'][$q]['id'] =  $quiz_list->element_id;
                                    $result['results'][$j]['quizzes'][$q]['title'] =  get_the_title($quiz_list->element_id);
                                }
                            }
                        }
                    }

                    // Related Courses
                    if ($course_list->language_code == $lsn) {
                        $r = 0;
                        $related_courses = self::getAssociatedList($course_list->element_id);
                        // echo "<pre>"; print_r($related_courses);
                        foreach ($related_courses as $rel => $related_list) {
                            $result['results'][$j]['relatedCourses'][$r]['id'] = $related_list->element_id;
                            $result['results'][$j]['relatedCourses'][$r]['language'] = $related_list->language_code;
                            $result['results'][$j]['relatedCourses'][$r]['title'] = get_the_title($related_list->element_id);
                            $r++;
                        }
                    }
                    $n++;
                }
            }


            $result['hasMore'] = $course_list_arr['hasMore'];
        }

        // echo "<pre>"; print_r($course_list_arr);exit;

        return $result;
    }

    public static function get_course_by_id($request)
    {

        $result = [];
        $asso_courses = [];
        $asso_lessons = [];
        $asso_topics = [];
        $asso_quizzes = [];

        if (isset($request['id'])) {
            $course_id = $request['id'];
        }

        $lsn = apply_filters('wpml_element_language_code', null, array('element_id' => $course_id, 'element_type' => 'post'));

        if ($lsn == 'zh-hans') {
            $lsn = 'zh';
        }

        // $lsn = $course_list->language_code;
        $result[$lsn]['id'] = $course_id;
        $result[$lsn]['title'] = html_entity_decode(get_the_title($course_id));
        $result[$lsn]['excerpt'] = get_the_excerpt($course_id);
        $result[$lsn]['content'] = self::unicode_decode(html_entity_decode(\STA\Inc\CptCourse::get_description($course_id), ENT_COMPAT, 'UTF-8'));
        $result[$lsn]['duration'] = CptCourse::get_duration($course_id);
        $result[$lsn]['thumbnailImageSource'] = get_the_post_thumbnail_url($course_id, 'full');

        $post = get_post($course_id);
        $content = $post->post_content;
        // $content = apply_filters('the_content', $content);

        $content1 = self::unicode_decode(html_entity_decode(CptCourse::get_description($course_id), ENT_COMPAT, 'UTF-8'));

        $dom = new \DOMDocument();
        $dom->loadHTML($content1);
        libxml_clear_errors(); // Clear the errors after loading HTML

        // Get the <h1> element
        $video_urls = $dom->getElementsByTagName('code');

        if ($video_urls->length > 0) {
            $urls = $video_urls->item(0);
            // echo $h1->nodeValue; // Output: Hello, World!
            $pattern = '/\[presto_player\s+src="([^"]+)"\]/';
            if (preg_match($pattern, $urls->nodeValue, $matches)) {
                $src_value = $matches[1];
                $result[$lsn]['video_url'] = $src_value;
            }
        }

        // // Hero Image Source
        if (has_blocks($content)) {
            $blocks = parse_blocks($content);
            if ($blocks[0]['blockName'] == 'carbon-fields/sta-hero-banner') {
                foreach ($blocks[0]['attrs'] as $hero_bg) {
                    $result[$lsn]['heroImageSource'] = wp_get_attachment_url($hero_bg['bg']);
                }
            }
        }

        $asso_courses = self::getAssociatedList($course_id);
        $j = 0;
        foreach ($asso_courses as $lan => $co_list) {
            $lessons[$lan] = learndash_get_course_lessons_list($co_list->element_id);

            // Get the lessons associated with the course
            // echo "<pre>";
            // print_r($lessons);
            // exit;
            // // Extract lesson IDs
            if (!empty($lessons)) {
                $i = 0;
                foreach ($lessons as $lan => $less_1) {
                    foreach ($less_1 as $l => $less) {
                        $asso_lessons = self::getAssociatedList($less['post']->ID);

                        foreach ($asso_lessons as $lsn_1 => $lesson_list) {
                            // Get the lessons associated with the course
                            $topics = learndash_course_get_topics($co_list->element_id, $lesson_list->element_id, $query_args = array());
                            // echo "<pre>"; print_r($lesson_list);
                            if ($lesson_list->language_code == 'zh-hans') {
                                $lesson_list->language_code = 'zh';
                            }

                            if ($lsn != 'zh') {
                                // echo "<pre>"; print_r($lesson_list);
                                if ($lesson_list->language_code == $lsn) {
                                    $result[$lsn][$j]['lessons'][$i]['id'] = $lesson_list->element_id;
                                    $result[$lsn][$j]['lessons'][$i]['title'] = html_entity_decode(get_the_title($lesson_list->element_id));
                                    $result[$lsn][$j]['lessons'][$i]['description'] = html_entity_decode(get_the_excerpt($lesson_list->element_id));
                                }
                            } else {
                                // echo "<pre>"; print_r($lesson_list);
                                $result[$lsn][$j]['lessons'][$i]['id'] = $lesson_list->element_id;
                                $result[$lsn][$j]['lessons'][$i]['title'] = html_entity_decode(get_the_title($lesson_list->element_id));
                                $result[$lsn][$j]['lessons'][$i]['description'] = html_entity_decode(get_the_excerpt($lesson_list->element_id));
                            }

                            // // Extract lesson IDs
                            if (!empty($topics)) {
                                $p = 0;
                                foreach ($topics as $t => $tops) {
                                    $asso_topics = self::getAssociatedList($tops->ID);
                                    foreach ($asso_topics as $lsn_2 => $topics_list) {
                                        if ($topics_list->language_code == 'zh-hans') {
                                            $topics_list->language_code = 'zh';
                                        }
                                        if ($topics_list->language_code == $lsn) {
                                            $result[$lsn]['lessons'][$i]['topics'][$p]['id'] = $topics_list->element_id;
                                            $result[$lsn]['lessons'][$i]['topics'][$p]['title'] = html_entity_decode(get_the_title($topics_list->element_id));
                                            $result[$lsn]['lessons'][$i]['topics'][$p]['description'] = html_entity_decode(get_the_excerpt($topics_list->element_id));
                                        }
                                    }
                                    $p++;
                                }
                                // echo "<pre>"; print_r($asso_topics);
                            }
                        }

                        $i++;
                    }
                }
            }

            //    array$result[$lsn]['Lessons']

            // Quiz
            $quizList = learndash_get_course_quiz_list($co_list->element_id, $user_id = null);

            if (!empty($quizList)) {
                foreach ($quizList as $q => $quiz) {
                    $asso_quizzes = self::getAssociatedList($quiz['post']->ID);
                    foreach ($asso_quizzes as $lsn_3 => $quiz_list) {
                        if ($quiz_list->language_code == 'zh-hans') {
                            $quiz_list->language_code = 'zh';
                        }
                        if ($quiz_list->language_code == $lsn) {
                            $result[$lsn]['quizzes'][$q]['id'] =  $quiz_list->element_id;
                            $result[$lsn]['quizzes'][$q]['title'] =  get_the_title($quiz_list->element_id);
                        }
                    }
                    //    echo "<pre>"; print_r($asso_quizzes);
                }
            }


            // Related Courses
            if ($co_list->language_code == 'zh-hans') {
                $co_list->language_code = 'zh';
            }

            $result[$lsn]['relatedCourses'][$j]['id'] = $co_list->element_id;
            $result[$lsn]['relatedCourses'][$j]['language'] = $co_list->language_code;
            $result[$lsn]['relatedCourses'][$j]['title'] = get_the_title($co_list->element_id);

            $j++;
        }




        // echo "<pre>"; print_r($result);

        return $result;
    }

    public static function get_courses_related($request)
    {

        $result = [];
        $asso_courses = [];
        $asso_lessons = [];
        $asso_topics = [];
        $asso_quizzes = [];

        $asso_courses = self::getAssociatedList($request['id']);

        foreach ($asso_courses as $lsn => $course_list) {

            $type = apply_filters('wpml_element_type', get_post_type($course_list->element_id));
            $trid = apply_filters('wpml_element_trid', false, $course_list->element_id, $type);

            $result[$lsn]['id'] = $course_list->element_id;
            $result[$lsn]['trid'] = $trid;
            $result[$lsn]['title'] = html_entity_decode(get_the_title($course_list->element_id));
            $result[$lsn]['excerpt'] = get_the_excerpt($course_list->element_id);
            $result[$lsn]['content'] = self::unicode_decode(html_entity_decode(\STA\Inc\CptCourse::get_description($course_list->element_id), ENT_COMPAT, 'UTF-8'));
            $result[$lsn]['duration'] = CptCourse::get_duration($course_list->element_id);
            $result[$lsn]['thumbnailImageSource'] = get_the_post_thumbnail_url($course_list->element_id, 'full');

            $post = get_post($course_list->element_id);
            $content = $post->post_content;
            // $content = apply_filters('the_content', $content);

            $content1 = self::unicode_decode(html_entity_decode(CptCourse::get_description($course_list->element_id), ENT_COMPAT, 'UTF-8'));

            $dom = new \DOMDocument();
            $dom->loadHTML($content1);
            libxml_clear_errors(); // Clear the errors after loading HTML

            // Get the <h1> element
            $video_urls = $dom->getElementsByTagName('code');

            if ($video_urls->length > 0) {
                $urls = $video_urls->item(0);
                // echo $h1->nodeValue; // Output: Hello, World!
                $pattern = '/\[presto_player\s+src="([^"]+)"\]/';
                if (preg_match($pattern, $urls->nodeValue, $matches)) {
                    $src_value = $matches[1];
                    $result[$lsn]['video_url'] = $src_value;
                }
            }

            // // Hero Image Source
            if (has_blocks($content)) {
                $blocks = parse_blocks($content);
                if ($blocks[0]['blockName'] == 'carbon-fields/sta-hero-banner') {
                    foreach ($blocks[0]['attrs'] as $hero_bg) {
                        $result[$lsn]['heroImageSource'] = wp_get_attachment_url($hero_bg['bg']);
                    }
                }
            }


            // Get the lessons associated with the course
            $lessons = learndash_get_course_lessons_list($course_list->element_id);
            //    echo "<pre>"; print_r($lessons);
            // // Extract lesson IDs
            if (!empty($lessons)) {
                $i = 0;
                foreach ($lessons as $l => $less) {
                    $asso_lessons = self::getAssociatedList($less['post']->ID);

                    foreach ($asso_lessons as $lsn => $lesson_list) {
                        // Get the lessons associated with the course
                        $topics = learndash_course_get_topics($course_list->element_id, $lesson_list->element_id, $query_args = array());
                        // echo "<pre>"; print_r($lesson_list);
                        $result[$lsn]['lessons'][$i]['id'] = $lesson_list->element_id;
                        $result[$lsn]['lessons'][$i]['title'] = html_entity_decode(get_the_title($lesson_list->element_id));
                        $result[$lsn]['lessons'][$i]['description'] = html_entity_decode(get_the_excerpt($lesson_list->element_id));

                        // // Extract lesson IDs
                        if (!empty($topics)) {
                            $p = 0;
                            foreach ($topics as $t => $tops) {
                                $asso_topics = self::getAssociatedList($tops->ID);
                                foreach ($asso_topics as $lsn => $topics_list) {
                                    $result[$lsn]['lessons'][$i]['topics'][$p]['id'] = $topics_list->element_id;
                                    $result[$lsn]['lessons'][$i]['topics'][$p]['title'] = html_entity_decode(get_the_title($topics_list->element_id));
                                    $result[$lsn]['lessons'][$i]['topics'][$p]['description'] = html_entity_decode(get_the_excerpt($topics_list->element_id));
                                }
                                $p++;
                            }

                            // echo "<pre>"; print_r($asso_topics);

                        }
                    }
                    $i++;
                }
            }

            //    array$result[$lsn]['Lessons']

            // Quiz
            $quizList = learndash_get_course_quiz_list($course_list->element_id, $user_id = null);

            if (!empty($quizList)) {
                foreach ($quizList as $q => $quiz) {
                    $asso_quizzes = self::getAssociatedList($quiz['post']->ID);
                    foreach ($asso_quizzes as $lsn => $quiz_list) {
                        $result[$lsn]['quizzes'][$q]['id'] =  $quiz_list->element_id;
                        $result[$lsn]['quizzes'][$q]['title'] =  get_the_title($quiz_list->element_id);
                    }
                    //    echo "<pre>"; print_r($asso_quizzes);
                }
            }
        }

        return $result;
    }

    public static function get_course_by_filter($request)
    {
        // Prepare the response
        $result = [];
        $total_pages = [];

        // Get pagination parameters from the request
        $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
        $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : '';
        $search = $request->get_param('search') ? sanitize_text_field($request->get_param('search')) : '';
        $maxDuration  = $request->get_param('maxDuration') ? intval($request->get_param('maxDuration')) : '';
        $content_type  = $request->get_param('content-type') ? sanitize_text_field($request->get_param('content-type')) : '';
        
        $filters  = $request->get_param('filters') ? sanitize_text_field($request->get_param('filters')) : '';
        
        $filter_category_terms = [];
        $filter_tags_terms = [];

        $filters_explode = explode(',', $filters);

        // foreach($filters_explode as $tag => $filter){
        //     // $filter_tags_terms[] = $filter;
        //     $filter_tags_terms_list = get_term_by('name', $filter, 'ld_course_tag');
        //     if(!empty($filter_tags_terms_list)){
        //         $filter_tags_terms[] = $filter_tags_terms_list->term_id;
        //     }
        // }

        foreach ($filters_explode as $filter) {

            $get_filter = explode('/', $filter);
        
            if (in_array('saudipartner:category', $get_filter)) {
                $filter_tags_terms_list = get_term_by('name', $filter, 'ld_course_tag');
                // if ($filter_tags_terms_list) {
                    $filter_category_terms[] = $filter_tags_terms_list->term_id;
                // }
            }
        
            if (in_array('saudipartner:destination', $get_filter)) {
                $filter_tags_terms_list = get_term_by('name', $filter, 'ld_course_tag');
                // if ($filter_tags_terms_list) {
                    $filter_tags_terms[] = $filter_tags_terms_list->term_id;
                // }
            }
        }

        // if($filters != ''){
        //     // Check if filter_tags_terms is empty
        //     if (empty($filter_tags_terms)) {
        //         return;
        //     }
        // }

        // Define the post type based on content type
        $type = ($content_type == 'course' || $content_type == 'courses' || $content_type == '') ? 'sfwd-courses' : $content_type;

        // Define the query arguments
        $args = [
            'post_type' => $type,
            'posts_per_page' => $per_page,
            'paged' => $page,
            's' => $search,
            'order' => 'ASC',
        ];

        if ($maxDuration) {
            $args['meta_query'] = [
                'relation' => 'AND',
                [
                    'key' => '_duration',
                    'value' => $maxDuration . ' minutes',
                    'compare' => '<=',
                ],
            ];
        }

        // Initialize tax_query
        if (!empty($filter_category_terms) && !empty($filter_tags_terms)) {

            $tax_query = ['relation' => 'AND'];

            if (!empty($filter_category_terms)) {
                $tax_query[] = [
                    'taxonomy' => 'ld_course_tag',
                    'field' => 'id',
                    'terms' => $filter_category_terms, // Categories
                    // 'operator' => 'AND', // Categories
                ];
            }

            if (!empty($filter_tags_terms)) {
                $tax_query[] = [
                    'taxonomy' => 'ld_course_tag',
                    'field' => 'id',
                    'terms' => $filter_tags_terms, // Categories
                    // 'operator' => 'AND', // Categories
                ];
            }
        }else{

            if (!empty($filter_category_terms)) {
                $tax_query[] = [
                    'taxonomy' => 'ld_course_tag',
                    'field' => 'id',
                    'terms' => $filter_category_terms, // Categories
                    // 'operator' => 'AND', // Categories
                ];
            }

            if (!empty($filter_tags_terms)) {
                $tax_query[] = [
                    'taxonomy' => 'ld_course_tag',
                    'field' => 'id',
                    'terms' => $filter_tags_terms, // Categories
                    // 'operator' => 'AND', // Categories
                ];
            }
        }

        // Add tax_query to args if not empty
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        // Execute the query
        $query = new WP_Query($args);

        // echo "<pre>";print_r($query);exit;

        // Prepare result based on query
        if ($query->have_posts()) {
            $result['numberOfResults'] = $query->found_posts;
            $total_pages = $query->max_num_pages;

            // Determine if there are more pages
            $result['hasMore'] = ($total_pages > $page) ? 1 : 0;

            // Collect posts
            while ($query->have_posts()) {
                $query->the_post();
                $result['response'][] = ['id' => get_the_ID()];
            }
        }

        // Reset post data
        wp_reset_postdata();

        return $result;
    }


    public static function get_learningpaths_course_data_fetch($request)
    {

        $course_steps = [];

        // Define the arguments for the query
        $args = array(
            'post_type' => 'learning-paths', // Replace 'your_post_type' with the actual post type
            'p' => 6094,          // Number of posts to retrieve
            'post_status' => 'publish',      // Only retrieve published posts
        );

        // Create a new WP_Query instance
        $query = new WP_Query($args);

        $course_id = [];
        $lessons_list = [];
        $topic_list = [];
        $qz_list = [];

        // Check if there are any posts
        if ($query->have_posts()) {
            // Loop through the posts
            while ($query->have_posts()) {

                $query->the_post();

                // Get the post ID
                $post_id = get_the_ID();

                // Get the post title
                $course_steps[$post_id]['title'] = $post_title = get_the_title();

                // Get the post content
                $post_content = html_entity_decode(get_the_content());
                $course_steps[$post_id]['content'] = "";

                if (has_blocks($post_content)) {
                    $blocks = parse_blocks($post_content);
                    // only render banner for logged-in users
                    if ($blocks[0]['blockName'] == 'carbon-fields/sta-course-grid') {
                        $course_id = $blocks[0]['attrs']['data']['items'];
                        // echo "<pre>"; print_r($blocks[0]['attrs']['data']['items']);
                        // echo render_block($blocks[0]);
                    }
                } else {

                    $course_steps[$post_id]['content'] = html_entity_decode(get_the_content());
                }

                $slug['slug'] = 'badge_course_' . $post_id;
                $course_steps[$post_id]['badges'] = BadgeSystem::get_badge_details($slug);

                foreach ($course_id as $c => $ids) {

                    $lessons_list[$ids['id']] = learndash_get_course_steps($ids['id'],  $include_post_types = array('sfwd-lessons'));
                }

                // Get Topics List
                foreach ($lessons_list as $course_id => $lesson_list) {

                    foreach ($lesson_list as $le => $less_id) {

                        $topic_list[$course_id][$less_id] = learndash_course_get_topics($course_id, $less_id,  $query_args = array());
                    }
                }

                // Final Structure
                $stepsCount = 1;

                foreach ($topic_list as $c_id => $lesson_list_arr) {

                    foreach ($lesson_list_arr as $l_id => $topic_list_arr) {

                        foreach ($topic_list_arr as $t_id => $topics) {

                            $course_steps[$post_id]['steps']['sfwd-courses'][$c_id]['sfwd-lessons'][$l_id]['sfwd-topic'][] = $t_id;
                        }
                    }

                    // Quiz List
                    $qz_list = learndash_get_course_quiz_list($course_id);

                    foreach ($qz_list as $qz => $quiz) {

                        $course_steps[$post_id]['steps']['sfwd-courses'][$c_id]['sfwd-quizz'][$qz] = $quiz['id'];
                    }

                    $course_steps[$post_id]['_learningpath_steps_count'] += $stepsCount;

                    $duration = CptCourse::get_duration($c_id);
                    $course_steps[$post_id]['steps']['sfwd-courses'][$c_id]['duration'] = $duration;

                    preg_match('/\d+/', $duration, $matches);

                    $course_steps[$post_id]['duration'] += $matches[0];
                }
            }
            // Reset post data
            wp_reset_postdata();
        }

        // echo "<pre>";
        // print_r($course_steps);
        // return $course_steps;


    }

    public static function get_learningpaths_course_data_fetch_1($request)
    {

        $course_id = [];
        $lessons_list = [];
        $topic_list = [];
        $qz_list = [];

        $post_id = 6094; // Replace with your actual post ID
        $post = get_post($post_id);

        $post_content = $post->post_content;

        if (has_blocks($post_content)) {
            $blocks = parse_blocks($post_content);
            // only render banner for logged-in users
            foreach ($blocks as $b => $block) {
                if ($block['blockName'] == 'carbon-fields/sta-course-grid') {
                    $course_id = $block['attrs']['data']['items'];
                    // echo "<pre>"; print_r($course_id);
                }
            }
        }

        $slug['slug'] = 'badge_course_' . $post_id;
        $course_steps['badges'] = BadgeSystem::get_badge_details($slug);

        foreach ($course_id as $c => $ids) {

            $lessons_list[$ids['id']] = learndash_get_course_steps($ids['id'],  $include_post_types = array('sfwd-lessons'));
        }

        // Get Topics List
        foreach ($lessons_list as $course_id => $lesson_list) {

            foreach ($lesson_list as $le => $less_id) {

                $topic_list[$course_id][$less_id] = learndash_course_get_topics($course_id, $less_id,  $query_args = array());
            }
        }

        // Final Structure
        $stepsCount = 1;

        foreach ($topic_list as $c_id => $lesson_list_arr) {

            foreach ($lesson_list_arr as $l_id => $topic_list_arr) {

                foreach ($topic_list_arr as $t_id => $topics) {

                    $course_steps['steps']['sfwd-courses'][$c_id]['sfwd-lessons'][$l_id]['sfwd-topic'][] = $t_id;
                }
            }

            // Quiz List
            $qz_list = learndash_get_course_quiz_list($course_id);

            foreach ($qz_list as $qz => $quiz) {

                $course_steps['steps']['sfwd-courses'][$c_id]['sfwd-quizz'][$qz] = $quiz['id'];
            }

            $course_steps['_learningpath_steps_count'] += $stepsCount;

            $duration = CptCourse::get_duration($c_id);
            $course_steps['steps']['sfwd-courses'][$c_id]['duration'] = $duration;

            preg_match('/\d+/', $duration, $matches);

            $course_steps['duration'] += $matches[0];
        }

        // echo "<pre>";
        // print_r($course_steps);
        return $course_steps;
    }

    public static function get_learningpaths_course_data($request)
    {
        // $result = self::get_learningpaths_course_data_fetch($request);
        $result = self::get_learningpaths_course_data_fetch_1($request);

        $post_id = 6094; // Replace with your actual post ID
        update_post_meta($post_id, '_learningpaths_badges_'.$post_id, serialize($result['badges']));
        update_post_meta($post_id, '_learningpaths_steps_'.$post_id, serialize($result['steps']));



        echo "<pre>";
        print_r($result);

        // update_post_meta($post_id, '_custom_meta_key_'.$post_id, 'custom_meta_value');
    }


    // CustomCourseApi End 
}
// CustomCourseApi End 

add_action('rest_api_init', function () {

    register_rest_route('custom/elearning/v1', '/courses', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApi, 'get_courses'),
    ));

    register_rest_route('custom/elearning/v1', '/courses/id/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApi, 'get_course_by_id'),
    ));

    register_rest_route('custom/elearning/v1', '/courses/language', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApi, 'get_course_by_language'),
    ));

    register_rest_route('custom/elearning/v1', '/courses/related/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApi, 'get_courses_related'),
    ));

    register_rest_route('custom/elearning/v1', '/sample/test', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApi, 'test_fun'),
    ));

    register_rest_route('custom/elearning   ', '/courses/v1/', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApi, 'get_course_by_filter'),
    ));

    register_rest_route('custom/elearning/v1', '/learningpaths/', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApi, 'get_learningpaths_course_data'),
    ));
});
