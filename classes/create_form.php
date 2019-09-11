<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/lib/editor/atto/lib.php');

class tool_gnotify_create_form extends moodleform {

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {

        $mform =& $this->_form;

        $mform->addElement('text', 'template_name', get_string('createname', 'tool_gnotify'), 'size="64"');
        $mform->setType('template_name', PARAM_TEXT);
        $mform->addRule('template_name',get_string('required') , 'required', null, 'client');
        $mform->addRule('template_name', get_string('maximumchars', '', 64), 'maxlength', 64, 'client');
        $mform->addRule('template_name','serversidetest', 'required', null,'server');


        $atto = new atto_texteditor();
        $atto->use_editor('editor', []);

        $mform->addElement('editor', 'content', get_string('createtemplatecontent','tool_gnotify'), null);
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content',get_string('required'), 'required', null, 'client');
        //$mform->addElement($atto);
        $this->add_action_buttons();
        // TODO: Implement definition() method.
    }

    function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);
        if (!empty($data['template_name'])) {
            if ($DB->record_exists('gnotify_tpl', ['name' => $data['template_name']])) {
                $errors['duplicate'] = 'Duplicate Keys';
                //TODO Show error message
            }
        }


        return $errors;
    }
}