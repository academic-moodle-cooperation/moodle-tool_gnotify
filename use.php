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

use core\notification;

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');

require_admin();

$id = optional_param('id', null, PARAM_INT);
$templateid = required_param('templateid', PARAM_INT);

admin_externalpage_setup('gnotify_templates');

global $DB;
$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/use.php', ['id' => $id, 'templateid' => $templateid]));

$template = \tool_gnotify\template::get_record(['id' => $templateid]);
if ($template) {

    if (empty($id)) {
        $instance = \tool_gnotify\notification::get_from_template($template);
    } else {
        $instance = new \tool_gnotify\notification($id);
    }

    $form = new \tool_gnotify\local\form\notification(new moodle_url('use.php',
        ['templateid' => $templateid, 'id' => $id]), ['persistent' => $instance]);

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
    } else if ($data = $form->get_data()) {
        try {
            $data->content = $template->get('content');
            if (empty($data->id)) {
                $persistent = new \tool_gnotify\notification(0, $data);
                $persistent->create();
            } else {
                $instance->from_record($data);
                $instance->update();
            }
        } catch (\Exception $e) {
            notification::error($e->getMessage());
        }

        redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
    }
} else {
    throw new moodle_exception('wrongiderror', 'tool_gnotify');
}

$PAGE->set_title(get_string('templates', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('templates', 'tool_gnotify'));
echo $form->render();
echo $OUTPUT->footer();