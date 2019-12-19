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
 * Gnotify creates instance of template
 *
 * @package     tool_gnotify
 * @author      Angela Baier, Gregor Eichelberger, Thomas Wedekind
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
// TODO Admin.
$id = required_param('templateid', PARAM_INT);
admin_externalpage_setup('gnotify_templates');
global $DB;
$context = context_system::instance();

$template = $DB->get_record('tool_gnotify_tpl', ['id' => $id]);
if ($template) {
    $templatelang = $DB->get_record('tool_gnotify_tpl_lang', ['tplid' => $template->id, 'lang' => 'en']);
    // TODO Multilang.
    $templatevars = $DB->get_fieldset_select('tool_gnotify_tpl_var', 'varname', 'tplid = :templateid ORDER BY id ASC', ['templateid' => $template->id]);
    $templatecontext = array();
    $templatecontext['vars'] = $templatevars;
    $templatecontext['lang'] = $templatelang;
    $form = new tool_gnotify_use_form(new moodle_url('use.php', ['templateid' => $id]), $templatecontext);

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
    } else if ($useform = $form->get_data()) {

        $trans = $DB->start_delegated_transaction();

        $record2 = new stdClass();
        $record2->tplid = $template->id;
        $record2->fromdate = $useform->fromdate;
        $record2->todate = $useform->todate;
        $record2->sticky = $useform->sticky;
        $record2->isvisibleonlogin = $useform->isvisibleonlogin;
        $record2->padding = $useform->padding;
        $record2->dismissable = $useform->dismissable;
        $record2->ntype = $useform->ntype;
        $id = $DB->insert_record('tool_gnotify_tpl_ins', $record2);

        foreach ($templatevars as $v) {
            $recordvar = new stdClass();
            $recordvar->insid = $id;
            $recordvar->varid = $DB->get_field('tool_gnotify_tpl_var', 'id', ['varname' => $v]);
            $recordvar->content = $useform->$v;
            $DB->insert_record('tool_gnotify_tpl_ins_var', $recordvar);
        }

        $DB->commit_delegated_transaction($trans);
        redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
    }

} else {
    print_error('wrongiderror', 'tool_gnotify');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/use.php?id=' . $id));
$PAGE->set_title(get_string('templates', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('templates', 'tool_gnotify'));
echo $form->render();
echo $OUTPUT->footer();