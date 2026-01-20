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

namespace tool_gnotify\local;

use core\hook\output\before_standard_head_html_generation;

/**
 * Tool Gnotify - Hook: Allows plugins to add any elements to the page <head> html tag.
 *
 * @package    tool_gnotify
 * @copyright  2024 Gregor Eichelberger <gregor.eichelberger@tuwien.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Callback to add head elements.
     *
     * @param before_standard_head_html_generation $hook
     */
    public static function before_standard_head_html_generation(before_standard_head_html_generation $hook): void {
        global $PAGE;

        if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect', 'embedded'])) {
            // Do not try to show notifications inside iframe, in maintenance mode,
            // when printing, or during redirects.
            return;
        }

        $PAGE->requires->js_call_amd('tool_gnotify/notification', 'init', [
                $PAGE->context->id,
                $PAGE->pagelayout,
                isloggedin(),
        ]);
    }
}
