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

require_login();

global $DB;

$PAGE->set_context(context_user::instance($USER->id));

$PAGE->set_url(new moodle_url('/admin/tool/gnotify/history.php'));
$PAGE->set_title(get_string('notificationhistory', 'tool_gnotify'));

$user = core_user::get_user($USER->id);
$PAGE->navigation->extend_for_user($user);
$PAGE->navbar->add(get_string('notificationhistory', 'tool_gnotify'), $PAGE->url);


$acks = \tool_gnotify\ack::get_records(['userid' => $USER->id]);

$notifications = [];

foreach ($acks as $ack) {
    $notification = \tool_gnotify\notification::get_record(['id' => $ack->get('notificationid')]);

    $formatoptions = new stdClass();
    $formatoptions->trusted = true;
    $formatoptions->noclean = true;

    $htmlcontent = format_text($notification->get('content'), FORMAT_HTML, $formatoptions);

    $datamodel = $notification->get_data_model();
    $lang = 'lang='.current_language();
    $datamodel->$lang = true;

    $renderer = new tool_gnotify_var_renderer($PAGE, 'web');
    $htmlcontent = $renderer->render_direct($htmlcontent, $datamodel);

    $notifications['ack'][] = ['html' => $htmlcontent, 'id' => $notification->get('id')];
}

echo $OUTPUT->header();
if ($notifications) {
    echo $OUTPUT->heading(get_string('acknotifications', 'tool_gnotify'));
} else {
    echo $OUTPUT->heading(get_string('noacknotifications', 'tool_gnotify'));
    echo $OUTPUT->notification(get_string('noacknotificationsinfo', 'tool_gnotify'));
}

$renderer = $PAGE->get_renderer('core');
echo $renderer->render_from_template('tool_gnotify/history', $notifications);
echo $OUTPUT->footer();