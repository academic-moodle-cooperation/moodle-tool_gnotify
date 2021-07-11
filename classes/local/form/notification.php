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
 * Use form
 *
 * @package     tool_gnotify
 * @author      Angela Baier, Gregor Eichelberger, Thomas Wedekind
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_gnotify\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/lib/editor/atto/lib.php');
require_once($CFG->dirroot . '/admin/tool/gnotify/locallib.php');

/**
 * Class notification
 *
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification extends \core\form\persistent {

    /** @var string Persistent class name. */
    protected static $persistentclass = 'tool_gnotify\\notification';

    /**
     * Form definition.
     */
    protected function definition() {
        $mform =& $this->_form;
        $formcontext = $this->_customdata;

        foreach ($this->get_persistent()->get_data_model() as $key => $value) {
            $fieldid = 'var_'.$key;
            $mform->addElement('text', $fieldid, $key, 'size="64"');
            $mform->addRule($fieldid, get_string('required'), 'required', null, 'client');
            $mform->setType($fieldid, PARAM_TEXT);
            $mform->addRule($fieldid, get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        }
        $options = array();
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_NONE] = get_string('optnone', 'tool_gnotify');
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_INFO] = get_string('optinfo', 'tool_gnotify');
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_WARN] = get_string('optwarn', 'tool_gnotify');
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_ERROR] = get_string('opterror', 'tool_gnotify');

        $mform->addElement('select', 'ntype', get_string('ntype', 'tool_gnotify'), $options);
        $mform->addElement('advcheckbox', 'sticky', get_string('sticky', 'tool_gnotify'), get_string('stickyinfo', 'tool_gnotify'), array(0, 1));
        $mform->addElement('advcheckbox', 'dismissable', get_string('dismissable', 'tool_gnotify'), get_string('dismissableinfo', 'tool_gnotify'), array(0, 1));
        $mform->setDefault('dismissable', 1);
        $mform->addElement('advcheckbox', 'isvisibleonlogin', get_string('isvisibleonlogin', 'tool_gnotify'), get_string('isvisibleonlogininfo', 'tool_gnotify'), array(0, 1));
        $mform->addElement('advcheckbox', 'padding', get_string('padding', 'tool_gnotify'), get_string('paddinginfo', 'tool_gnotify'), array(0, 1));
        $mform->setDefault('padding', 1);
        $mform->addElement('date_time_selector',
                'fromdate',
                get_string('fromdate', 'tool_gnotify'),
                array('optional' => false));
        $mform->addElement('date_time_selector',
                'todate',
                get_string('todate', 'tool_gnotify'),
                array('optional' => false));
        $mform->setDefault('todate', time() + 3600 * 24);

        $this->add_action_buttons();
    }
}