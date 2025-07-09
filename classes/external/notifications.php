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
namespace tool_gnotify\external;

use coding_exception;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use moodle_exception;
use tool_gnotify\notification;
use tool_gnotify_var_renderer;

/**
 * Class notifications
 *
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notifications extends external_api {

    /**
     * Notifications
     *
     * @param int $contextid
     * @param string $pagelayout
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    public static function execute(int $contextid, string $pagelayout) {
        global $CFG, $PAGE, $USER;;
        $context = \context::instance_by_id($contextid);
        $PAGE->set_pagelayout('course');
        $PAGE->set_context($context);
        // Require local library.
        require_once($CFG->dirroot.'/admin/tool/gnotify/locallib.php');
        $result = [
                'template' => 'tool_gnotify/notifications',
                'notifications' => [],
                'javascript' => null,
        ];

        if (!isloggedin() || isguestuser()) {
            $records = notification::get_records_select(
                    ':time between fromdate and todate',
                    [
                            'time' => time(),
                    ]);
        } else {
            $select = ":time BETWEEN fromdate AND todate AND NOT EXISTS
                   (SELECT 1 FROM {tool_gnotify_acks} a
                   WHERE {tool_gnotify_notifications}.id=a.notificationid and a.userid=:userid)";
            $records = notification::get_records_select($select, ['time' => time(), 'userid' => $USER->id]);
        }

        if ($records) {
                $renderer = new tool_gnotify_var_renderer($PAGE, 'web');
                $formatoptions = (object)[
                    'trusted' => true,
                    'noclean' => false,
                ];
                foreach ($records as $record) {

                    if (!$record->is_visible_on_page($pagelayout)) {
                        continue;
                    }

                    if (!$record->is_visible_for_role($PAGE->context)) {
                        continue;
                    }

                    if (!$record->is_visible_for_profile($USER)) {
                        continue;
                    }

                    $htmlcontent = \core_external\util::format_text($record->get('content'), FORMAT_HTML,
                            $context , 'tool_gnotify', null, null, $formatoptions);

                    $datamodel = $record->get_data_model();
                    $lang = 'lang='.current_language();
                    $datamodel->$lang = true;

                    $htmlcontent = $renderer->render_direct($htmlcontent[0], $datamodel);
                    $config = json_decode($record->get('configdata'));

                    if (!isloggedin() || isguestuser()) {
                        $config->dismissable = false;
                    } else {
                        $config->dismissable = boolval($config->dismissable);
                    }

                    switch ($config->ntype) {
                        case TOOL_GNOTIFY_NOTIFICATION_TYPE_INFO:
                            $config->ntype = 'alert-info';
                            break;
                        case TOOL_GNOTIFY_NOTIFICATION_TYPE_WARN:
                            $config->ntype = 'alert-warning';
                            break;
                        case TOOL_GNOTIFY_NOTIFICATION_TYPE_ERROR:
                            $config->ntype = 'alert-danger';
                            break;
                        case TOOL_GNOTIFY_NOTIFICATION_TYPE_SUCCESS:
                            $config->ntype = 'alert-success';
                            break;
                        case TOOL_GNOTIFY_NOTIFICATION_TYPE_PRIMARY:
                            $config->ntype = 'alert-primary';
                            break;
                        case TOOL_GNOTIFY_NOTIFICATION_TYPE_SECONDARY:
                            $config->ntype = 'alert-secondary';
                            break;
                        default:
                            unset($config->ntype); // This is a dummy value.
                            break;
                    }

                    $config->html = $htmlcontent;
                    $config->id = $record->get('id');
                    $config->padding = boolval(get_config('tool_gnotify', 'notificationpadding'));
                    $result['notifications'][] = $config;
                }
        }
        return $result;
    }

    /**
     * Get notifications parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
                'contextid' => new external_value(PARAM_INT, 'Context id name', VALUE_REQUIRED ),
                'pagelayout' => new external_value(PARAM_ALPHAEXT, 'Page layout', VALUE_REQUIRED, ),
        ]);
    }

    /**
     * Acknowledge returns
     *
     * @return |null
     */
    public static function execute_returns() {
        return new external_single_structure([
                'template' => new external_value(PARAM_PATH, 'Template name'),
                'notifications' => new external_multiple_structure(
                        new external_single_structure([
                            'id' => new external_value(PARAM_INT, 'Notification id'),
                            'html' => new external_value(PARAM_RAW, 'HTML content'),
                            'ntype' => new external_value(PARAM_TEXT, 'Notification type', VALUE_OPTIONAL),
                            'dismissable' => new external_value(PARAM_BOOL, 'Dismissable flag'),
                            'padding' => new external_value(PARAM_BOOL, 'Padding flag'),
                        ])
                ),
                'javascript' => new external_value(PARAM_RAW, 'JavaScript fragment'),
        ]);
    }

}
