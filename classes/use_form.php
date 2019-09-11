<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/lib/editor/atto/lib.php');

class tool_gnotify_use_form extends moodleform {
    
    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        $mform =& $this->_form;
        $formcontext = $this->_customdata;
        foreach ($formcontext["vars"] as $var) {
            $mform->addElement('text', $var->name, $var->name, 'size="64"');
            $mform->addRule($var->name,get_string('required') , 'required', null, 'client');
            $mform->setType($var->name, PARAM_TEXT);
            $mform->addRule($var->name, get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        }
        $mform->addElement('date_time_selector',
            'fromdate',
            get_string('fromdate', 'tool_gnotify'),
            array('optional' => false));
        $mform->addElement('date_time_selector',
            'todate',
            get_string('todate', 'tool_gnotify'),
            array('optional' => false));
        $this->add_action_buttons();
    }
}