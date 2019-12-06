<?php
// This file is part of tool_gnotify for Moodle - http://moodle.org/
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
 * deletes all notification usages older than one week
 *
 * @package       tool_gnotify
 * @subpackage    gnotify
 * @author        Thomas Wedekind <Thomas.Wedekind@univie.ac.at>
 * @copyright     2019 University of Vienna Computer Center
 * @since         Moodle 3.8+
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_gnotify\task;

defined('MOODLE_INTERNAL') || die();

/**
 * @author        Thomas Wedekind <Thomas.Wedekind@univie.ac.at>
 * @copyright     2019 University of Vienna Computer Center
 * @since         Moodle 3.8+
 */
class delete_completed_task extends \core\task\scheduled_task {
    /**
     * {@inheritDoc}
     * @see \core\task\scheduled_task::get_name()
     */
    public function get_name() {
        // Shown in admin screens.
        return get_string('deletecompletedtaskname', 'tool_gnotify');
    }
    /**
     * {@inheritDoc}
     * @see \core\task\task_base::execute()
     */
    public function execute() {
        global $DB;
        // Remove all template usages older than 1 week.
        $timenow = time();
        $sql = "SELECT id
              FROM {tool_gnotify_tpl_ins}
             WHERE todate < :expiretime";
        $params = array('expiretime' => (int) $timenow);

        // First we get the different IDs.
        $ids = $DB->get_fieldset_sql($sql, $params);

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $DB->delete_records('tool_gnotify_tpl_ins', ["id" => $id]);
                $DB->delete_records('tool_gnotify_tpl_ins_var', ['insid' => $id]);
                $DB->delete_records('tool_gnotify_tpl_ins_ack', ['insid' => $id]);
            }
        }
    }
}