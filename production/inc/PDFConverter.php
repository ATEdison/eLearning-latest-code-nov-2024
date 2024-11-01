<?php

namespace STA\Inc;

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFConverter {

    private static $instance;

    public static function instance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'serve_pdf'], 1);
    }

    public function serve_pdf() {
        if (!is_user_logged_in()) {
            return;
        }

        $request_uri = $_SERVER['REQUEST_URI'];
        // var_dump($request_uri); die;

        if (preg_match('@/pdf/certificate/?@', $request_uri)) {
            self::generate_certificate();
            return;
        }

        preg_match_all('@/pdf/(.*)/([0-9]*)/?@', $request_uri, $matches);
        // printf('<pre>%s</pre>', var_export($matches, true));die;
        if (count($matches) != 3) {
            return;
        }

        $type = ($matches[1] ?? [])[0] ?? '';
        $post_id = ($matches[2] ?? [])[0] ?? '';

        if ($type == 'lesson' && get_post_type($post_id) == CptCourseLesson::$post_type) {
            self::course_post_to_pdf($post_id);
            return;
        }

        if ($type == 'topic' && get_post_type($post_id) == CptCourseTopic::$post_type) {
            self::course_post_to_pdf($post_id);
            return;
        }
    }

    private static function generate_certificate() {
        $user = wp_get_current_user();
        $last_time_pass_a_core_course = \STA\Inc\PointLogs::last_time_pass_a_core_course($user->ID);

        // the user has not passed any core course
        if (!$last_time_pass_a_core_course) {
            return;
        }

        $filename = 'certificate.pdf';

        ob_start();
        get_template_part('template-parts/pdf/certificate', '', [
            'user_id' => $user->ID,
            'last_time_pass_a_core_course' => $last_time_pass_a_core_course,
        ]);
        // die;
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        // $dompdf->setPaper('A4', 'landscape');
        // $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream($filename, [
            'Attachment' => false,
        ]);
        exit();
    }

    public static function image_file_to_base64_url($image_path) {
        $type = pathinfo($image_path, PATHINFO_EXTENSION);
        $data = file_get_contents($image_path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    private static function course_post_to_pdf($post_id) {
        $user_id = get_current_user_id();
        $course_id = get_post_meta($post_id, 'course_id', true);

        // empty course
        if (!$course_id) {
            return;
        }

        // user does not have permission to access this course
        if (!CptCourse::user_can_enroll_course($user_id, $course_id)) {
            return;
        }

        $filename = sprintf('%s-%s.pdf', get_post_field('post_name', $course_id), get_post_field('post_name', $post_id));

        ob_start();
        get_template_part('template-parts/pdf/pdf-content', '', [
            'post_title' => get_the_title($post_id),
            'post_link' => get_permalink($post_id),
            'post_content' => get_post_field('post_content', $post_id),
        ]);
        $html = ob_get_clean();

        self::html_to_pdf($filename, $html);
        die;
    }

    private static function html_to_pdf($filename, $html) {
        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        // $dompdf->setPaper('A4', 'landscape');
        // $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream($filename, [
            'Attachment' => false,
        ]);
        // echo htmlentities($dompdf->outputHtml());
    }

    public static function download_url($post_id) {
        $post_type = get_post_type($post_id);

        switch ($post_type) {
            case CptCourseLesson::$post_type:
                return home_url(sprintf('/pdf/lesson/%s', $post_id));
            case CptCourseTopic::$post_type:
                return home_url(sprintf('/pdf/topic/%s', $post_id));
        }

        return '#';
    }
}
