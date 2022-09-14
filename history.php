<?php
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
 * Gnotify manage templates
 *
 * @package     tool_gnotify
 * @author      Angela Baier, Gregor Eichelberger, Thomas Wedekind
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/locallib.php');

require_login();

global $DB;

$PAGE->set_context(context_user::instance($USER->id));

$PAGE->set_url(new moodle_url('/admin/tool/gnotify/history.php'));
$PAGE->set_title(get_string('notificationhistory', 'tool_gnotify'));

$user = core_user::get_user($USER->id);
$PAGE->navigation->extend_for_user($user);
$PAGE->navbar->add(get_string('notificationhistory', 'tool_gnotify'), $PAGE->url);
$PAGE->set_pagelayout('standard');

$notifications = [];

$allnotifications = $notification = \tool_gnotify\notification::get_records([], 'fromdate', 'DESC');
foreach ($allnotifications as $notification) {
    $formatoptions = new stdClass();
    $formatoptions->trusted = true;
    $formatoptions->noclean = true;

    $htmlcontent = format_text($notification->get('content'), FORMAT_HTML, $formatoptions);

    $datamodel = $notification->get_data_model();

    $lang = 'lang='.current_language();
    $datamodel->$lang = true;

    $renderer = new tool_gnotify_var_renderer($PAGE, 'web');
    $htmlcontent = $renderer->render_direct($htmlcontent, $datamodel);

    $ack = tool_gnotify\ack::get_record(['notificationid' => $notification->get('id'), 'userid' => $USER->id]);

    $config = json_decode($notification->get('configdata'));

    switch ($config->ntype) {
        case TOOL_GNOTIFY_NOTIFICATION_TYPE_INFO:
            $config->ntype = 'alert-info';
            break;
        case TOOL_GNOTIFY_NOTIFICATION_TYPE_WARN:
            $config->ntype = 'alert-warning';
            break;
        case TOOL_GNOTIFY_NOTIFICATION_TYPE_ERROR:
            $config->ntype = 'alert-danger';
            break;
        default:
            $config->ntype = 'alert-none'; // This is a dummy value.
            break;
    }

    $nbody = [
            'html' => $htmlcontent, 'id' => $notification->get('id'),
            'fromdate' => userdate($notification->get('fromdate')),
            'todate' => userdate($notification->get('todate')),
            'ackdate' => $ack ? userdate($ack->get('timecreated')) : null,
            'ntype' => $config->ntype
    ];

    if ($notification->get('todate') > time()) {
        $notifications['active'][] = $nbody;
    } else {
        $notifications['expired'][] = $nbody;
    }

    $notifications['retentionperiod'] = intval(get_config('tool_gnotify', 'retentionperiod')) / 86400;

}

echo $OUTPUT->header();

if (!$notifications) {
    echo $OUTPUT->notification(get_string('nonotificationhistoryinfo', 'tool_gnotify'));
}

$renderer = $PAGE->get_renderer('core');
echo $renderer->render_from_template('tool_gnotify/history', $notifications);

echo $OUTPUT->footer();