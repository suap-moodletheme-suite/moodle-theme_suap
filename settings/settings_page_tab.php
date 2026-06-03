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

class settings_page_tab extends admin_settingpage {
    protected $theme_name;

    public function __construct($pagename, $pagetitle) {
        $this->theme_name = 'theme_suap';
        parent::__construct($pagename, get_string($pagetitle, $this->theme_name));

        $this->create_page_settings();
    }

    public function add_setting_configtext($name, $default = '') {
        $this->add(new admin_setting_configtext(
            "{$this->theme_name}/$name",
            get_string($name, $this->theme_name),
            get_string("{$name}_desc", $this->theme_name),
            $default
        ));
    }

    public function add_setting_configtextarea($name, $updatecallback = false, $default = '') {
        $setting = new admin_setting_configtextarea(
            "{$this->theme_name}/$name",
            get_string($name, $this->theme_name),
            get_string("{$name}_desc", $this->theme_name),
            $default
        );

        if ($updatecallback) {
            $setting->set_updatedcallback('theme_reset_all_caches');
        }

        $this->add($setting);
    }

    public function add_setting_confightmleditor($name, $updatecallback = false, $default = '') {
        $setting = new admin_setting_confightmleditor(
            "{$this->theme_name}/$name",
            get_string($name, $this->theme_name),
            get_string("{$name}_desc", $this->theme_name),
            $default
        );

        if ($updatecallback) {
            $setting->set_updatedcallback('theme_reset_all_caches');
        }

        $this->add($setting);
    }

    public function add_setting_configcheckbox($name, $default = 0) {
        $this->add(new admin_setting_configcheckbox(
            "{$this->theme_name}/$name",
            get_string($name, $this->theme_name),
            get_string("{$name}_desc", $this->theme_name),
            $default
        ));
    }

    public function add_setting_configselect($name, $default = 0, $options = [], $updatecallback = false) {
        $setting = new admin_setting_configselect(
            "{$this->theme_name}/$name",
            get_string($name, $this->theme_name),
            get_string("{$name}_desc", $this->theme_name),
            $default,
            $options
        );

        if ($updatecallback) {
            $setting->set_updatedcallback('theme_reset_all_caches');
        }

        $this->add($setting);
    }

    public function add_setting_configcolourpicker($name, $updatecallback = false, $default = '') {
        $setting = new admin_setting_configcolourpicker(
            "{$this->theme_name}/$name",
            get_string($name, $this->theme_name),
            get_string("{$name}_desc", $this->theme_name),
            $default
        );

        if ($updatecallback) {
            $setting->set_updatedcallback('theme_reset_all_caches');
        }

        $this->add($setting);
    }

    public function add_setting_configstoredfile($name, $default = '', $options = [], $itemid = 0) {
        $this->add(new admin_setting_configstoredfile(
            "{$this->theme_name}/$name",
            get_string($name, $this->theme_name),
            get_string("{$name}_desc", $this->theme_name),
            $default,
            $itemid,
            $options
        ));
    }

    protected function create_page_settings() {
    }
}
