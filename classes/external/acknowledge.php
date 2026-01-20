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
namespace tool_gnotify\external;

use dml_exception;
use tool_gnotify\ack;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

/**
 * Class tool_gnotify_external
 *
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class acknowledge extends external_api {
    /**
     * Acknowledge
     *
     * @param int $id Template instance
     * @throws dml_exception
     */
    public static function execute(int $id) {
        global $USER;

        ['id' => $id] = self::validate_parameters(self::execute_parameters(), ['id' => $id]);
        self::validate_context(\context_user::instance($USER->id));

        if (
            $USER && $USER->id
            && \tool_gnotify\notification::record_exists($id)
            && !(\tool_gnotify\ack::record_exists_select(
                'userid = :userid and notificationid = :id',
                [ 'userid' => $USER->id, 'id' => $id ]
            ))
        ) {
            $record = new \stdClass();
            $record->userid = $USER->id;
            $record->notificationid = $id;
            $ack = new ack(0, $record);
            $ack->create();
        }
    }

    /**
     * Acknowledge parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            [
                        'id' => new external_value(PARAM_INT, 'id of notificationtemplate'),
                ]
        );
    }

    /**
     * Acknowledge returns
     *
     * @return |null
     */
    public static function execute_returns() {
        return null;
    }
}
