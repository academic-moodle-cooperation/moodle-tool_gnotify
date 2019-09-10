<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/lib/editor/atto/lib.php');

class tool_gnotify_create_form extends moodleform {

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        global $CFG, $OUTPUT;
        $mform =& $this->_form;

        $mform->addElement('text', 'template_name', get_string('create_name', 'tool_gnotify'), 'size="64"');
        $mform->setType('template_name', PARAM_TEXT);
        $mform->addRule('template_name','Test33', 'required', null, 'client');

        $atto = new atto_texteditor();
        $atto->use_editor('editor', []);

        $mform->addElement('editor', 'message','Test22', null);
        $mform->setType('message', PARAM_RAW);
        $mform->addRule('message','Test33', 'required', null, 'client');
        //$mform->addElement($atto);
        // TODO: Implement definition() method.
    }
}