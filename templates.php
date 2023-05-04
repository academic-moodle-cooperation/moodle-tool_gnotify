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
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('gnotify_templates');

$action = optional_param('action', null, PARAM_ALPHANUMEXT);
$templateid = optional_param('templateid', null, PARAM_INT);
$notificationid = optional_param('notificationid', null, PARAM_INT);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/templates.php'));
$PAGE->set_title(get_string('managegnotify', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('gnotify', 'tool_gnotify'));

global $DB;

if ($action == "delete") {
    if (empty($templateid) && $notificationid) {
        $DB->delete_records('tool_gnotify_acks', ["notificationid" => $notificationid]);
        $DB->delete_records('tool_gnotify_notifications', ["id" => $notificationid]);
    } else if ($templateid) {
        $DB->delete_records('tool_gnotify_acks', ["notificationid" => $notificationid]);
        $DB->delete_records('tool_gnotify_notifications', ["templateid" => $templateid]);
        $DB->delete_records('tool_gnotify_templates', ["id" => $templateid]);
    }
}

$renderer = $PAGE->get_renderer('core');

$templates = \tool_gnotify\template::get_records();

$templatecontext = array();

foreach ($templates as $template) {
    $templatecontext['templates'][] = $template->export_for_template($renderer);
}

if (empty($templates)) {
    $templatecontext = array("templates" => $templates);
}

$templatecontext["wwwroot"] = $CFG->wwwroot;

$activenotifications = \tool_gnotify\notification::get_records_select('todate >= :expiretime', ['expiretime' => time()], 'fromdate DESC');

$readytpl = array();
foreach ($activenotifications as $value) {
    $fromdate = userdate($value->get('fromdate'));
    $todate = userdate($value->get('todate'));
    $base = $value->get_template();

    $readytpl[] = [
            'id' => $value->get('id'),
            'name' => $base->get('name'),
            'fromdate' => $fromdate, 'todate' => $todate,
            'ack' => \tool_gnotify\ack::count_records(['notificationid' => $value->get('id')]),
            'tplid' => $value->get('templateid')];
}

$templatecontext['activenotifications'] = $readytpl;

$expirednotifications = \tool_gnotify\notification::get_records_select('todate < :expiretime', ['expiretime' => time()], 'fromdate DESC');

$expiredtpl = array();
foreach ($expirednotifications as $value) {
    $fromdate = userdate($value->get('fromdate'));
    $todate = userdate($value->get('todate'));
    $base = $value->get_template();

    $expiredtpl[] = [
            'id' => $value->get('id'),
            'name' => $base->get('name'),
            'fromdate' => $fromdate, 'todate' => $todate,
            'ack' => \tool_gnotify\ack::count_records(['notificationid' => $value->get('id')]),
            'tplid' => $value->get('templateid')];
}

$templatecontext['expirednotifications'] = $expiredtpl;

echo $renderer->render_from_template('tool_gnotify/templates_table', $templatecontext);

echo $OUTPUT->footer();
