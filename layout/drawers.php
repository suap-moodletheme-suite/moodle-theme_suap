<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A drawer based layout for the boost theme.
 *
 * @package   theme_suap
 * @copyright 2021 Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

// User role for course context
if (isloggedin()) {
    $rolestr;
    $context = context_course::instance($COURSE->id);
    $roles = get_user_roles($context, $USER->id, true);

    if (empty($roles)) {
        $rolestr = "";
    } else {
        foreach ($roles as $role) {
            $rolestr[] = role_get_name($role, $context);
        }
        $rolestr = implode(', ', $rolestr);
    }
}

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$counterClose = get_user_preferences('theme_suap_counter_close');
if ($counterClose) {
    $extraclasses[] = 'counter-close';
}

// Pega as preferências do usuário
$courseindexopen = get_user_preferences('theme_suap_index_drawer_open');
$blockdraweropen = get_user_preferences('theme_suap_blocks_drawer_open');

// Verifica se a gaveta de blocos existe
$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}

// Verifica se o índice do curso existe
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

// Adiciona a classe drawer-open apenas se alguma gaveta existir
if ($courseindexopen || $blockdraweropen) {
    $extraclasses[] = 'drawer-open';
}

if (isguestuser()) {
    $extraclasses[] = 'guestuser';
}


$pageType = $PAGE->pagetype;

// Checar se está na página de enrol de curso
$is_enrol_course_page = false;
$enrolpage_and_guestuser = false;
if ($pageType == 'enrol-index') {
    $is_enrol_course_page = true;
    $extraclasses[] = 'layout-width-expanded';
    $extraclasses[] = 'enrol-page';
    if (isguestuser()) {
        $enrolpage_and_guestuser = true;
        $extraclasses[] = 'counteroff';
    }
}

// Se está no dashboard
if ($pageType == 'my-index') {
    $extraclasses[] = 'layout-width-expanded';
}

$conf = get_config('theme_suap');

$frontpage_buttons_configtextarea = parse_configtextarea_string($conf->frontpage_buttons_configtextarea);
$frontpage_buttons_configtextarea_when_user_logged = parse_configtextarea_string($conf->frontpage_buttons_configtextarea_when_user_logged);


use core_course\customfield\course_handler;

$handler = course_handler::create();
$datas = $handler->get_instance_data($COURSE->id, true);

$customfields_suap_shortnames = [
    'campus_sigla',
    'curso_codigo',
    'turma_codigo',
    'disciplina_sigla',
    'curso_sala_coordenacao',
];

$custom_fields = [];

foreach ($datas as $data) {
    $shortname = $data->get_field()->get('shortname');
    $valor = $data->get_value();

    if (in_array($shortname, $customfields_suap_shortnames) && !empty($valor)) {
        $custom_fields[$shortname] = $valor;

        if ($shortname === 'curso_sala_coordenacao') {
            if (mb_strtolower(trim($valor)) === 'sim') {
                $extraclasses[] = 'curso_sala_coordenacao';
            }
        } else {
            $classe = $shortname . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $valor);
            $extraclasses[] = $classe;
        }
    }
}


$extraclasses = array_merge($extraclasses, theme_suap_get_accessibility_classes($USER));

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);
$navbar = $OUTPUT->navbar();

$isloggedin = isloggedin();
$is_admin = is_siteadmin($USER->id);

$userid = $USER->id;

// pega a preferencia no banco
$preferenceCounter = get_user_preferences('visual_preference');

// região de blocos apenas na página inicial dos cursos
if ($PAGE->pagelayout == 'course') {
    $addcontentblockbutton = $OUTPUT->addblockbutton('content');
    $contentblocks = $OUTPUT->custom_block_region('content');
    $addfooterblockbutton = $OUTPUT->addblockbutton('footerblock');
    $footerblocks = $OUTPUT->custom_block_region('footerblock');
}

include('_menu.php');

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'logout' => $_logoutlink,
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'navbar' => $navbar,
    'userid' => $userid,
    'rolename' => $rolestr,
    'isloggedin' => $isloggedin,
    'isguestuser' => isguestuser(),
    'is_admin' => $is_admin,
    'theme_suap_items_user_menu_admin' => theme_suap_add_admin_items_user_menu(),
    'preferenceCounter' => $preferenceCounter,
    'isenrolpage' => $is_enrol_course_page,
    'enrolpage_and_guestuser' => $enrolpage_and_guestuser,
    'frontpage_buttons_configtextarea' => $frontpage_buttons_configtextarea,
    'frontpage_buttons_configtextarea_when_user_logged' => $frontpage_buttons_configtextarea_when_user_logged,
    'addcontentblockbutton' => isset($addcontentblockbutton) ? $addcontentblockbutton : '',
    'contentblocks' => isset($contentblocks) ? $contentblocks : '',
    'addfooterblockbutton' => isset($addfooterblockbutton) ? $addfooterblockbutton : '',
    'footerblocks' => isset($footerblocks) ? $footerblocks : '',
    'contentbutton' => get_string('contentbutton', 'theme_suap'),
    'contentbuttonurl' => $CFG->wwwroot . '/course/view.php?id=' . $COURSE->id,
    'isactivecontentbutton' => theme_suap_is_contentbutton_active(),

    'footer_title' => $conf->footer_title,
    'footer_support_button' => $conf->footer_support_button,
    'footer_support_button_url' => $conf->footer_support_button_url,
    'footer_social_media_text' => $conf->footer_social_media_text,
    'footer_social_media_facebook' => $conf->footer_social_media_facebook,
    'footer_social_media_instagram' => $conf->footer_social_media_instagram,
    'footer_social_media_youtube' => $conf->footer_social_media_youtube,

    'footer_social_media_icon_1' => $conf->social_icon1_url ?? '',
    'footer_social_media_icon_2' => $conf->social_icon2_url ?? '',
    'footer_social_media_icon_1_alt' => $conf->footer_social_media_icon_1_alt ?? '',
    'footer_social_media_icon_2_alt' => $conf->footer_social_media_icon_2_alt ?? '',

    'footer_map_list' => $conf->footer_map_list,

    'footer_credits_text' => $conf->footer_credits_text,
    'footer_credits_first_link' => $conf->footer_credits_first_link,
    'footer_credits_second_link' => $conf->footer_credits_second_link,
    'footer_credits_first_link_url' => $conf->footer_credits_first_link_url,
    'footer_credits_second_link_url' => $conf->footer_credits_second_link_url,
    'footer_credits_first_link_new_window' => $conf->footer_credits_first_link_new_window,
    'footer_credits_second_link_new_window' => $conf->footer_credits_second_link_new_window,

    'accessibility_zoom_level' => get_user_preferences('theme_suap_accessibility_zoom_level', 100),
];
echo $OUTPUT->render_from_template('theme_boost/drawers', $templatecontext);
