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

define(["core_user/repository", "core/pubsub", "core_message/message_drawer_events"], function(RepositoryUser, PubSub, MessageDrawerEvents) {
    const body = document.body;
    const breakpointSM = 768;
    let backdrop = document.querySelector('[data-region="suap-backdrop"]');

    let drawers = document.querySelectorAll('.drawer-content');
    let drawersToggler = document.querySelectorAll('.drawer-toggler');
    let closeButtons = document.querySelectorAll('.drawer-close');

    const counterToggler = document.querySelector(".counter-toggler");

    const searchForm = document.querySelector(".searchform-js");

    const preferenceCounter = 'theme_suap_counter_close';
    const preferenceIndexDrawer = 'theme_suap_index_drawer_open';
    const preferenceBlocksDrawer = 'theme_suap_blocks_drawer_open';

    const setDrawerPreference = (drawerId) => {
        if (drawerId === 'drawer-index') {
            RepositoryUser.setUserPreference(preferenceIndexDrawer, true);
            RepositoryUser.setUserPreference(preferenceBlocksDrawer, false);
            return;
        }
        if (drawerId === 'drawer-blocks') {
            RepositoryUser.setUserPreference(preferenceBlocksDrawer, true);
            RepositoryUser.setUserPreference(preferenceIndexDrawer, false);
            return;
        }

        clearDrawerPreference();
    };

    const clearDrawerPreference = () => {
        RepositoryUser.setUserPreference(preferenceIndexDrawer, false);
        RepositoryUser.setUserPreference(preferenceBlocksDrawer, false);
    };

    // Abre gaveta clicada e fecha outras que estiverem abertas
    var openDrawer = (toggler) => {
        toggler.addEventListener("click", () => {
            let drawerId = toggler.getAttribute("data-drawer");
            let drawer = document.getElementById(drawerId);
            // Garantir que o drawer e a lista de drawers existem no momento do clique.
            let currentDrawers = document.querySelectorAll('.drawer-content');

            if (!drawer) {
                return;
            }

            if (drawer.classList.contains('active-drawer')) { // Close drawer
                drawer.classList.remove('active-drawer');
                toggler.classList.remove('active-toggler');

                if (window.innerWidth <= breakpointSM) {
                    body.classList.remove('drawer-open-mobile');
                } else {
                    body.classList.remove('drawer-open');
                }

                clearDrawerPreference();
            } else { // Open drawer
                if (currentDrawers && currentDrawers.length) {
                    closeAllDrawers(currentDrawers);
                }
                setDrawerPreference(drawerId);
                drawer.classList.add('active-drawer');
                toggler.classList.add('active-toggler');

                if (window.innerWidth <= breakpointSM) {
                    body.classList.remove('counter-open-mobile');
                    body.classList.add('drawer-open-mobile');
                } else {
                    body.classList.add('drawer-open');
                }
            }
        });
    };

    var closeAllDrawers = function(drawers) {
        if (window.innerWidth <= breakpointSM) {
            body.classList.remove("drawer-open-mobile");
        } else {
            body.classList.remove("drawer-open");
        }
        drawers.forEach((drawer) => {
            drawer.classList.remove("active-drawer");
        });
        drawersToggler.forEach((toggler) => {
            toggler.classList.remove("active-toggler");
        });
    };

    /**
     * Abre automaticamente a gaveta de blocos na primeira página de um módulo específico.
     * @param {string} path Substring do pathname para identificar o módulo (ex: 'attempt.php')
     * @param {string} pageParam Nome do parâmetro que indica a página (ex: 'page', 'chapterid')
     */
    var openBlocksOnFirstPage = function(path, pageParam) {
        if (!window.location.pathname.includes(path)) {
            return;
        }

        const params = new URLSearchParams(window.location.search);
        const isFirstPage = !params.has(pageParam);

        if (!isFirstPage) {
            return;
        }

        const blocksDrawer = document.getElementById('drawer-blocks');
        const blocksToggler = document.querySelector('[data-drawer="drawer-blocks"]');

        if (blocksDrawer && blocksToggler) {
            closeAllDrawers(drawers);

            blocksDrawer.classList.add('active-drawer');
            blocksToggler.classList.add('active-toggler');

            if (window.innerWidth <= breakpointSM) {
                body.classList.add('drawer-open-mobile');
            } else {
                body.classList.add('drawer-open');
            }

            setDrawerPreference('drawer-blocks');
        }
    };


    var init = function() {

        if (searchForm) {
            const searchSubmit = searchForm.querySelector('.search-js');
            const searchInput = searchForm.querySelector('.input-js');
            searchSubmit.addEventListener('click', () => {
                if (window.innerWidth <= breakpointSM &&
                    !body.classList.contains('counter-open-mobile')) {
                    closeAllDrawers(drawers);
                }

                if (window.innerWidth <= breakpointSM) {
                    body.classList.add('counter-open-mobile');
                } else {
                    body.classList.remove('counter-close');
                }

                searchInput.focus();
            });
        }

        if (window.innerWidth <= breakpointSM) {
            closeAllDrawers(drawers);
        }

        // Caso o usuário diminua largura e esteja com counter e drawer abertas
        window.addEventListener('resize', function() {
            if (window.innerWidth <= breakpointSM &&
            !body.classList.contains('counter-close') &&
            body.classList.contains('drawer-open')) {
                body.classList.add('counter-close');
            }
        });

        // Ao clicar no backdrop fecha counter ou drawers
        backdrop.addEventListener('click', function(e) {
            if (e.target === e.currentTarget) {
                body.classList.remove('counter-open-mobile');
                if (body.classList.contains('drawer-open-mobile')) {
                    closeAllDrawers(drawers);
                }
            }
        });

        counterToggler.addEventListener('click', () => {
            if (window.innerWidth <= breakpointSM) {
                body.classList.toggle('counter-open-mobile');
                if (body.classList.contains('counter-open-mobile')) {
                    closeAllDrawers(drawers);
                }
            } else {
                body.classList.toggle('counter-close');
            }

            // Salva a preferência no desktop
            if (body.classList.contains('counter-close')) {
                RepositoryUser.setUserPreference(preferenceCounter, true);
            } else {
                RepositoryUser.setUserPreference(preferenceCounter, false);
            }

            if (searchForm) {
                const searchInput = searchForm.querySelector('.input-js');
                searchInput.value = "";
            }
        });

        drawersToggler.forEach((toggler) => {
            openDrawer(toggler);
        });

        closeButtons.forEach((button) => {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                closeAllDrawers(drawers);
                clearDrawerPreference();
            });
        });

        // Para questionário
        openBlocksOnFirstPage('/mod/quiz/attempt.php', 'page');

        // Para livro
        openBlocksOnFirstPage('/mod/book/view.php', 'chapterid');

        // Listen for Moodle's message drawer events to open the custom drawer
        const openMessageDrawer = () => {
            const messageToggler = document.querySelector('[data-drawer="drawer-messages"]');
            if (messageToggler && !messageToggler.classList.contains('active-toggler')) {
                messageToggler.click();
            }
        };

        PubSub.subscribe(MessageDrawerEvents.SHOW, openMessageDrawer);
        PubSub.subscribe(MessageDrawerEvents.SHOW_CONVERSATION, openMessageDrawer);
        PubSub.subscribe(MessageDrawerEvents.SHOW_SETTINGS, openMessageDrawer);
        PubSub.subscribe(MessageDrawerEvents.CREATE_CONVERSATION_WITH_USER, openMessageDrawer);

    };

    return {
        init: init,
    };
});
