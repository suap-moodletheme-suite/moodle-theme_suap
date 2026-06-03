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
 * Controls the drawers general behavior
 * Has the same function of message_popup/notification_popover_controller
 *
 * @package
 * @copyright  2024 IFRN DEAD
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "core_user/repository"], function($, Repository) {
    var init = function() {
        // Seleciona os elementos do DOM
        const contentpart1 = document.querySelector("#content-part1");
        const countcontent = document.querySelector("#counter-content");
        const preferenceName = "visual_preference";

        const preferCounter = document.getElementById("preferCounter");
        const preferCounterTitle = document.getElementById("preferCounterTitle");

        document
            .getElementById("preferCounter")
            .addEventListener("change", function() {
                if (this.checked) {
                    // Se estiver selecionado, adiciona as classes e salva no banco de dados
                    contentpart1.classList.add("content-original");
                    countcontent.classList.add("content-reverse");
                    preferCounterTitle.innerText = "Ativado";

                    // Salvar a preferência
                    Repository.setUserPreference(preferenceName, true);
                } else {
                    contentpart1.classList.remove("content-original");
                    countcontent.classList.remove("content-reverse");
                    preferCounterTitle.innerText = "Desativado";

                    // Salvar a preferência
                    Repository.setUserPreference(preferenceName, false);
                }
            });

        Repository.getUserPreference(preferenceName).then(function(result) {
            // Recupera o valor de user preference salva no banco
            let preferenceCounter = result;

            if (preferenceCounter === "1") {
                preferCounter.checked = true;
                preferCounterTitle.innerText = 'Ativado';
            } else {
                preferCounter.checked = false;
                preferCounterTitle.innerText = 'Desativado';
            }
        });

    };

    return {
        init: init,
    };
});
