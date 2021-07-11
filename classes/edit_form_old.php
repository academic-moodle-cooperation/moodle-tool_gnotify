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

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/lib/editor/atto/lib.php');

/**
 * Class tool_gnotify_edit_form
 *
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_gnotify_edit_form_old extends moodleform {

    /**
     * Validation
     *
     * @param array $data
     * @param array $files
     * @return array
     * @throws dml_exception
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!empty($data['template_name'])) {
            if ($DB->record_exists('tool_gnotify_tpl', ['name' => $data['template_name']])) {
                $errors['duplicate'] = 'Duplicate Keys';
                // TODO Show error message
            }
        }

        return $errors;
    }

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {

        $mform =& $this->_form;

        $mform->addElement('static', 'template_name', get_string('createname', 'tool_gnotify'));
        $mform->setType('template_name', PARAM_TEXT);

        $atto = new atto_texteditor();
        $atto->use_editor('editor', []);

        $mform->addElement('editor', 'content', get_string('createtemplatecontent', 'tool_gnotify'), null);
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons();
    }
}