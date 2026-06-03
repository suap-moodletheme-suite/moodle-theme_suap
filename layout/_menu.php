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

$_logoutlink = "#";
if (isset($primarymenu["user"]["items"])) {
    // submenu com as configurações de preferencia do usuario
    include('_submenu_userpreference.php');

    // alteração na ordenação do menu
    include('_menu_order.php');
    $key_logout = array_search(get_string('logout'), array_map(fn($p) => isset($p->title) ? $p->title : "", $_menu_items));
    $_logoutlink = array_splice($_menu_items, $key_logout, 1)[0];
}
