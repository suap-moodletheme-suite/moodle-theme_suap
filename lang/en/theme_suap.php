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
// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// A description shown in the admin theme selector.
$string['choosereadme'] = 'Theme SUAP is a child theme of Boost.';
// The name of our plugin.
$string['pluginname'] = 'SUAP';
// We need to include a lang string for each block region.
$string['region-side-pre'] = 'Right';
// The name of the second tab in the theme settings.
$string['advancedsettings'] = 'Advanced settings';
// The brand colour setting.
$string['brandcolor'] = 'Brand colour';
// The brand colour setting description.
$string['brandcolor_desc'] = 'The accent colour.';
// A description shown in the admin theme selector.
$string['configtitle'] = 'SUAP theme settings';
// Name of the first settings tab.
$string['generalsettings'] = 'General settings';
// Preset files setting.
$string['presetfiles'] = 'Additional theme preset files';
// Preset files help text.
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files, and see the <a href=http://moodle.net/boost>Presets repository</a> for presets that others have shared.';
// Preset setting.
$string['preset'] = 'Theme preset';
// Preset help text.
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
// Raw SCSS setting.
$string['rawscss'] = 'Raw SCSS';
// Raw SCSS setting help text.
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';
// Raw initial SCSS setting.
$string['rawscsspre'] = 'Raw initial SCSS';
// Raw initial SCSS setting help text.
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';

// Drawers aditional strings
$string['drawer_course_index'] = "Course index";
$string['drawer_blocks'] = "Blocks";
$string['drawer_user'] = "User menu";
$string['allconversations'] = "All";
$string['unreadmessages'] = "Unread";
$string['user_preference_menu'] = "Menu at the bottom";
$string['preference_panel'] = "Preference panel";

$string['accessibility'] = "Accessibility";
$string['dyslexia_friendly'] = "Dyslexia friendly font";
$string['align_left'] = "Align text to the left";
$string['highlight_links'] = "Highlight links";
$string['stop_animations'] = "Stop animations";
$string['hide_illustrative_images'] = "Hide illustrative images";
$string['increase_cursor_size'] = "Increase cursor size";
$string['enable_vlibras'] = "Enable VLibras";
$string['high_line_height'] = "High line height";

// Frontpage aditional strings
$string['workload'] = 'Total hours';
$string['certificate'] = 'Certificate';
$string['pt-br'] = 'Portuguese';
$string['es'] = 'Spanish';
$string['upto_hours'] = 'Up to {$a} hours';

// frontpage-settings.php
$string['frontpagesettings'] = 'Frontpage settings';
$string['frontpage_title'] = 'Frontpage title';
$string['frontpage_title_desc'] = '';
$string['frontpage_buttons_configtextarea'] = 'Frontpage buttons configuration';
$string['frontpage_buttons_configtextarea_desc'] = 'Delete the (/n) snippet and press "Enter" to apply the line break';
$string['frontpage_button_home'] = 'Home';
$string['frontpage_button_about'] = 'About';

$string['pagination_secret'] = 'Pagination secret';
$string['pagination_secret_desc'] = 'It is necessary to create a token in the web services section of Moodle for mobile devices';

$string['frontpage_main_courses_title'] = 'Frontpage main courses title';
$string['frontpage_main_courses_title_desc'] = '';
$string['frontpage_buttons_configtextarea_when_user_logged'] = 'Frontpage buttons configuration when user is logged';
$string['frontpage_buttons_configtextarea_when_user_logged_desc'] = 'Delete the (/n) snippet and press "Enter" to apply the line break';
$string['frontpage_button_courses'] = 'Courses';
$string['frontpage_button_courses_desc'] = '';
$string['frontpage_button_learningpaths'] = 'Learning paths';
$string['frontpage_button_learningpaths_desc'] = '';

// Footer settings.
$string['footer_title'] = 'Footer title';
$string['footer_title_desc'] = 'Main title displayed in the footer.';

$string['footer_support_button'] = 'Support button';
$string['footer_support_button_desc'] = 'Label for the support button in the footer.';
$string['footer_support_button_url'] = 'Support button link';
$string['footer_support_button_url_desc'] = '';

$string['footer_social_media_text'] = 'Social media text';
$string['footer_social_media_text_desc'] = 'Text about the IFRN ZL social media in the footer.';
$string['footer_social_media_facebook'] = 'Facebook URL';
$string['footer_social_media_facebook_desc'] = '';
$string['footer_social_media_instagram'] = 'Instagram URL';
$string['footer_social_media_instagram_desc'] = '';
$string['footer_social_media_youtube'] = 'Youtube URL';
$string['footer_social_media_youtube_desc'] = '';

// Footer map
$string['footer_map_list'] = 'Footer Link Lists';
$string['footer_map_list_desc'] = '';

// Footer credits.
$string['footer_credits_text'] = 'Footer credits';
$string['footer_credits_text_desc'] = 'Text for the footer credits.';

$string['footer_credits_first_link'] = 'First credit link';
$string['footer_credits_first_link_desc'] = '';
$string['footer_credits_first_link_url'] = 'URL of first credit link';
$string['footer_credits_first_link_url_desc'] = '';
$string['footer_credits_first_link_new_window'] = 'Open new window first link';
$string['footer_credits_first_link_new_window_desc'] = '';

$string['footer_credits_second_link'] = 'Second credit link';
$string['footer_credits_second_link_desc'] = '';
$string['footer_credits_second_link_url'] = 'URL of second credit link';
$string['footer_credits_second_link_url_desc'] = '';
$string['footer_credits_second_link_new_window'] = 'Open new window second link';
$string['footer_credits_second_link_new_window_desc'] = '';

// Incourse aditional strings
$string['contentbutton'] = 'Content';

// Profile aditional strings
$string['aboutme'] = 'About me';
$string['certificates'] = 'Certificates';
$string['describe_yourself'] = 'Describe yourself to your community';
$string['no_your_certificates'] = 'Complete a course to obtain certificates';
$string['no_your_badges'] = 'Explore our courses to earn badges';
$string['no_description'] = 'No description yet';
$string['no_certificates'] = 'No certificates';
$string['no_badges'] = 'No badges to display';

// Enrolment aditional strings
$string['issue_certificate'] = 'Issue certificate';
$string['login'] = 'Log in';
$string['no_description_course'] = 'No course description available yet';
$string['overview'] = 'Overview';
$string['instructor'] = 'Instructor';
$string['instructors'] = 'Instructors';
$string['comments'] = 'Comments';
$string['no_description_instructor'] = 'No instructor description available';

// Setting layout navigation menu
$string['layouttype'] = 'Always show top menu';
$string['layouttype_desc'] = 'The top navigation menu is used in Moodle that are not integrated into the Painel AVA';
