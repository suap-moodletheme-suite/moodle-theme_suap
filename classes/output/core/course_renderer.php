<?php

namespace theme_suap\output\core;

use core_course\course;
use html_writer;
use stdClass;
use core_course\external\course_summary_exporter;

use core_course\customfield\course_handler;

class course_renderer extends \core_course_renderer
{
    /**
     * Renders course info box.
     *
     * @param stdClass $course
     * @return string
     */
    public function course_info_box(stdClass $course)
    {
        global $OUTPUT, $DB, $CFG;
        // Pega os custom fields que tiver no curso
        $handler = course_handler::create();
        $datas = $handler->get_instance_data($course->id, true);

        foreach ($datas as $data) {
            if (empty($data->get_value())) {
                continue;
            }
            // $cat = $data->get_field()->get_category()->get('name');
            $custom_fields[$data->get_field()->get('shortname')] = $data->get_value();
        }

        $categoryid = $course->category;
        $category = $DB->get_record('course_categories', ['id' => $categoryid]);

        $imageurl = course_summary_exporter::get_course_image($course);
        if (!$imageurl) {
            $imageurl = $CFG->wwwroot . '/theme/suap/pix/default-course-image.webp';
        }
        $enrolment_methods = enrol_get_instances($course->id, true);
        $enrolment_types = [];

        $self_enrolment = null;
        foreach ($enrolment_methods as $method) {
            $enrolment_types[] = $method->enrol;
            if ($method->enrol === 'self' && $method->status == ENROL_INSTANCE_ENABLED) {
                $require_password = !empty($method->password);

                $self_enrolment = [
                    'id' => $course->id,
                    'instance' => $method->id,
                    'sesskey' => sesskey(),
                    'require_password' => $require_password
                ];
            }
        };

        $clist = new \core_course_list_element($course);
        $teachers = $clist->get_course_contacts();
        $list_teachers = [];

        foreach ($teachers as $teacher) {
            $record = $DB->get_record("user", ["id" => $teacher['user']->id]);
            $record->pic =  $OUTPUT->user_picture($record, ['size' => 100, 'link' => true]);

            $list_teachers[] = $record;
        }

        $are_teachers = count($list_teachers) > 1;

        global $PAGE;
        $request_url = $CFG->wwwroot . '/theme/suap/api/get_teacher_data.php';
        $PAGE->requires->js_call_amd('theme_suap/teacher_data', 'init', [$request_url]);

        // Get the current language course
        $lang_data = $OUTPUT->get_lang_menu_data();

        // Verifica se o plugin de avaliação de curso em bloco está presente
        $coursecontext = \context_course::instance($course->id);
        $has_rating_plugin = $DB->record_exists('block_instances', [
            'blockname' => 'course_rating',
            'parentcontextid' => $coursecontext->id
        ]);

        $templatecontext = [
            'fullcoursename' => $course->fullname,
            'summary' => $course->summary,
            'teachers' => $list_teachers,
            'are_teachers' => $are_teachers,
            'category' => $category->name,
            'imageurl' => $imageurl,
            'self_enrolment' => $self_enrolment,
            'workload' => isset($custom_fields['carga_horaria']) ? $custom_fields['carga_horaria'] : null,
            'has_certificate' => isset($custom_fields['tem_certificado']) ? $custom_fields['tem_certificado'] : false,
            'course_id' => $course->id,
            'langactivename' => $lang_data['langactivename'],
            'isguestuser' => isguestuser(),
            'has_rating_plugin' => $has_rating_plugin
        ];
        echo $OUTPUT->render_from_template('theme_suap/enroll_course', $templatecontext);
    }
}
