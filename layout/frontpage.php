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

defined('MOODLE_INTERNAL') || die();

use core_course\course;

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

require_once($CFG->dirroot . '/theme/suap/lib.php');

global $DB;

// require_once(__DIR__ . '/../../config.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING')) {
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

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $secondary = $PAGE->secondarynav;

    if ($secondary->get_children_key_list()) {
        $tablistnav = $PAGE->has_tablist_secondary_navigation();
        $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
        $secondarynavigation = $moremenu->export_for_template($OUTPUT);
        $extraclasses[] = 'has-secondarynavigation';
    }

    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

// A frontpage utiliza a largura maior
$extraclasses[] = 'layout-width-expanded';

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

if (!isloggedin() || isguestuser()) {
    $extraclasses[] = 'counteroff';
}

// Usuário possui capacidade de editar página
if (
    has_capability('moodle/course:manageactivities', $PAGE->context) ||
    (!isloggedin() || isguestuser())
) {
    $extraclasses[] = 'editswitchon';
    $viewNavbar = true;
}
// pega a preferencia no banco
$preferenceCounter = get_user_preferences('visual_preference');

$extraclasses = array_merge($extraclasses, theme_suap_get_accessibility_classes($USER));

$bodyattributes = $OUTPUT->body_attributes($extraclasses);

// áreas blocos
$addcontentpreblockbutton = $OUTPUT->addblockbutton('content-pre');
$contentpreblocks = $OUTPUT->custom_block_region('content-pre');

$addcontentposblockbutton = $OUTPUT->addblockbutton('content-pos');
$contentposblocks = $OUTPUT->custom_block_region('content-pos');

$conf = get_config('theme_suap');
$frontpage_buttons_configtextarea = parse_configtextarea_string($conf->frontpage_buttons_configtextarea);
$frontpage_buttons_configtextarea_when_user_logged = parse_configtextarea_string($conf->frontpage_buttons_configtextarea_when_user_logged);

$learningpaths_records = $DB->get_records('suap_learning_path', null, '', 'id, name');
$learningpaths = [];
foreach ($learningpaths_records as $learningpath) {
    $learningpath_obj = new stdClass();
    $learningpath_obj->id = $learningpath->id;
    $learningpath_obj->name = $learningpath->name;
    $learningpaths[] = $learningpath_obj;
}

include('_menu.php');

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'isloggedin' => isloggedin(),
    'userid' => $USER->id,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'theme_suap_items_user_menu_admin' => theme_suap_add_admin_items_user_menu(),
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'logout' => $_logoutlink,
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,

    'addcontentpreblockbutton' => isset($addcontentpreblockbutton) ? $addcontentpreblockbutton : '',
    'contentpreblocks' => isset($contentpreblocks) ? $contentpreblocks : '',
    'addcontentposblockbutton' => isset($addcontentposblockbutton) ? $addcontentposblockbutton : '',
    'contentposblocks' => isset($contentposblocks) ? $contentposblocks : '',

    'frontpage_title' => $conf->frontpage_title,
    'about_title' => isset($conf->about_title) ? $conf->about_title : '',
    'frontpage_buttons_configtextarea' => $frontpage_buttons_configtextarea,
    'frontpage_buttons_configtextarea_when_user_logged' => $frontpage_buttons_configtextarea_when_user_logged,
    'learningpaths' => $learningpaths,
    'viewnavbar' => $viewNavbar,
    'preferenceCounter' => $preferenceCounter,
    'loggedin_and_notguestuser' => isloggedin() && !isguestuser(),

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
// throw new Exception("Error Processing Request", 1);

echo $OUTPUT->render_from_template('theme_suap/frontpage', $templatecontext);
