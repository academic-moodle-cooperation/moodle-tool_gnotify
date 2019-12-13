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
 * Create templates
 *
 * @package     tool_gnotify
 * @author      Angela Baier, Gregor Eichelberger, Thomas Wedekind
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');

global $DB;

admin_externalpage_setup('gnotify_templates');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/create.php'));
$PAGE->set_pagelayout('admin');

require_login();
// TODO admin

$cform = new tool_gnotify_create_form();

if ($cform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
} else if ($fromform = $cform->get_data()) {

    $trans = $DB->start_delegated_transaction();

    $record = new stdClass();
    $record->name = $fromform->template_name;
    $id = $DB->insert_record('tool_gnotify_tpl', $record);

    $recordcontent = new stdClass();
    $recordcontent->tplid = $id;
    $recordcontent->lang = 'en';
    $recordcontent->content = $fromform->content['text'];
    // TODO: lang should be not null
    $DB->insert_record('tool_gnotify_tpl_lang', $recordcontent);

    preg_match_all('/{{\s*(.*?)\s*}}/', $fromform->content['text'], $matches);

    foreach ($matches[1] as $value) {
        $recordvar = new stdClass();
        $recordvar->tplid = $id;
        $recordvar->varname = $value;
        $DB->insert_record('tool_gnotify_tpl_var', $recordvar);
    }

    $DB->commit_delegated_transaction($trans);
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
}

$PAGE->set_title(get_string('createtemplate', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('createtemplate', 'tool_gnotify'));
echo $cform->render();
echo $OUTPUT->footer();