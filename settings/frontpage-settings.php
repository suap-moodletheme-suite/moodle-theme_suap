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

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . "/settings_page_tab.php");


class frontpage_settings_tab extends settings_page_tab {
    public function __construct() {
        parent::__construct('theme_suap_frontpage', 'frontpagesettings');
    }

    protected function create_page_settings() {
        $this->add_setting_configtext('frontpage_title', 'Teste');
        $this->add_setting_configtextarea('frontpage_buttons_configtextarea', true, 'frontpage_button_home,theme_suap|/?redirect=0||| (/n) frontpage_button_about,theme_suap|theme/suap/about.php|||');
        $this->add_setting_configtextarea('frontpage_buttons_configtextarea_when_user_logged', true, 'frontpage_button_courses,theme_suap|my/courses.php||| (/n) frontpage_button_learningpaths,theme_suap|theme/suap/learningpaths.php|||0');

        $this->add_setting_configtext('footer_title', 'Moodle');
        $this->add_setting_configtext('footer_support_button', 'Botão para o suporte');
        $this->add_setting_configtext('footer_support_button_url', 'https://ajuda.ead.ifrn.edu.br/');

        $this->add_setting_configtext('footer_social_media_text', 'Texto sobre as redes sociais do IFRN ZL');
        $this->add_setting_configtext('footer_social_media_facebook', 'https://www.facebook.com/ifrnzonaleste');
        $this->add_setting_configtext('footer_social_media_instagram', 'https://www.instagram.com/ifrnzonaleste/');
        $this->add_setting_configtext('footer_social_media_youtube', 'https://www.youtube.com/c/ifrncampusead');

        $this->add_setting_confightmleditor('footer_map_list', true, '<div>
                                                                        <span>Título da coluna</span>
                                                                        <ul>
                                                                            <li><a href="#">Item um</a></li>
                                                                            <li><a href="#">Item dois</a></li>
                                                                            ...
                                                                        </ul>
                                                                     </div>
                                                                     ...');

        $this->add_setting_configtext('footer_credits_text', 'Tema: © 2024 IFRN/ZL - DEAD. All Rights Reserved.');

        $this->add_setting_configtext('footer_credits_first_link', 'Moodle');
        $this->add_setting_configtext('footer_credits_first_link_url', 'https://aberto.ava.ifrn.edu.br/');
        $this->add_setting_configcheckbox('footer_credits_first_link_new_window', false);

        $this->add_setting_configtext('footer_credits_second_link', 'Ajuda');
        $this->add_setting_configtext('footer_credits_second_link_url', 'https://ajuda.ead.ifrn.edu.br/');
        $this->add_setting_configcheckbox('footer_credits_second_link_new_window', false);
    }
}
