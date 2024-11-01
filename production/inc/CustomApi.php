<?php

namespace STA\Inc;

use WP_Query;

class CustomApi {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getAuthentication( $request ){

        $auth_src = "$#eLearnstoch@sta#$";
        $auth = sha1(md5($auth_src));

        // Check if Authorization header exists
        $authorization_header = $request->get_header('Authorization');

        if (empty($authorization_header)) {
            return 'unauthorized , Authorization header is missing.';
        }

        // Parse the Authorization header, e.g., "Bearer <token>"
        $token = str_replace('Bearer ', '', $authorization_header);

        if($auth != $token){
            return 'unauthorized Invalid or expired token';
        }

        return true;

    }

    // Callback function to handle the API request
    public function get_courses_list( $request ) {

        // $auth = self::getAuthentication( $request );

        // if($auth != 1){
        //     return $auth;
        // }

        $language = $request['language'];

        if($language){
            $lang_code = $language;
        }else{
            $lang_code = 'en';
        }

        // Execute your PHP function or code here
        $result = self::get_courses($lang_code);

       /// Prepare and return the response
       $response = array(
           'success' => true,
           'data' => $result,
       );
       
        
       return $response;
    }

    public static function get_courses($code){

        $result = [];

        $api = '/wp-json/ldlms/v1/sfwd-courses';

       $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST].$api";

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $courses = curl_exec($ch);

        $course_list = json_decode($courses);

        $contentType = 'Course';
        if($code == 'en'){
            $contentType = 'Course';
        }elseif($code == 'ar'){
            $contentType = 'دورة';
        }elseif($code == 'zh'){
            $contentType = '课程';
        }

        foreach($course_list as $k => $course_id){

            // Get the Associated Courses
            $type = apply_filters( 'wpml_element_type', get_post_type($course_id->id) );
            $trid = apply_filters( 'wpml_element_trid', false, $course_id->id, $type );
            
            $asso_courses = apply_filters( 'wpml_get_element_translations', array(), $trid, $type );

            foreach($asso_courses as $j => $course){
                if($code == $j){
                    $result[$k]['id'] = $course->element_id;
                    $result[$k]['title'] = html_entity_decode(get_the_title($course->element_id));
                    $result[$k]['excerpt'] = get_the_excerpt($course->element_id);
                    $result[$k]['link'] = get_the_permalink($course->element_id);
                    $result[$k]['img'] = get_the_post_thumbnail_url($course->element_id, 'full');
                    $result[$k]['duration'] = \STA\Inc\CptCourse::get_duration($course->element_id);
                    $result[$k]['lessons'] = \STA\Inc\CptCourse::get_course_lesson_count($course->element_id);
                    $result[$k]['topics'] = \STA\Inc\CptCourse::get_course_topic_count($course->element_id);
                    $result[$k]['quizzes'] = \STA\Inc\CptCourse::get_course_quiz_count($course->element_id);
                    $result[$k]['contentType'] = $contentType;
                    // $result[$k]['contentType2'] = \STA\Inc\Translator::textTranslation($contentType);
                }
            }
        }

        // echo '<pre>'; print_r($result);

