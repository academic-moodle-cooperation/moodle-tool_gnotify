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
 * Before footer
 *
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function tool_gnotify_before_footer() {

}

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

    global $PAGE, $DB, $USER;
    if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect'])) {
        // Do not try to show notifications inside iframe, in maintenance mode,
        // when printing, or during redirects.
        return;
    }
    $html = "";
    if (!isloggedin() || isguestuser()) {
        if ($PAGE->pagelayout == "login" || $PAGE->pagelayout == "frontpage") {
            $sql = "SELECT g.id, l.content, g.sticky
            FROM   {tool_gnotify_tpl_ins} g,
                   {tool_gnotify_tpl_lang} l
            WHERE  :time between fromdate AND todate
            AND    l.lang = 'en'
            AND    l.tplid = g.tplid
            AND    g.isvisibleonlogin = 1";
            $records = $DB->get_records_sql($sql, ['time' => time(), 'userid' => $USER->id]);
        } else {
            return;
        }
    } else {
        $sql = "SELECT g.*, l.content
                FROM   {tool_gnotify_tpl_ins} g,
                       {tool_gnotify_tpl_lang} l
                WHERE  :time between fromdate AND todate
                AND    l.lang = 'en'
                AND    l.tplid = g.tplid
                AND NOT EXISTS
                   (SELECT 1
                    FROM   {tool_gnotify_tpl_ins_ack} a
                    WHERE  g.id=a.insid
                    AND    a.userid = :userid)";
        $records = $DB->get_records_sql($sql, ['time' => time(), 'userid' => $USER->id]);
    }
    if ($records) {
        try {
            $context = [];
            $renderer = new tool_gnotify_var_renderer($PAGE, 'web');
            $formatoptions = new stdClass();
            $formatoptions->trusted = true;
            $formatoptions->noclean = true;

            foreach ($records as $record) {
                $htmlcontent = format_text($record->content, FORMAT_HTML, $formatoptions);
                $sql = "SELECT var.varname, content
                    FROM   {tool_gnotify_tpl_ins_var} ins,
                           {tool_gnotify_tpl_var} var
                    WHERE  var.id = ins.varid
                    AND    ins.insid = :insid";
                $vars = $DB->get_records_sql($sql, ['insid' => $record->id]);

                $varray = [];
                foreach ($vars as $var) {
                    $varray[$var->varname] = $var->content;
                }

                $htmlcontent = $renderer->render_direct($htmlcontent, $varray);
                if (!isloggedin() || isguestuser() || !$record->dismissable) {
                    $dismissable = false;
                } else {
                    $dismissable = true;
                }

                switch ($record->ntype) {
                    case TOOL_GNOTIFY_NOTIFICATION_TYPE_INFO:
                        $ntype = 'alert-info';
                        break;
                    case TOOL_GNOTIFY_NOTIFICATION_TYPE_WARN:
                        $ntype = 'alert-warning';
                        break;
                    case TOOL_GNOTIFY_NOTIFICATION_TYPE_ERROR:
                        $ntype = 'alert-danger';
                        break;
                    default:
                        $ntype = 'alert-none'; // This is a dummy value.
                        break;
                }

                $content = ['html' => $htmlcontent, 'id' => $record->id, 'dismissable' => $dismissable, 'ntype' => $ntype];
                if ($record->sticky != 1) {
                    $context['non-sticky'][] = $content;
                } else {
                    $context['sticky'][] = $content;
                }
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