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

/**
 * Class tool_gnotify_var_renderer
 *
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_gnotify_var_renderer extends renderer_base {

    /**
     * Render direct
     *
     * @param string $html Template
     * @param array $vars Variables
     * @return bool|string
     * @throws moodle_exception
     */
    public function render_direct($html, $vars) {
        $mustache = $this->get_mustache();
        $tmploader = $mustache->getLoader();
        $mustache->setLoader(new Mustache_Loader_StringLoader());
        $rendered = $this->render_from_template($html, $vars);
        $mustache->setLoader($tmploader);
        return $rendered;

    }

}

/**
 * Before footer
 *
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function tool_gnotify_before_footer() {

    global $PAGE, $DB, $USER;
    if (!isloggedin() || isguestuser()) {
        return;
    }
    if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect'])) {
        // Do not try to show notifications inside iframe, in maintenance mode,
        // when printing, or during redirects.
        return;
    }
    $sql = "SELECT g.id, l.content, g.sticky FROM {tool_gnotify_tpl_ins} g, {tool_gnotify_tpl_lang} l " .
    "WHERE :time between fromdate AND todate AND l.lang = 'en' AND l.tplid = g.tplid AND NOT EXISTS " .
    "(SELECT 1 FROM {tool_gnotify_tpl_ins_ack} a WHERE g.id=a.insid AND a.userid = :userid)";
    $records = $DB->get_records_sql($sql, ['time' => time(), 'userid' => $USER->id]);

    if ($records) {
        $context = [];
        foreach ($records as $record) {
            $formatoptions = new stdClass();
            $formatoptions->trusted = true;
            $formatoptions->noclean = true;
            $htmlcontent = format_text($record->content, FORMAT_HTML, $formatoptions);
            $sql = "SELECT var.varname, content from {tool_gnotify_tpl_ins_var} ins, {tool_gnotify_tpl_var} var  WHERE var.id = ins.varid AND ins.insid = :insid";
            $vars = $DB->get_records_sql($sql, ['insid' => $record->id]);
            $renderer = new tool_gnotify_var_renderer($PAGE, 'web');
            $vararray = [];
            foreach ($vars as $var) {
                $vararray[$var->varname] = $var->content;
            }
            $htmlcontent = $renderer->render_direct($htmlcontent, $vararray);

            if ($record->sticky != 1) {
                $context['notifications']['non-sticky'][] =
                        ['html' => $htmlcontent, 'id' => $record->id];
            } else {
                $context['notifications']['sticky'][] =
                        ['html' => $htmlcontent, 'id' => $record->id];
            }
        }
        $PAGE->requires->js_call_amd('tool_gnotify/notification', 'init', $context);
    }
}

