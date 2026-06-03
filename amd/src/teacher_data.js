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

define(["core/str"], function(str) {
    let url = '';
    let course = document.querySelector('#course-infos');
    let courseid = course.getAttribute('data-courseid');
    let teacherDataContainers = document.querySelectorAll('[data-render="teacher-numbers"]');

    /**
     * Carrega os dados dos professores a partir da API
     */
    async function loadTeacherData() {
        if (!url) {
            // Retorna silenciosamente para cumprir a regra no-console
            return;
        }

        try {
            const response = await fetch(`${url}?courseid=${courseid}`);

            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status} ${response.statusText}`);
            }

            const teacherData = await response.json();
            renderTeacherData(teacherData);

        } catch (error) {
            // Falha silenciosa para cumprir a regra no-console
        }

    }

    /**
     * Renderiza os dados numéricos dos professores no DOM
     *
     * @param {Array} teacherData Lista de objetos com os dados dos professores
     */
    async function renderTeacherData(teacherData) {
        const studentsString = await str.getString('students', 'core');
        const coursesString = await str.getString('courses', 'core');

        teacherData.forEach((teacher, i) => {
            if (teacherDataContainers[i]) {
                teacherDataContainers[i].innerHTML = `
                    <div class="students-number">
                        <i class="fa-solid fa-user-group"></i>
                        <span>${teacher.totalstudents} ${studentsString}</span>
                    </div>
                    <div class="courses-number">
                        <i class="fa-regular fa-file-lines"></i>
                        <span>${teacher.courses.length} ${coursesString}</span>
                    </div>
                `;
            }
        });
    }

    return {
        init: (requestUrl) => {
            url = requestUrl;
            loadTeacherData();
        }
    };
});