        return $result;
    }

    // Callback function to handle the API request
    public function get_user_badges( $request ) {

        $auth = self::getAuthentication( $request );

        if($auth != 1){
            return $auth;
        }

        $email = $request->get_param('email');

        // Check if the email parameter is valid
        if (empty($email) || !is_email($email)) {
            return new WP_Error('invalid_email', __('Invalid email parameter.'), array('status' => 400));
        }

        $badge_list = [];
        $result = [];

        $user_data = get_user_by('email', $email);
        $badge_list = BadgeSystem::get_user_earned_badges($user_data->ID);

        // $user_data = get_userdata($request['id']);
        // $result['email'] = $user_data->user_email;

        foreach($badge_list as $b => $slug){
            $r['slug'] = $slug;
            $badge = BadgeSystem::get_badge_details($r);
            if($badge['image_id'] && $badge['desc']){
                $result[$b]['badge'] = htmlentities($badge['desc']);
                $result[$b]['image'] = wp_get_attachment_image_url($badge['image_id']);
            }
        }

        // echo '<pre>'; print_r($user_data);

        // Prepare and return the response
        $response = array(
            'success' => true,
            'data' => $result,
        );

        // return new WP_REST_Response( $response, 200 );
        
        return $response;
    }


    public static function replace_unicode_escape_sequence($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
    }
    
    public static function unicode_decode($str) {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
    }

    public static function get_course_by_id( $request ){

        $result = [];
        
        $api = '/wp-json/ldlms/v1/sfwd-courses';

        $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST].$api";
 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $courses = curl_exec($ch);

        $course_list = json_decode($courses);

        foreach($course_list as $k => $course_list){
            if($request->get_param('id') == $course_list->id){
             // Get the Associated Courses
             $type = apply_filters( 'wpml_element_type', get_post_type($course_list->id) );
             $trid = apply_filters( 'wpml_element_trid', false, $course_list->id, $type );
             
             $asso_courses = apply_filters( 'wpml_get_element_translations', array(), $trid, $type );

            foreach($asso_courses as $j => $course){
            // if($code == $j){
                    $result[$k]['id'] = $course->element_id ;
                    $result[$k]['title'] = html_entity_decode(get_the_title($course->element_id));
                    $result[$k]['excerpt'] = get_the_excerpt($course->element_id);
                    $result[$k]['content'] = self::unicode_decode(html_entity_decode(\STA\Inc\CptCourse::get_description($course->element_id), ENT_COMPAT, 'UTF-8'));
                    $result[$k]['duration'] = \STA\Inc\CptCourse::get_duration($course->element_id);
                    $result[$k]['thumbnailImageSource'] = get_the_post_thumbnail_url($course->element_id, 'full');

                    $post = get_post($course->element_id);
                    $content = $post->post_content;
                    // $content = apply_filters('the_content', $content);

                    $content1 = self::unicode_decode(html_entity_decode(\STA\Inc\CptCourse::get_description($course->element_id), ENT_COMPAT, 'UTF-8'));

                    if ( strpos( $content1, 'nn_youtube' ) !== false ) {
                        // Text found, do something
                        $token = 'nn_youtube';
                        $index = strpos($content1, $token);
                        $video_tag = substr($content1, $index + strlen($token));

                        $pattern = '/\b(?:https?:\/\/|www\.)\S+\b/';
                        preg_match_all($pattern, $video_tag, $matches);

                        // Output the matched URLs
                        foreach ($matches[0] as $url) {
                            $result[$k]['video_url'] = $url;
                        }

                    
                    }

                    // // Hero Image Source
                    if (has_blocks($content)) {
                        $blocks = parse_blocks($content);
                        if ($blocks[0]['blockName'] == 'carbon-fields/sta-hero-banner') {
                            foreach($blocks[0]['attrs'] as $hero_bg){
                                $result[$k]['heroImageSource'] = wp_get_attachment_url($hero_bg['bg']);
                            }
                        }
                    }

                    // Lessons & Topics // //
                    $lesson_list = learndash_30_get_course_sections($course->element_id);
                    foreach($lesson_list as $lessons){
                        foreach ($lessons->steps as $l => $lesson_id){
                            $result[$k]['Lessons'][$l]['id'] =  $lesson_id;
                            $result[$k]['Lessons'][$l]['title'] =  html_entity_decode(get_the_title($lesson_id));
                            $result[$k]['Lessons'][$l]['description'] =  html_entity_decode(get_the_excerpt($lesson_id));

                            $lesson_steps = learndash_course_get_children_of_step($course->element_id, $lesson_id);
                            foreach ($lesson_steps as $t => $step_id){
                                $result[$k]['Lessons'][$l]['Topics'][$t]['id'] = $step_id;
                                $result[$k]['Lessons'][$l]['Topics'][$t]['title'] = html_entity_decode(get_the_title($step_id));
                                $result[$k]['Lessons'][$l]['Topics'][$t]['description'] = html_entity_decode(get_the_excerpt($step_id));
                            }
                        }
                    }

                    // Quiz
                    $quizList = learndash_get_course_quiz_list($course->element_id, $user_id = null);
                    foreach($quizList as $q => $quiz){
                        $result[$k]['Quizzes']['id'] =  $quiz['id'];
                        $result[$k]['Quizzes']['title'] =  get_the_title($quiz['id']);
                    }


                }
            }
        }

        return $result;

    }    

    public static function get_course_test( $request ){

       $result = $request->get_param('test');
         /// Prepare and return the response
       $response = array(
            'success' => true,
            'data' => $result,
        );
        
        
        return $response;
    }

    public static function get_user_course_list($user_id){
        $result = []; 
        $response = [];         
        global $wpdb;
        $result =  $wpdb->get_results("SELECT * FROM {$wpdb->prefix}learndash_user_activity WHERE user_id = $user_id AND activity_type LIKE '%course%' GROUP BY course_id");

        if($result){
            foreach($result as $res){
                $response[] = $res->course_id;
            }
        }
        // echo "<pre>"; print_r($course_id);
        return $response;
    }

    // Course Progress Multiple Users
    public static function get_course_progress( $request ){

        $auth = self::getAuthentication( $request );

        if($auth != 1){
            return $auth;
        }

        $result = [];
        
        $email_list_arr = [];
        $email = $request->get_param('email');
        $email_list_arr = explode(',', $email);
        // print_r($email_list_arr);
        // exit;

        foreach($email_list_arr as $m => $email){
            // Check if the email parameter is valid
            // if (empty($email) || !is_email($email)) {
            //     $result[$m]['sts'] = new WP_Error('invalid_email', __('Invalid email parameter.'), array('status' => 400));
            // }

            $user = get_user_by('email', $email);
            $user_id = $user->ID;

            $result[$m]['email'] = $email;

            if (!email_exists($email)) {
                $result[$m]['response'] = 'User Not Exist';
                $result[$m]['courseProgress'] = null;
            }

            // $course_list = learndash_user_get_enrolled_courses( $user_id, $course_query_args = array(), $bypass_transient = false );

            $course_list = self::get_user_course_list($user_id);
        // print_r($course_list);
            
            foreach($course_list as $k => $course_id){

                $course_progress = CptCourse::get_user_progress($user_id, $course_id);
                $course_de = get_post($course_id);
                $result[$m]['courseProgress'][$k]['id'] =  html_entity_decode($course_id);
                $result[$m]['courseProgress'][$k]['title'] =  $course_de->post_title;
                $result[$m]['courseProgress'][$k]['progress'] = $course_progress['completed_percentage'];

            }

            if(empty($result[$m]['courseProgress']) && email_exists($email)){
                $result[$m]['courseProgress'] = [];
            }
        }

        // print_r($get_course_progress);
        return $result;

    }

    public function tag_fun($request){

        // $auth = self::getAuthentication( $request );

        // if($auth != 1){
        //     return $auth;
        // }

        $result = [];

        $tag = $request->get_param('tag');
        
        $args = array(
            'post_type' => 'sfwd-courses',
            'tax_query' => array(
        array(
            'taxonomy' => 'ld_course_tag',
            'field'    => 'slug',  // Can also use 'id' or 'name' depending on how you're filtering
            'terms'    => $tag,  // Replace with the slug of the tag you want to filter by
        ),
    ),
        );
        
        $query = new WP_Query( $args );
        
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $result['response'][] = get_the_title();
                // Display your course content here
            }
        } else {
            // No courses found
        }
        
        wp_reset_postdata();
 
         return $result;


    }

    public static function test_fun(){
        // return "hello";

        $result = [];          
        global $wpdb;
        $result =  $wpdb->get_results("SELECT * FROM {$wpdb->prefix}learndash_user_activity WHERE user_id = 1424 AND activity_type LIKE '%course%' GROUP BY course_id");

        if($result){
            foreach($result as $res){
                $result[] = $res->course_id;
            }
        }
        // echo "<pre>"; print_r($course_id);
        return $result;

    }

    

 


