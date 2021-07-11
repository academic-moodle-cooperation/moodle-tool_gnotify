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
 * Edit templates
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

global $DB;

$id = required_param('templateid', PARAM_INT);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/edit.php?templateid=' . $id));

// TODO multilang
$form = new tool_gnotify_edit_form(new moodle_url('edit.php', ['templateid' => $id]));
if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
} else if ($useform = $form->get_data()) {

    $trans = $DB->start_delegated_transaction();

    $updaterecord = new stdClass();
    $updaterecord->id = $useform->langid;
    $updaterecord->content = $useform->content['text'];

    $DB->update_record('tool_gnotify_tpl_lang', $updaterecord);

    preg_match_all('/{{\s*(.*?)\s*}}/', $useform->content['text'], $matches);

    foreach ($matches[1] as $value) {
        if (!$DB->record_exists('tool_gnotify_tpl_var', ['varname' => $value])) {
            $recordvar = new stdClass();
            $recordvar->tplid = $id;
            $recordvar->varname = $value;
            $DB->insert_record('tool_gnotify_tpl_var', $recordvar);
        }
    }

    $DB->commit_delegated_transaction($trans);
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
}

if ($templatelang = $DB->get_record('tool_gnotify_tpl_lang', ['tplid' => $id])) {
    $template = $DB->get_record('tool_gnotify_tpl', ['id' => $id]);
    $formdata['template_name'] = $template->name;
    $formdata['content']['text'] = $templatelang->content;
    $form->set_data($formdata);
}

$PAGE->set_title(get_string('templates', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('templates', 'tool_gnotify'));
echo $form->render();
echo $OUTPUT->footer();