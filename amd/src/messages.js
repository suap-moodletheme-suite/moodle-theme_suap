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
 * Controls the message drawer
 * Has the same function of message_popup/notification_popover_controller
 *
 * @package
 * @copyright  2024 IFRN DEAD
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification', 'core/templates', 'core_message/message_repository',
    'core_message/message_drawer_view_conversation_constants',
    'core_message/message_drawer_view_overview', 'core_message/message_drawer_view_overview_section',
    'core/pending', 'core/str'
],
function($, Ajax, Notification, Templates, Repository, Constants, ViewOverview, ViewOverviewSection, Pending, Str) {

    const viewOverview = document.querySelector('[data-region="body-container"] [data-region="view-overview"]');

    const userid = viewOverview.getAttribute('data-user-id');
    const toggleMessages = document.querySelector('[data-drawer="drawer-messages"]');

    const viewAll = viewOverview.querySelectorAll('[data-region="view-overview-all-messages"]');

    const listItems = viewAll[1].querySelector('[data-region="content-container"]');
    const listPlaceholder = viewAll[1].querySelector('[data-region="placeholder-container"]');

    var CONVERSATION_TYPES = Constants.CONVERSATION_TYPES;

    var TEMPLATES = {
        CONVERSATIONS_LIST: 'core_message/message_drawer_conversations_list',
        CONVERSATIONS_LIST_ITEMS_PLACEHOLDER: 'core_message/message_drawer_conversations_list_items_placeholder'
    };

    var renderUnreadCount = function() {
        const id = parseInt(userid, 10);
        const count = toggleMessages.querySelector('[data-region="count-container"]');
        var args = {
            useridto: id,
        };
        Repository.countUnreadConversations(args).then((result) => {
            count.innerText = result;
            if (result > 0) {
                count.classList.remove('hidden');
            }
        });

        // TODO: Move this block out of renderUnreadCount.
        let viewFavourites = document.querySelectorAll('[data-region="view-overview-favourites"]');
        let viewGroup = document.querySelectorAll('[data-region="view-overview-group-messages"]');
        let viewMessages = document.querySelectorAll('[data-region="view-overview-messages"]');

        keepOpenView(viewFavourites);
        keepOpenView(viewGroup);
        keepOpenView(viewMessages);
    };

    var keepOpenView = function(viewOverview) {
        let lazyView = viewOverview[1].querySelector('[data-region="lazy-load-list"]');
        let clickCount = 0;

        viewOverview[0].addEventListener('click', () => {
            if (clickCount > 0) {
                viewOverview[1].classList.remove('expanded');
                lazyView.classList.remove('show');
            }
            clickCount++;
        });
    };

    var getConversation = function() {
        var originalButton = document.getElementById('message-user-button');
        var customButton = document.getElementById('suap-message-user-button');
        if (originalButton && customButton) {
            customButton.addEventListener('click', function(e) {
                e.preventDefault();
                originalButton.click(); // Simulate a click on Moodle's native message button
            });
        }
    };


    /**
     * Render the messages in the overview page.
     *
     * @param {Array} conversations List of conversations to render.
     * @param {Number} userId Logged in user id.
     * @return {Object} jQuery promise.
     */
    var render = function(conversations, userId) {

        // Helper to format the last message for rendering.
        // Returns a promise which resolves to either a string, or null
        // (such as in the event of an empty personal space).
        var pending = new Pending();

        var formatMessagePreview = async function(lastMessage) {
            if (!lastMessage) {
                return null;
            }
            // Check the message html for a src attribute, indicative of media.
            // Replace <img with <noimg to stop browsers pre-fetching the image as part of tmp element creation.
            var tmpElement = document.createElement("element");
            tmpElement.innerHTML = lastMessage.text.replace(/<img /g, '<noimg ');
            var isMedia = tmpElement.querySelector("[src]") || false;

            if (!isMedia) {
                // Try to get the text value of the content.
                // If that's not possible, we'll report it under the catch-all 'other media'.
                var messagePreview = $(lastMessage.text).text();
                if (messagePreview) {
                    // The text value of the message must have no html/script tags.
                    if (messagePreview.indexOf('<') == -1) {
                        return messagePreview;
                    }
                }
            }

            // As a fallback, report unknowns as 'other media' type content.
            var pix = 'i/messagecontentmultimediageneral';
            var label = 'messagecontentmultimediageneral';

            if (lastMessage.text.includes('<img')) {
                pix = 'i/messagecontentimage';
                label = 'messagecontentimage';
            } else if (lastMessage.text.includes('<video')) {
                pix = 'i/messagecontentvideo';
                label = 'messagecontentvideo';
            } else if (lastMessage.text.includes('<audio')) {
                pix = 'i/messagecontentaudio';
                label = 'messagecontentaudio';
            }

            try {
                var labelString = await Str.get_string(label, 'core_message');
                var icon = await Templates.renderPix(pix, 'core', labelString);
                return icon + ' ' + labelString;
            } catch (error) {
                Notification.exception(error);
                return null;
            }
        };

        var mapPromises = conversations.map(function(conversation) {

            var lastMessage = conversation.messages.length ? conversation.messages[conversation.messages.length - 1] : null;
            // Console.log(lastMessage);

            return formatMessagePreview(lastMessage)
                .then(function(messagePreview) {
                    var formattedConversation = {
                        id: conversation.id,
                        imageurl: conversation.imageurl,
                        name: conversation.name,
                        subname: conversation.subname,
                        unreadcount: conversation.unreadcount,
                        ismuted: conversation.ismuted,
                        lastmessagedate: lastMessage ? lastMessage.timecreated : null,
                        sentfromcurrentuser: lastMessage ? lastMessage.useridfrom == userId : null,
                        lastmessage: messagePreview
                    };

                    var otherUser = null;
                    if (conversation.type == CONVERSATION_TYPES.SELF) {
                        // Self-conversations have only one member.
                        otherUser = conversation.members[0];
                    } else if (conversation.type == CONVERSATION_TYPES.PRIVATE) {
                        // For private conversations, remove the current userId from the members to get the other user.
                        otherUser = conversation.members.reduce(function(carry, member) {
                            if (!carry && member.id != userId) {
                                carry = member;
                            }
                            return carry;
                        }, null);
                    }

                    if (otherUser !== null) {
                        formattedConversation.userid = otherUser.id;
                        formattedConversation.showonlinestatus = otherUser.showonlinestatus;
                        formattedConversation.isonline = otherUser.isonline;
                        formattedConversation.isblocked = otherUser.isblocked;
                    }

                    if (conversation.type == CONVERSATION_TYPES.PUBLIC) {
                        formattedConversation.lastsendername = conversation.members.reduce(function(carry, member) {
                            if (!carry && lastMessage && member.id == lastMessage.useridfrom) {
                                carry = member.fullname;
                            }
                            return carry;
                        }, null);
                    }

                    return formattedConversation;
                }).catch(Notification.exception);
        });

        return Promise.all(mapPromises)
            .then(function(formattedConversations) {
                formattedConversations.forEach(function(conversation) {
                    if (new Date().toDateString() == new Date(conversation.lastmessagedate * 1000).toDateString()) {
                        conversation.istoday = true;
                    }
                });

                return Templates.render(TEMPLATES.CONVERSATIONS_LIST, {conversations: formattedConversations});
            }).then(function(html, js) {
                pending.resolve();
                return $.Deferred().resolve(html, js);
            }).catch(function(error) {
                pending.resolve();
                Notification.exception(error);
            });
    };

    var init = function() {
        // EventListener botões grupos de conversas
        viewAll[0].addEventListener('click', () => {

            Repository.getConversations(2, null, null, null, null, true)
            .then(function(conversations) {

                render(conversations.conversations, userid)
                .then(function(html) {
                    // ListItens.append(html);
                    listItems.innerHTML = html;
                    return html;
                })
                .catch(Notification.exception);

            });

            listItems.classList.remove('hidden');
            listPlaceholder.classList.add('hidden');
        });
    };

    return {
        init: init,
        renderUnreadCount: renderUnreadCount,
        getConversation: getConversation
    };
});