// CustomApi End 
}
// CustomApi End 

add_action( 'rest_api_init', function (){
   
    register_rest_route( 'custom/elearning/v1', '/course/progress', array(
        'methods' => 'POST', 
        'callback' => array(new CustomApi, 'get_course_progress' ),
    ));

    register_rest_route( 'custom/elearning/v1', '/course/list/(?P<language>\w+)', array(
        'methods' => 'GET', 
        'callback' => array(new CustomApi, 'get_courses_list' ),
        'args' => 'language'
    ));

    register_rest_route( 'custom/elearning/v1', '/course/badges', array(
        'methods' => 'POST', 
        'callback' => array(new CustomApi, 'get_user_badges' ),
    ));

    register_rest_route( 'custom/elearning/v1', '/course', array(
        'methods' => 'POST', 
        'callback' => array(new CustomApi, 'get_course_by_id' )
    ) );

    register_rest_route( 'custom/elearning/v1', '/course/test', array(
        'methods' => 'POST', 
        'callback' => array(new CustomApi, 'get_course_test' )
    ) );

     // testing route
     register_rest_route( 'custom/elearning/v1', '/tag', array(
        'methods' => 'POST', 
        'callback' => array(new CustomApi, 'tag_fun' ),
    ));

    // testing route
    register_rest_route( 'custom/elearning/v1', '/test/a3/b1', array(
        'methods' => 'GET', 
        'callback' => array(new CustomApi, 'test_fun' ),
    ));
    

 

    
});
