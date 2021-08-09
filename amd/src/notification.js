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
import Log from 'core/log';
import Pending from "core/pending";

export const init = async(uid) => {
    let gnotify = document.querySelector(`#${uid}`);
    let context = JSON.parse(gnotify.dataset.gnotify);

    const pendingPromise = new Pending('gnotfiy-render');
    const Templates = await import('core/templates');
    Templates.renderForPromise('tool_gnotify/notifications', context)
        .then(({html, js=''}) => {
            Templates.prependNodeContents('#page', html, js);
        })
        .then(pendingPromise.resolve)
        .fail(({ex}) => {
            Log.error(ex.message);
        });
};

export const acknowledge = (id) => {
    let notification = document.getElementById(id + '-gnotify-wrapper');
    notification.hidden = true;
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