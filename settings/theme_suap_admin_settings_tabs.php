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

require_once(__DIR__ . "/frontpage-settings.php");
require_once(__DIR__ . "/advanced-settings.php");
require_once(__DIR__ . "/general-settings.php");


class theme_suap_admin_settings_tabs extends theme_boost_admin_settingspage_tabs
{
    public function __construct() {
        parent::__construct('themesettingsuap', get_string('configtitle', 'theme_suap'));

        $this->add(new general_settings_tab());
        $this->add(new advanced_settings_tab());
        $this->add(new frontpage_settings_tab());
    }
}
