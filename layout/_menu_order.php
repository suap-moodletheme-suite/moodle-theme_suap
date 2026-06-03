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

$_menu_items = &$primarymenu["user"]["items"];

$key_userpreference = array_search(get_string('userpreferences'), array_map(fn($p) => isset($p->title) ? $p->title : "", $_menu_items));
$_userpreference = array_splice($_menu_items, $key_userpreference, 1)[0];

$key_language = array_search(get_string('language'), array_map(fn($p) => isset($p->title) ? $p->title : "", $_menu_items));
$_language = array_splice($_menu_items, $key_language, 1)[0];

$key_preferences = array_search(get_string('preferences'), array_map(fn($p) => isset($p->title) ? $p->title : "", $_menu_items));
$p = array_splice($_menu_items, $key_preferences, 1)[0];


// alterar a ordem dos menus de preferencia de usuario e de idioma
array_unshift($_menu_items, $_userpreference);
array_unshift($_menu_items, $_language);

// adiciona a bandeira no texto do idioma
$langs = \get_string_manager()->get_list_of_translations();
$_submenu_items = &$primarymenu["user"]["submenus"][0]->items;

for ($i = 0; $i < count($_submenu_items); $i++) {
    if ($_submenu_items[$i]['title'] == $langs['en']) :
        $_submenu_items[$i]['text'] .= ' 🇺🇸';
    endif;
    if (array_key_exists('pt_br', $langs) && $langs['pt_br'] == $_submenu_items[$i]['title']) :
        $_submenu_items[$i]['text'] .= ' 🇧🇷';
    endif;
}
