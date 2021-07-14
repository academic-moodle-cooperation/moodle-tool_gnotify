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
 * Notification persistent
 *
 * @package     tool_gnotify
 * @author      Gregor Eichelberger
 * @copyright   2021 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_gnotify;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing a notification
 *
 * @package     tool_gnotify
 * @author      Gregor Eichelberger
 * @copyright   2021 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification extends \core\persistent {

    /**
     * Table name for this persistent.
     */
    const TABLE = 'tool_gnotify_notifications';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'templateid' => [
                'type' => PARAM_INT,
                'description' => 'Template',
            ],
            'content' => [
                'type' => PARAM_TEXT,
            ],
            'fromdate' => [
                'type' => PARAM_INT,
                'description' => 'Start data',
            ],
            'todate' => [
                'type' => PARAM_INT,
                'description' => 'End data',
            ],
            'visibleonlogin' => [
                'type' => PARAM_INT,
                'description' => 'Visible on login pagge',
            ],
            'configdata' => [
                'type' => PARAM_RAW,
            ],
            'datamodel' => [
                'type' => PARAM_RAW,
            ],
        );
    }

    /**
     * Return corresponding template.
     *
     * @return template
     * @throws \coding_exception
     */
    public function get_template(): template {
        return new template($this->get('templateid'));
    }

    /**
     * Extract data model.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function get_data_model() {
        return json_decode($this->get('datamodel'));
    }

    /**
     * Create notification bare bone from template.
     *
     * @param template $template
     * @return notification
     * @throws \coding_exception
     */
    public static function get_from_template(template $template) {
        $record = new \stdClass();
        $record->templateid = $template->get('id');
        $record->content = $template->get('content');
        $record->datamodel = $template->get('datamodel');
        return new notification(0, $record);
    }
}