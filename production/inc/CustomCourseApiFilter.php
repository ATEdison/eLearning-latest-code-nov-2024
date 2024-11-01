<?php

namespace STA\Inc;

use WP_Query;

class CustomCourseApiFilter
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

        $result = apply_filters('wpml_get_element_translations', array(), $trid, $type);

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

    public static function get_courses($request)
    {

        $languages = apply_filters('wpml_active_languages', NULL, array('skip_missing' => 0));

        if (!isset($request)) {

            $result = [];

            foreach ($languages as $k => $lang) {

                if ($lang['language_code'] == 'zh-hans') {
                    $lang['language_code'] = 'zh';
                }
                // $api = '/wp-json/custom/elearning/v1/courses/language/' . $lang;
                $api = '/wp-json/custom/elearning/v2/courses/language?language=' . $lang['language_code'];

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

                        // // Course tags
                        $tags = wp_get_post_terms($course_list->element_id, 'ld_course_tag');
                        if (!empty($tags) && !is_wp_error($tags)) {
                            foreach ($tags as $tag) {
                                $course_tag_list[] = $tag->name;
                            }
                        }

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


    public static function get_used_tags($request)
    {
        // Prepare the response
        $result = array();

        $filters  = $request->get_param('filters') ? sanitize_text_field($request->get_param('filters')) : '';

        $filter_category_terms = [];
        $filter_tags_terms = [];

        $filters_explode = explode(',', $filters);
        
        foreach($filters_explode as $tag => $filter){
                // $filter_tags_terms[] = $filter;
                $filter_tags_terms_list = get_term_by('name', $filter, 'ld_course_tag');
                $filter_tags_terms[] = $filter_tags_terms_list->term_id;

        }
        // echo "<pre>"; print_r($filter_tags_terms);exit;

        // Define the query arguments
        $args = array(
            'post_type' => 'sfwd-courses', // LearnDash course post type 30 minutes
            'tax_query' => array(
                array(
                    'taxonomy' => 'ld_course_tag',
                    'field'    => 'id',
                    'terms'    => $filter_tags_terms, // Multiple terms
                    'operator' => 'IN', // Optional, defaults to 'IN'
                ),
            ),
        );

        // Execute the query
        $query = new WP_Query($args);

        // echo "<pre>";
        // print_r($query);
        // exit;

        if ($query->found_posts) {

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();

                    $terms = wp_get_post_terms(get_the_ID(), 'ld_course_tag');

                    if (!is_wp_error($terms) && !empty($terms)) {

                        foreach ($terms as $term) {
                            if (in_array($term->term_id, $filter_tags_terms)) {
                                $result[] = $term->name;
                            }
                        }

                    }
                }
            }
        }

        $response = [];
        $response['filteredtags'] = array_values(array_unique($result));

        // Reset post data
        wp_reset_postdata();

        // echo "<pre>"; print_r($response);exit;

        return $response;
    }

    public static function get_course_by_filter($request)
    {
        // Prepare the response
        $result = array();

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
        
        foreach($filters_explode as $tag => $filter){
                // $filter_tags_terms[] = $filter;
                $filter_tags_terms_list = get_term_by('name', $filter, 'ld_course_tag');
                $filter_tags_terms[] = $filter_tags_terms_list->term_id;

        }

        // echo "<pre>"; print_r($filter_tags_terms);exit;

        $type = $content_type;
        
        if($content_type == 'course' || $content_type == 'courses' || $content_type == ''){
            $type = 'sfwd-courses';
        }
        
        // echo "<pre>"; print_r($type);exit;

        // Define the query arguments
        $args = array(
            'post_type' => $type, // LearnDash course post type 30 minutes
            'posts_per_page' => $per_page,
            'paged' => $page,
            's' => $search,
            'order' => 'ASC'
        );

        if($maxDuration){
            $meta_query = array('relation' => 'AND');
            $meta_query[] = array(
                array(
                    'key' => '_duration',
                    'value' => $maxDuration .' minutes',
                    'compare' => '<=',
                ),
            );
        }

        if($filters && !empty($filter_tags_terms)){
            $tax_query = array('relation' => 'AND');
            $tax_query[] = array(
                array(
                    'taxonomy' => 'ld_course_tag', // Custom taxonomy
                    'field'    => 'id', // Use 'id' if you're filtering by term ID
                    'terms'    => $filter_tags_terms, // Term slug or array of slugs
                )
            );
        }

        // Add tax_query to the arguments array
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        // Execute the query
        $query = new WP_Query($args);

        // echo "<pre>";
        // print_r($args);
        // exit;

        if ($query->found_posts) {

            $total_pages = $query->max_num_pages;

            $result['numberOfResults'] =  $query->found_posts;

            if ($total_pages == $page || $total_pages <= $page) {
                $result['hasMore'] =  0; // False
            } else {
                $result['hasMore'] =  1; // True
            }

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $result['response'][] = array(
                        'id' => get_the_ID(),
                    );
                }
            }
        }

        // Reset post data
        wp_reset_postdata();

        // echo "<pre>"; print_r($result);exit;

        return $result;
    }


    

//     // CustomCourseApi End 
}
// CustomCourseApi End 

add_action('rest_api_init', function () {

    register_rest_route('custom/elearning/v1', '/test', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApiFilter, 'test_fun'),
    ));

    register_rest_route('custom/elearning/v1', '/courses', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApiFilter, 'get_courses'),
    ));

    register_rest_route('custom/elearning/v1', '/usedtags', array(
        'methods' => 'GET',
        'callback' => array(new CustomCourseApiFilter, 'get_used_tags'),
    ));


});
