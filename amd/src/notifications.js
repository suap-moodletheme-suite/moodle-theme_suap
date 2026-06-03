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

define(['jquery', 'core/templates', 'core/notification', 'message_popup/notification_repository'],
    function($, Templates, Notification, NotificationRepository) {
    const LIMIT_NOTIFICATION = 20;

    var displayException = Notification.exception;

    let notificationToggler = document.querySelector('[data-drawer="drawer-notifications"]');
    let countContainer = notificationToggler.querySelector('[data-region="count-container"]');

    let notificationContainer = document.querySelector('#drawer-notifications');
    const userid = notificationContainer.getAttribute('data-userid');
    const markAllReadButton = notificationContainer.querySelector('[data-action="mark-all-read"]');
    let loadingIcon = document.querySelector('[data-region="loading-icon-container"]');

    // Api de notificações
    /**
     *
     * @param offset
     * @param initial
     * @param isFetching
     */
    function getNotifications(offset, initial = true, isFetching = false) {
        const limit = LIMIT_NOTIFICATION;

        return new Promise((resolve, reject) => {
            NotificationRepository.query({
                useridto: userid,
                newestfirst: true,
                limit: limit,
                offset: offset
            }).done(function(data) {
                renderNotifications(data, initial);
                if (data.notifications.length < limit) {
                    resolve(true);
                }

            }).fail(function(error) {
                console.error(error);
                reject(error);
            });
        });
    }

    /**
     *
     */
    function getUnreadCount() {
        NotificationRepository.countUnread({
            useridto: userid,
        }).done(function(data) {
            if (data) {
                countContainer.innerHTML = data;
                countContainer.classList.remove('hidden');
            } else {
                countContainer.classList.add('hidden');
            }
        }).fail(function(error) {
            console.log(error);
        });
    }

    /**
     *
     * @param id
     */
    function setReadOne(id) {
        NotificationRepository.markAsRead(id)
        .then(() => {
            getUnreadCount();
            getNotifications(0);
        });
    }

    /**
     *
     */
    function setReadAll() {
        NotificationRepository.markAllAsRead({
            useridto: userid,
        }).then(function(response) {
            getNotifications(0);
            getUnreadCount();
        });
    }

    /**
     *
     * @param data
     * @param initial
     */
    function renderNotifications(data, initial = true) {
        let allMessages = notificationContainer.querySelector("[data-region='notification-list']");

        if (initial) {
            allMessages.innerHTML = '';
        }

        const newCount = data.notifications.length;
        const alreadyHasItems = allMessages.children.length > 0;

        // Se não vier nada novo...
        if (newCount === 0) {
            // ...e já tinha itens, não faz nada
            if (alreadyHasItems) {
                return;
            }
            // ...e não tinha itens, mostra “sem notificações”
            Templates.renderForPromise('theme_suap/notification_list', {notifications: [], notloading: true})
                .then(({html, js}) => {
                    Templates.appendNodeContents(allMessages, html, js);
                }).catch((error) => displayException(error));
            return;
        }


        data.notloading = true;
        Templates.renderForPromise('theme_suap/notification_list', data)
        .then(({html, js}) => {
            Templates.appendNodeContents(allMessages, html, js);

            checkNotification(data, allMessages);

        }).catch((error) => displayException(error));
    }

    /**
     *
     * @param data
     * @param allMessages
     */
    function checkNotification(data, allMessages) {
        let notificationsItens = document.querySelectorAll('[data-region="notification-shortened"]');
        let fullMessage = document.querySelector('[data-region="notification-full"]');
        drawerHeader = notificationContainer.querySelector('[data-region="drawer-header"]');

        // Open full notification message
        notificationsItens.forEach(notification => {
            notification.addEventListener('click', () => {

                fullMessage.classList.remove('hidden');
                fullMessage.innerHTML = '';
                let notificationID = parseInt(notification.getAttribute("data-id"), 10);

                setReadOne(notificationID);

                drawerHeader.classList.add('open-message');
                returnToList(drawerHeader, fullMessage, allMessages);

                data.notifications.find((notificationData) => {
                    if (notificationData.id === notificationID) {
                        let openData = {
                            "shortenedsubject": notificationData.shortenedsubject,
                            "timecreatedpretty": notificationData.timecreatedpretty,
                            "fullmessagehtml": notificationData.fullmessagehtml,
                            "contexturlname": notificationData.contexturlname,
                            "contexturl": notificationData.contexturl
                                ? notificationData.contexturl.replace(/\\\//g, '/').replace(/&amp;/g, '&')
                                : '',
                        };

                        Templates.renderForPromise('theme_suap/notification_full', openData)
                        .then(({html, js}) => {
                            Templates.appendNodeContents(fullMessage, html, js);
                            allMessages.classList.add('hidden');

                        }).catch((error) => displayException(error));
                    }
                });

            });
        });
    }

    /**
     *
     * @param drawerHeader
     * @param fullMessage
     * @param allMessages
     */
    function returnToList(drawerHeader, fullMessage, allMessages) {
        const returnButton = drawerHeader.querySelector('[data-action="return-list"]');
        returnButton.addEventListener('click', () => {
            fullMessage.classList.add('hidden');
            drawerHeader.classList.remove('open-message');
            allMessages.classList.remove('hidden');
        });
    }

    return {
        init: function() {
            allMessages = notificationContainer.querySelector("[data-region='notification-list']");
            let scrollNotifications = notificationContainer.querySelector('[data-region="notification-scroll"]');
            let fullMessage = document.querySelector('[data-region="notification-full"]');

            getUnreadCount();
            notificationToggler.addEventListener('click', () => {
                if (notificationToggler.classList.contains('active-toggler')) {
                    getNotifications(0);
                    getUnreadCount();
                    allMessages.classList.remove('hidden');
                    fullMessage.classList.add('hidden');

                    let offset = LIMIT_NOTIFICATION;

                    let lastItems = false;
                    let throttleTimeout = null;

                    scrollNotifications.addEventListener('scroll', () => {

                        if (throttleTimeout) {
                            clearTimeout(throttleTimeout);
                        }

                        throttleTimeout = setTimeout(() => {
                            let scrollTop = scrollNotifications.scrollTop;
                            let scrollHeight = scrollNotifications.scrollHeight;
                            let clientHeight = scrollNotifications.clientHeight;

                            if (!lastItems && (scrollTop + clientHeight >= scrollHeight - 50)) {

                                loadingIcon.classList.remove('hidden');

                                getNotifications(offset, false).then(result => {
                                    lastItems = result;
                                    loadingIcon.classList.add('hidden');
                                });
                                offset += LIMIT_NOTIFICATION;
                            }
                        }, 200);
                    });

                }
            });

            markAllReadButton.addEventListener('click', (event) => {
                event.preventDefault();
                setReadAll();
            });

        }
    };

});
