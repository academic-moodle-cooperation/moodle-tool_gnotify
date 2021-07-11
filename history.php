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
global $DB;

if (!isloggedin()) {
    require_login();
}

$PAGE->set_context(context_user::instance($USER->id));

$PAGE->set_url(new moodle_url('/admin/tool/gnotify/history.php'));
$PAGE->set_title(get_string('notificationhistory', 'tool_gnotify'));

$user = core_user::get_user($USER->id);
$PAGE->navigation->extend_for_user($user);
$PAGE->navbar->add(get_string('notificationhistory', 'tool_gnotify'), $PAGE->url);

$sql = "SELECT g.id, l.content, g.sticky FROM {tool_gnotify_tpl_ins} g, {tool_gnotify_tpl_lang} l " .
        "WHERE :time between fromdate AND todate AND l.lang = 'en' AND l.tplid = g.tplid AND EXISTS " .
        "(SELECT 1 FROM {tool_gnotify_tpl_ins_ack} a WHERE g.id=a.insid AND a.userid = :userid)";
$records = $DB->get_records_sql($sql, ['time' => time(), 'userid' => $USER->id]);

$notifications = [];

if ($records) {
    foreach ($records as $record) {
        $formatoptions = new stdClass();
        $formatoptions->trusted = true;
        $formatoptions->noclean = true;
        $htmlcontent = format_text($record->content, FORMAT_HTML, $formatoptions);
        $sql =
                'SELECT var.varname, content from {tool_gnotify_tpl_ins_var} ins, {tool_gnotify_tpl_var} var  WHERE var.id = ins.varid AND ins.insid = :insid';
        $vars = $DB->get_records_sql($sql, ['insid' => $record->id]);
        $renderer = new tool_gnotify_var_renderer($PAGE, 'web');
        $varray = [];
        foreach ($vars as $var) {
            $varray[$var->varname] = $var->content;
        }
        $htmlcontent = $renderer->render_direct($htmlcontent, $varray);

        $notifications['ack'][] = ['html' => $htmlcontent, 'id' => $record->id];
    }
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