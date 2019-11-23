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
 * External
 *
 * @package     tool_gnotify
 * @author      Angela Baier, Gregor Eichelberger, Thomas Wedekind
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Class tool_gnotify_external
 *
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_gnotify_external extends external_api {

    /**
     * Acknowledge
     *
     * @param int $id Template instance
     * @throws dml_exception
     */
    public static function acknoledge($id) {
        global $USER, $DB;
        if ($USER) {
            if ($USER->id) {
                if ($DB->get_record('tool_gnotify_tpl_ins', ['id' => $id]) &&
                        !$DB->get_record('tool_gnotify_tpl_ins_ack', ['insid' => $id, 'userid' => $USER->id])) {
                    $dataobject = new stdClass();
                    $dataobject->insid = $id;
                    $dataobject->userid = $USER->id;
                    $DB->insert_record('tool_gnotify_tpl_ins_ack', $dataobject);
                }
            }
        }
    }

    /**
     * Acknowledge parameters
     *
     * @return external_function_parameters
     */
    public static function acknoledge_parameters() {
        return new external_function_parameters(
                array(
                        'id' => new external_value(PARAM_INT, 'id of notificationtemplate')
                )
        );
    }

    /**
     * Acknowledge returns
     *
     * @return |null
     */
    public static function acknoledge_returns() {
        return null;
    }

}