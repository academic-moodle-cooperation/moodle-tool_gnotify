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

namespace tool_gnotify\local\hook\output;

use Exception;
use html_writer;
use stdClass;
use tool_gnotify\notification;
use tool_gnotify_var_renderer;

/**
 * Tool Gnotify - Hook: Allows plugins to add any elements to the page <head> html tag.
 *
 * @package    tool_gnotify
 * @copyright  2024 Gregor Eichelberger <gregor.eichelberger@tuwien.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_standard_head_html_generation {
    /**
     * Callback to add head elements.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function callback(\core\hook\output\before_standard_head_html_generation $hook): void {
        global $CFG, $PAGE, $USER;;

        // Require local library.
        require_once($CFG->dirroot.'/admin/tool/gnotify/locallib.php');


        if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect', 'embedded'])) {
            // Do not try to show notifications inside iframe, in maintenance mode,
            // when printing, or during redirects.
            return;
        }
        $html = "";
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
                $attributes = [
                        'id' => $uid,
                        'type' => 'hidden',
                        'data-gnotify' => json_encode($context),
                ];
                $html = html_writer::empty_tag('input', $attributes);

                $PAGE->requires->js_call_amd('tool_gnotify/notification', 'init', [$uid]);
            } catch (exception $e) {
                if (is_siteadmin()) {
                    \core\notification::error('[tool_gnotify] ' . $e);
                }
            }
        }
        $hook->add_html($html);
    }
}
