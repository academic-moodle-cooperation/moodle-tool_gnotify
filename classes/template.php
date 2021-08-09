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
 * Notification template persistent
 *
 * @package     tool_gnotify
 * @author      Gregor Eichelberger
 * @copyright   2021 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_gnotify;

use renderer_base;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing a notification template
 *
 * @package     tool_gnotify
 * @author      Gregor Eichelberger
 * @copyright   2021 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template extends \core\persistent implements \templatable {

    /**
     * Table name for this persistent.
     */
    const TABLE = 'tool_gnotify_templates';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'Template name',
            ],
            'content' => [
                'type' => PARAM_RAW,
            ],
            'contentformat' => [
                'type' => PARAM_INT,
            ],
            'datamodel' => [
                'type' => PARAM_RAW,
            ],
        );
    }

    /**
     * Get template name.
     *
     * @return mixed|null
     * @throws \coding_exception
     */
    public function get_template_name() {
        return $this->get('name');
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
     * Check if template with name exists.
     *
     * @param string $name
     * @return bool
     * @throws \dml_exception
     */
    public static function template_exists(string $name) : bool {
        global $DB;
        return $DB->record_exists(self::TABLE, ['name' => $name]);
    }

    /**
     * Export for use in mustache templates.
     *
     * @param renderer_base $output
     * @return array|stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = $this->to_record();
        $data->datamodel = json_decode($data->datamodel);
        return $data;
    }
}
