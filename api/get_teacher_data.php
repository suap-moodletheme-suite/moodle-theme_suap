<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

require_once('../../../config.php');
require_once($CFG->libdir . '/enrollib.php');

header('Content-Type: application/json');

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);

$clist = new \core_course_list_element($course);
$teachers = $clist->get_course_contacts();
$result = [];

foreach ($teachers as $teacher) {
    $usercourses = enrol_get_users_courses($teacher['user']->id);
    $teacher_courses = [];
    $total_students = 0;

    foreach ($usercourses as $course) {
        $context = \context_course::instance($course->id);

        $roles = get_user_roles($context, $teacher['user']->id);
        foreach ($roles as $role) {
            if ($role->shortname == 'editingteacher') {
                $teacher_courses[] = [
                    'id'        => $course->id,
                    'fullname'  => $course->fullname,
                    'shortname' => $course->shortname,
                ];

                // Quantidade de alunos no curso
                $participants = get_enrolled_users($context, '', 0, 'u.id');
                foreach ($participants as $participant) {
                    $participant_roles = get_user_roles($context, $participant->id);
                    foreach ($participant_roles as $participant_role) {
                        if ($participant_role->shortname == 'student') {
                            $total_students++;
                            break;
                        }
                    }
                }
                break;
            }
        }
    }

    $result[] = [
        'teacherid'       => $teacher['user']->id,
        'teacherfullname' => $teacher['user']->firstname . ' ' . $teacher['user']->lastname,
        'totalstudents'   => $total_students,
        'courses'         => $teacher_courses,
    ];
}

echo json_encode($result);
exit;
