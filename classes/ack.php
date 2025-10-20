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
 * Notification acknowledgement persistent
 *
 * @package     tool_gnotify
 * @author      Gregor Eichelberger
 * @copyright   2021 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_gnotify;

/**
 * Class representing an acknowledgement
 *
 * @package     tool_gnotify
 * @author      Gregor Eichelberger
 * @copyright   2021 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ack extends \core\persistent {

    /**
     * Table name for this persistent.
     */
    const TABLE = 'tool_gnotify_acks';

    /**
     * Delete all acknowledgements for a user.
     *
     * @param int $userid
     * @return void
     * @throws \dml_exception
     */
    public static function delete_all_by_user(int $userid): void {
        global $DB;
        $DB->delete_records('tool_gnotify_acks', ['userid' => $userid]);
    }

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'userid' => [
                'type' => PARAM_INT,
            ],
            'notificationid' => [
                'type' => PARAM_INT,
            ],
        ];
    }

}
