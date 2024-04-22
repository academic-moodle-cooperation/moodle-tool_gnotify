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
 * Tool Gnotify - Hook callbacks
 *
 * @package    tool_gnotify
 * @copyright  2024 Gregor Eichelberger <gregor.eichelberger@tuwien.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\hook\output\before_standard_head_html_generation;

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => before_standard_head_html_generation::class,
        'callback' => 'tool_gnotify\local\hook\output\before_standard_head_html_generation::callback',
        'priority' => 0,
    ],
];
