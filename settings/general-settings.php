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


class general_settings_tab extends settings_page_tab {
    public function __construct() {
        parent::__construct('theme_suap_general', 'generalsettings');
    }

    protected function create_page_settings() {
        // Replica a configuração predefinida do tema Boost.
        $name = 'preset';
        $default = 'default.scss';
        $context = context_system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'theme_suap', 'preset', 0, 'itemid, filepath, filename', false);

        $choices = [];
        foreach ($files as $file) {
            $choices[$file->get_filename()] = $file->get_filename();
        }

        $choices['default.scss'] = 'default.scss';
        $choices['plain.scss'] = 'plain.scss';

        $this->add_setting_configselect($name, $default, $choices, true);

        $name = 'presetfiles';
        $default = 'preset';
        $options = ['maxfiles' => 20, 'accepted_types' => ['.scss']];

        $this->add_setting_configstoredfile($name, $default, $options);

        $name = 'brandcolor';
        $this->add_setting_configcolourpicker($name, true);

        $name = 'layouttype';
        $this->add_setting_configcheckbox($name, 0);
    }
}
