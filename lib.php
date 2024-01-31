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
 * Gnotify magic happens here
 *
 * @package     tool_gnotify
 * @author      Angela Baier, Gregor Eichelberger, Thomas Wedekind
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Before standard top of body html
 *
 * @return string|void
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function tool_gnotify_before_standard_top_of_body_html() {
    require_once(__DIR__ . '/locallib.php');

    global $PAGE, $USER;
    if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect', 'embedded'])) {
        // Do not try to show notifications inside iframe, in maintenance mode,
        // when printing, or during redirects.
        return;
    }
    $html = "";
    if (!isloggedin() || isguestuser()) {
        $records = \tool_gnotify\notification::get_records_select(
            ':time between fromdate and todate',
            [
                'time' => time()
            ]);
    } else {
        $select = ":time BETWEEN fromdate AND todate AND NOT EXISTS
                   (SELECT 1 FROM {tool_gnotify_acks} a
                   WHERE {tool_gnotify_notifications}.id=a.notificationid and a.userid=:userid)";
        $records = \tool_gnotify\notification::get_records_select($select, ['time' => time(), 'userid' => $USER->id]);
    }

    if ($records) {
        try {
            $context = [];
            $renderer = new tool_gnotify_var_renderer($PAGE, 'web');
            $formatoptions = new stdClass();
            $formatoptions->trusted = true;
            $formatoptions->noclean = true;

            foreach ($records as $record) {

                if (!$record->is_visible_on_page($PAGE->pagelayout)) {
                    continue;
                }

                if (!$record->is_visible_for_role($PAGE->context)) {
                    continue;
                }

                if (!$record->is_visible_for_profile($USER)) {
                    continue;
                }

                $htmlcontent = format_text($record->get('content'), FORMAT_HTML, $formatoptions);

                $datamodel = $record->get_data_model();
                $lang = 'lang='.current_language();
                $datamodel->$lang = true;

                $htmlcontent = $renderer->render_direct($htmlcontent, $datamodel);
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
                        $config->ntype = 'alert-none'; // This is a dummy value.
                        break;
                }

                $config->html = $htmlcontent;
                $config->id = $record->get('id');
                $config->padding = boolval($config->padding);

                $context['non-sticky'][] = $config;
            }

            $uid = uniqid("gnotify");
            $attributes = array(
                    'id' => $uid,
                    'type' => 'hidden',
                    'data-gnotify' => json_encode($context));
            $html = html_writer::empty_tag('input', $attributes);

            $PAGE->requires->js_call_amd('tool_gnotify/notification', 'init', array($uid));
        } catch (exception $e) {
            if (is_siteadmin()) {
                \core\notification::error('[tool_gnotify] ' . $e);
            }
        }
    }
    return $html;
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 * @throws coding_exception
 * @throws moodle_exception
 */
function tool_gnotify_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    if (isguestuser($user)) {
        return false;
    }

    $history = new moodle_url('/admin/tool/gnotify/history.php');
    $string = get_string('myprofilehistory', 'tool_gnotify');
    $node = new core_user\output\myprofile\node('miscellaneous', 'gnotifyhistory', $string, null,
            $history);
    $tree->add_node($node);

    return true;
}
