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

import Ajax from 'core/ajax';
import * as Templates from "core/templates";
import Notification from "core/notification";

export const init = async(contextid, pagelayout) => {
    const request = {
        methodname: 'tool_gnotify_get_notifications',
        args: {contextid, pagelayout}
    };

    try {
        const gnotify = await Ajax.call([request])[0];
        const {html, js} = await Templates.renderForPromise(gnotify.template,
            {padding: gnotify.padding, notifications: gnotify.notifications});
        Templates.prependNodeContents('#page', html, js);
    } catch (error) {
        Notification.exception(error);
    }
};

export const acknowledge = (id) => {
    document.getElementById(id + '-gnotify-wrapper').hidden = true;
    const request = {
        methodname: 'tool_gnotify_acknowledge_notification',
        args: {
            id: id,
        }
    };

    Ajax.call([request])[0].done();
};

export default {
    init,
    acknowledge,
};