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

/**
 *
 * @package    theme_suap
 * @copyright 2024 IFRN
 */

namespace theme_suap\output\core_user;

use core_user\output\myprofile\renderer as base_renderer;
use core_user\output\myprofile\tree;
use core_user\output\myprofile\manager;

class myprofile_renderer extends base_renderer {
    public function get_user_certificates($userid) {
        global $DB;

        $manager = $DB->get_manager();

        $all_certificates = [];

        // Get user certificates (plugin Course Certificates)
        if ($manager->table_exists('tool_certificate_issues')) {
            $tool_certificates = $DB->get_records('tool_certificate_issues', ['userid' => $userid]);
        }

        // Get user certificates (plugin Custom certificates)
        if ($manager->table_exists('customcert_issues')) {
            $custom_certificates = $DB->get_records('customcert_issues', ['userid' => $userid]);
        }

        if (!empty($tool_certificates)) {
            foreach ($tool_certificates as $cert) {
                $cert_data = json_decode($cert->data);
                $certificate_name = $cert_data->coursefullname;

                $contextid = \context_system::instance()->id;  // Obtém o contexto do sistema
                $fileurl = \moodle_url::make_pluginfile_url($contextid, 'tool_certificate', 'issues', $cert->id, '/', $cert->code . '.pdf', false);

                if ($cert->expires > 0) {
                    $expirationYear = date('Y', $cert->expires);
                    $expirationMonth = date('m', $cert->expires);
                }

                $createdYear = date('Y', ($cert->timecreated));
                $createdMonth = date('m', ($cert->timecreated));
                $cert_url = new \moodle_url('/admin/tool/certificates/index.php', ['code' => $cert->code]);
                $cert_url_encoded = urlencode($cert_url->__toString());
                $linkedin_url = "https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name={$certificate_name}&organizationName={$sitename}&issueYear={$createdYear}&issueMonth={$createdMonth}&expirationYear={$expirationYear}&expirationMonth={$expirationMonth}&certId={$cert->code}&certUrl={$cert_url_encoded}";

                $all_certificates[] = [
                    'certificateid' => $cert->id,
                    'datereceived' => date('d/m/Y', $cert->timecreated),
                    'name' => $certificate_name,
                    'link' => $fileurl,
                    'linkedin_url' => $linkedin_url,
                ];
            }
        }

        if (!empty($custom_certificates)) {
            foreach ($custom_certificates as $cert) {
                $certificate_name = $DB->get_field('customcert', 'name', ['id' => $cert->customcertid]);

                $all_certificates[] = [
                    'certificateid' => $cert->customcertid,
                    'dateraw' => $cert->timecreated,
                    'datereceived' => date('d/m/Y', $cert->timecreated),
                    'name' => $certificate_name,
                    'link' => new \moodle_url('/mod/customcert/my_certificates.php', [
                        'userid' => $userid,
                        'certificateid' => $cert->customcertid,
                        'downloadcert' => 1,
                    ]),
                ];
            }
        }

        // Ordena o array $all_certificates em ordem decrescente
        usort($all_certificates, function ($a, $b) {
            return $b['dateraw'] - $a['dateraw'];
        });

        return $all_certificates;
    }

    public function get_user_badges($userid) {
        global $CFG;
        require_once($CFG->libdir . '/badgeslib.php');

        $badges = badges_get_user_badges($userid);
        $badges_formated = [];

        if (empty($badges)) {
            return false;
        }

        foreach ($badges as $badge) {
            $badgeObj = new \badge($badge->id);
            $badge_context = $badgeObj->get_context();
            $imageurl = \moodle_url::make_pluginfile_url($badge_context->id, 'badges', 'badgeimage', $badge->id, '/', 'f1', false);
            $badge_link = new \moodle_url('/badges/badge.php', ['hash' => $badge->uniquehash]);

            $createdYear = date('Y', ($badge->timecreated));
            $createdMonth = date('m', ($badge->timecreated));
            $expirationYear = '';
            $expirationMonth = '';

            if ($badge->expiredate) {
                $expirationYear = date('Y', $badge->expiredate);
                $expirationMonth = date('m', $cert->expiredate);
            }

            $badge_linkedin = "https://www.linkedin.com/profile/add?startTask=CERTIFICATION_NAME&name={$badge->name}
            &organizationName={$badge->issuername}&issueYear={$createdYear}&issueMonth={$createdMonth}&expirationYear={$expirationYear}
            &expirationMonth={$expirationMonth}&certId={$badge->uniquehash}";

            $badges_formated[] = [
                'name' => $badge->name,
                'description' => $badge->description,
                'datereceived' => date('d/m/Y', $badge_issued->dateissued),
                'imageurl' => $imageurl,
                'link' => $badge_link,
                'badgelink' => $badge_linkedin,
            ];
        }

        return $badges_formated;
    }

