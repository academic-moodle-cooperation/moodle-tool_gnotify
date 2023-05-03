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

admin_externalpage_setup('gnotify_templates');

global $DB;

$id = optional_param('id', null, PARAM_INT);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/edit.php', ['id' => $id]));
$PAGE->set_pagelayout('admin');

require_admin();

$template = null;

if ($id) {
    $template = new \tool_gnotify\template($id);
}

$form = new \tool_gnotify\local\form\template($PAGE->url->out(false), ['persistent' => $template]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
} else if ($data = $form->get_data()) {

    if (empty($data->id)) {
        $template = new \tool_gnotify\template(0, $data);
        $template->create();
    } else {
        $template->from_record($data);
        $template->update();
        $notifications = \tool_gnotify\notification::get_records(['templateid' => $template->get('id')]);
        foreach ($notifications as $notification) {
            $notification->patch($template);
            $notification->update();
        }
    }

    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
}

$PAGE->set_title(get_string('createtemplate', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('createtemplate', 'tool_gnotify'));
echo $form->render();
echo $OUTPUT->footer();
