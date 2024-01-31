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
namespace tool_gnotify\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Class template form
 *
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template extends \core\form\persistent {

    /** @var string Persistent class name. */
    protected static $persistentclass = 'tool_gnotify\\template';

    /**
     * Validation
     *
     * @param array $data
     * @param array $files
     * @param array $errors
     * @return array
     * @throws \dml_exception|\moodle_exception
     */
    public function extra_validation($data, $files, array &$errors) {
        global $PAGE;
        $newerrors = [];

        $renderer = new \tool_gnotify_var_renderer($PAGE, 'web');

        $formatoptions = new \stdClass();
        $formatoptions->trusted = true;
        $formatoptions->noclean = true;

        $htmlcontent = format_text($data->content, FORMAT_HTML, $formatoptions);

        try {
            $renderer->render_direct($htmlcontent, []);
        } catch (Exception $e) {
            $newerrors['content'] = $e->getMessage();
        }

        return $newerrors;
    }

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {

        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('createname', 'tool_gnotify'), 'size="64"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 64), 'maxlength', 64, 'client');
        $mform->addRule('name', 'duplicate', 'required', null, 'server');

        $mform->addElement('editor', 'content', get_string('createtemplatecontent', 'tool_gnotify'), null);
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons();
    }

    /**
     * Convert some fields.
     *
     * @param stdClass $data
     * @return object
     */
    protected static function convert_fields($data) {
        $data = parent::convert_fields($data);

        preg_match_all('/{{\s*([a-zA-Z0-9_]+?)\s*}}/', $data->content, $matches);

        $datamodel = new \stdClass();
        foreach ($matches[1] as $value) {
            $datamodel->$value = ''; // TODO Introduce default value.
        }

        $data->datamodel = json_encode($datamodel);

        return $data;
    }
}
