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
 * Controls the notification drawer
 * Has the same function of message_popup/notification_popover_controller
 *
 * @package
 * @copyright  2024 IFRN DEAD
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {

    /**
     *
     */
    function breadcrumbsMobile() {
        const ol = document.querySelector('.breadcrumb');
        // 1) Não faz nada se não houver breadcrumb ou já existir dropdown
        if (!ol || ol.querySelector('.breadcrumb-item.dropdown')) {
 return;
}

        const items = Array.from(ol.querySelectorAll('.breadcrumb-item'));
        if (items.length < 3) {
 return;
} // Precisa ter pelo menos 3 itens

        // 2) Itens que serão “colapsados”
        const hiddenItems = items.slice(1, -1);

        // 3) Cria o <li class="breadcrumb-item dropdown">
        const dropdownLi = document.createElement('li');
        dropdownLi.classList.add('breadcrumb-item', 'dropdown');

        // 4) Cria o botão toggle
        const toggle = document.createElement('button');
        toggle.classList.add('btn', 'p-0');
        toggle.type = 'button';
        toggle.id = 'breadcrumbDropdown';
        toggle.setAttribute('data-toggle', 'dropdown');
        toggle.setAttribute('aria-haspopup', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.textContent = '•••';
        dropdownLi.appendChild(toggle);

        // 5) Cria o <div class="dropdown-menu">
        const menu = document.createElement('div');
        menu.classList.add('dropdown-menu');
        menu.setAttribute('aria-labelledby', 'breadcrumbDropdown');

        // 6) Clona cada link oculto para dentro do dropdown
        hiddenItems.forEach(li => {
            const link = li.querySelector('a')?.cloneNode(true) || document.createElement('span');
            link.classList.add('dropdown-item');
            if (link.tagName === 'SPAN') {
 link.textContent = li.textContent;
}
            menu.appendChild(link);
        });
        dropdownLi.appendChild(menu);

        // 7) Insere o dropdown antes do último item
        ol.insertBefore(dropdownLi, items[items.length - 1]);

    }

    return {
        init: () => {
            breadcrumbsMobile();
        }
    };

});