    /**
     * Render the whole profile tree as a dropdown-accordion via Mustache template.
     *
     * @param tree $tree The populated profile tree
     * @return string HTML fragment
     */
    public function render_tree(tree $tree) {

        global $DB, $CFG, $USER, $PAGE, $COURSE, $OUTPUT, $SITE;

        $is_admin = is_siteadmin($USER->id);

        $userid         = optional_param('id', 0, PARAM_INT); // User id.
        $courseid       = optional_param('course', SITEID, PARAM_INT); // course id (defaults to Site).
        $sitename       = format_string($SITE->shortname, true, ['context' => \context_course::instance(SITEID), "escape" => false]);

        // See your own profile by default.
        if (empty($userid)) {
            require_login();
            $userid = $USER->id;
        }

        $user = \core_user::get_user($userid);

        // edit profile button
        $has_edit_button = false;

        if ($is_admin || $USER->id == $userid) {
            $useremail = $user->email;
            $has_edit_button = true;
        }

        $edit_itens = [
            'id' => $userid,
            'course' => $courseid,
            'returnto' => 'profile',
        ];

        if ($is_admin) {
            $edit_url = new \moodle_url('/user/editadvanced.php', $edit_itens);
        } else {
            $edit_url = new \moodle_url('/user/edit.php', $edit_itens);
        }

        // My profile
        $is_my_profile = $USER->id == $userid;

        // Send message button
        if ($USER->id != $userid) {
            $message_url = new \moodle_url('/message/index.php', ['id' => $userid]);
        }

        $context = \context_course::instance($COURSE->id);

        $myrolestr;
        $myroles = get_user_roles($context, $userid, true);

        if (empty($myroles)) {
            $myrolestr = "";
        } else {
            foreach ($myroles as $myrole) {
                $myrolestr[] = role_get_name($myrole, $context);
            }
            $myrolestr = implode(', ', $myrolestr);
        }

        // Get profile image and alternative text
        $profile_picture = new \user_picture($user);
        $profile_picture->size = 50;
        $profile_picture_url = $profile_picture->get_url($PAGE)->out();
        $profile_picture_alt = $user->imagealt;

        $all_certificates = $this->get_user_certificates($userid);

        $badges = $this->get_user_badges($userid);

        $categories = [];
        $categories_user = [];
        foreach ($tree->categories as $category) {
            $categoryname = strtolower($category->name);

            if ($categoryname === 'contact' && !$is_admin && !$is_my_profile) {
                continue;
            }

            if (in_array(strtolower($category->name), ['contact', 'loginactivity', 'coursedetails'])) {
                $nodes = [];
                foreach ($category->nodes as $node) {
                    if ($node->name == 'editprofile' || $node->name == 'email') {
                        continue;
                    }
                    if ($node->url) {
                        $url = $node->url->out();
                    } else {
                        $url = false;
                    }
                    $nodes[] = [
                        'title' => $node->title,
                        'url' => $url,
                        'content' => $node->content ?? null,
                    ];
                }
                $categories_user[] = [
                    'title' => $category->title,
                    'nodes' => $nodes,
                ];
                continue;
            }

            $nodes = [];
            $allnodes = array_values($category->nodes); // reindex numericamente
            $count = count($allnodes);

            foreach ($allnodes as $idx => $node) {
                if ($node->url) {
                    $url = $node->url->out();
                } else {
                    continue; // sem url não entra na category
                }
                $text = $node->title;

                $nodes[] = [
                    'text'  => $text,
                    'url'   => $url,
                    'first' => ($idx === 0),
                    'last'  => ($idx === $count - 1),
                ];
            }

            if (empty($nodes)) {
                continue; // sem nodes não vai para o mustache
            }

            $categories[] = [
                'title' => $category->title,
                'nodes' => $nodes,
            ];
        }

        $PAGE->requires->js_call_amd('theme_suap/messages', 'getConversation');

        $data = [
            'userfullname' => fullname($user),
            'useremail' => $useremail,
            'haseditbutton' => $has_edit_button,
            'editprofile' => $edit_url,
            'userdescription' => $user->description,
            'userpictureurl' => $profile_picture_url,
            'userpicturealt' => $profile_picture_alt,
            'hascertificates' => !empty($all_certificates),
            'certificates' => $all_certificates,
            'hasbadges' => !empty($badges),
            'badges' => $badges,
            'useridprofile' => $userid,
            'myrolename' => $myrolestr,
            'is_my_profile' => $is_my_profile,

            'categories' => $categories,
            'categories_user' => $categories_user,
        ];

        $PAGE->requires->js_call_amd('theme_suap/profile', 'init');

        return $this->render_from_template('theme_suap/profile_user', $data);
    }
}
