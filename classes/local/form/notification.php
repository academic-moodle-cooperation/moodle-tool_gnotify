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

    /** Mustache template variable form id prefix */
    const PREFIX = 'var_';

    /**
     * Form definition.
     */
    protected function definition() {
        $mform =& $this->_form;

        foreach ($this->get_persistent()->get_data_model() as $key => $value) {
            $fieldid = self::PREFIX . $key;
            $mform->addElement('text', $fieldid, $key, 'size="64"');
            $mform->addRule($fieldid, get_string('required'), 'required', null, 'client');
            $mform->setType($fieldid, PARAM_TEXT);
            $mform->addRule(
                $fieldid,
                get_string('maximumchars', '', 255),
                'maxlength',
                255,
                'client'
            );
        }

        $options = [];
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_NONE] = get_string('optnone', 'tool_gnotify');
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_INFO] = get_string('optinfo', 'tool_gnotify');
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_WARN] = get_string('optwarn', 'tool_gnotify');
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_ERROR] = get_string('opterror', 'tool_gnotify');
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_SUCCESS] = get_string('optsuccess', 'tool_gnotify');
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_PRIMARY] = get_string('optprimary', 'tool_gnotify');
        $options[TOOL_GNOTIFY_NOTIFICATION_TYPE_SECONDARY] = get_string('optsecondary', 'tool_gnotify');

        $mform->addElement('select', 'ntype', get_string('ntype', 'tool_gnotify'), $options);

        $mform->addElement(
            'advcheckbox',
            'dismissable',
            get_string('dismissable', 'tool_gnotify'),
            get_string('dismissableinfo', 'tool_gnotify'),
            [0, 1]
        );
        $mform->setDefault('dismissable', 1);

        $mform->addElement(
            'advcheckbox',
            'visibleonany',
            get_string('visibleon', 'tool_gnotify'),
            get_string('visibleoninfo', 'tool_gnotify'),
            [0, 1]
        );
        $mform->setDefault('visibleonany', 1);

        $options = [
            'mydashboard' => get_string('myhome', 'core', null, false),
            'mycourses' => get_string('mycourses', 'core', null, false),
            'frontpage' => get_string('sitehome', 'core', null, false),
            'course' => get_string('course', 'core', null, false),
            'incourse' => get_string('incourse', 'tool_gnotify', null, false),
            'login' => get_string('login', 'core', null, false),
            'standard' => get_string('standard', 'core', null, false),
        ];

        $mform->addElement('select', 'visibleon', null, $options);
        $mform->getElement('visibleon')->setMultiple(true);
        $mform->hideif('visibleon', 'visibleonany', 'checked');

        $mform->addElement(
            'advcheckbox',
            'visibleforany',
            get_string('visiblefor', 'tool_gnotify'),
            get_string('visibleforinfo', 'tool_gnotify'),
            [0, 1]
        );

        $mform->setDefault('visibleforany', 1);

        $context = \context_system::instance();
        $roles = [];
        foreach (role_fix_names(get_all_roles($context)) as $role) {
            $roles[$role->id] = $role->localname;
        }
        $mform->addElement(
            'select',
            'visiblefor',
            null,
            $roles
        );
        $mform->addHelpButton('visiblefor', 'visiblefor', 'tool_gnotify');
        $mform->getElement('visiblefor')->setMultiple(true);
        $mform->setDefault('visiblefor', $roles);
        $mform->hideif('visiblefor', 'visibleforany', 'checked');
        $mform->hideif('visiblefor', 'visibleon', 'in', 'course');

        $mform->addElement(
            'text',
            'visibleforprofile',
            get_string('visibleforprofile', 'tool_gnotify'),
            'maxlength="128" size="64"'
        );

        $mform->addHelpButton('visibleforprofile', 'visibleforprofile', 'tool_gnotify');

        $mform->setType('visibleforprofile', PARAM_TEXT);

        $mform->addElement(
            'date_time_selector',
            'fromdate',
            get_string('fromdate', 'tool_gnotify'),
            ['optional' => false]
        );
        $mform->addElement(
            'date_time_selector',
            'todate',
            get_string('todate', 'tool_gnotify'),
            ['optional' => false]
        );

        $mform->addElement('hidden', 'templateid');
        $mform->setType('templateid', PARAM_INT);

        $mform->addElement('hidden', 'content');
        $mform->setType('content', PARAM_RAW);

        $this->add_action_buttons();
    }

    /**
     * Convert some fields.
     *
     * @param \stdClass $data
     * @return object
     */
    protected static function convert_fields($data) {
        $data = parent::convert_fields($data);
        $datamodel = new \stdClass();
        foreach ($data as $key => $value) {
            if (strpos($key, self::PREFIX) === 0) {
                $str = substr($key, strlen(self::PREFIX));
                $datamodel->$str = $value;
                unset($data->$key);
            }
        }
        $data->datamodel = json_encode($datamodel);

        $configdata = new \stdClass();
        $configdata->ntype = $data->ntype;
        unset($data->ntype);
        unset($data->padding); // Deprecated, but kept for compatibility.
        $configdata->dismissable = $data->dismissable;
        unset($data->dismissable);

        $data->configdata = json_encode($configdata);

        $data->visibleon = implode(',', $data->visibleon);
        $data->visiblefor = implode(',', $data->visiblefor);

        return $data;
    }

    /**
     * Get the default data.
     *
     * @return \stdClass
     */
    protected function get_default_data() {
        $data = parent::get_default_data();

        foreach (json_decode($data->datamodel) as $key => $value) {
            $fieldid = self::PREFIX . $key;
            $data->$fieldid = $value;
        }
        unset($data->datamodel);

        if ($data->configdata) {
            $configdata = json_decode($data->configdata);

            $data->ntype = $configdata->ntype;
            $data->dismissable = $configdata->dismissable;

            unset($data->configdata);
        }

        if (!$data->visibleon) {
            $data->visibleon = '';
            $data->visibleonany = 1;
        } else {
            $data->visibleon = explode(',', $data->visibleon);
            $data->visibleonany = 0;
        }

        if (!$data->visiblefor) {
            $data->visiblefor = '';
            $data->visibleforany = 1;
        } else {
            $data->visiblefor = explode(',', $data->visiblefor);
            $data->visibleforany = 0;
        }

        if ($data->todate == 0) {
            $data->todate = time() + 3600 * 24;
        }

        return $data;
    }

    /**
     * Validate form data.
     *
     * @param  stdClass $data Data to validate.
     * @param  array $files Array of files.
     * @param  array $errors Currently reported errors.
     * @return array of additional errors, or overridden errors.
     */
    protected function extra_validation($data, $files, array &$errors) {
        if ($data->fromdate > $data->todate) {
            $errors['todate'] = get_string('todateerror', 'tool_gnotify');
        }
        if ($data->visibleforprofile) {
            $expressions = explode(',', $data->visibleforprofile);
            foreach ($expressions as $expression) {
                $rule = explode(':', $expression);
                if (count($rule) != 2) {
                    $errors['visibleforprofile'] = get_string('visibleforprofileerrorrule', 'tool_gnotify')
                            . " [{$expression}]";
                    break;
                } else {
                    if (preg_match("/$rule[1]/", '') === false) {
                        $errors['visibleforprofile'] = get_string('visibleforprofileerrorregex', 'tool_gnotify')
                                . " [{$rule[1]}]";
                    }
                }
            }
        }
        return $errors;
    }
}
