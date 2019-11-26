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

$action = optional_param('action', null, PARAM_ALPHANUMEXT);
$tpltodeleteid = optional_param('templateid', null, PARAM_INT);
$instodeleteid = optional_param('insid', null, PARAM_INT);

admin_externalpage_setup('gnotify_templates');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/templates.php'));
$PAGE->set_title(get_string('templates', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('templates', 'tool_gnotify'));

global $DB;

if ($action == "delete" && $tpltodeleteid) {

    $DB->delete_records('tool_gnotify_tpl', ['id' => $tpltodeleteid]);
    $DB->delete_records('tool_gnotify_tpl_var', ['tplid' => $tpltodeleteid]);
    $DB->delete_records('tool_gnotify_tpl_lang', ['tplid' => $tpltodeleteid]);
    // TODO lets get rid of riid
    $riid = $DB->get_records('tool_gnotify_tpl_ins', ['tplid' => $tpltodeleteid]);

    $DB->delete_records('tool_gnotify_tpl_ins', ["tplid" => $tpltodeleteid]);

    foreach ($riid as $x) {
        $DB->delete_records('tool_gnotify_tpl_ins_var', ['insid' => $x->id]);
        $DB->delete_records('tool_gnotify_tpl_ins_ack', ['insid' => $x->id]);
    }
}

if ($action == "delete-ins" && $instodeleteid) {
    $DB->delete_records('tool_gnotify_tpl_ins', ["id" => $instodeleteid]);
    $DB->delete_records('tool_gnotify_tpl_ins_var', ['insid' => $instodeleteid]);
    $DB->delete_records('tool_gnotify_tpl_ins_ack', ['insid' => $instodeleteid]);
}

$templates = $DB->get_recordset('tool_gnotify_tpl', null);

if ($templates->valid()) {
    $templatestablecontext = array("templates" => $templates);
}
$templatestablecontext["wwwroot"] = $CFG->wwwroot;

// Template ins
// TODO use moodle functions
$instemplates =
        $DB->get_records_sql('SELECT B.id, A.name, B.fromdate, B.todate FROM {tool_gnotify_tpl} A RIGHT JOIN {tool_gnotify_tpl_ins} B ON A.id=B.tplid');

$readytpl = array();
foreach ($instemplates as $value) {
    $fromdate = (new DateTime("@$value->fromdate", core_date::get_user_timezone_object()))->format('Y-m-d H:i:s');
    $todate = (new DateTime("@$value->todate", core_date::get_user_timezone_object()))->format('Y-m-d H:i:s');
    array_push($readytpl, ['id' => $value->id, 'name' => $value->name, 'fromdate' => $fromdate, 'todate' => $todate]);
}

$templatestablecontext['instemplates'] = $readytpl;

$renderer = $PAGE->get_renderer('core');
echo $renderer->render_from_template('tool_gnotify/templates_table', $templatestablecontext);

echo $OUTPUT->footer();