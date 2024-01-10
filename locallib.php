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
 * Gnotify locallib
 *
 * @package     tool_gnotify
 * @author      Angela Baier, Gregor Eichelberger, Thomas Wedekind
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

define('TOOL_GNOTIFY_NOTIFICATION_TYPE_NONE', 0);
define('TOOL_GNOTIFY_NOTIFICATION_TYPE_INFO', 1);
define('TOOL_GNOTIFY_NOTIFICATION_TYPE_WARN', 2);
define('TOOL_GNOTIFY_NOTIFICATION_TYPE_ERROR', 3);
define('TOOL_GNOTIFY_NOTIFICATION_TYPE_SUCCESS', 4);
define('TOOL_GNOTIFY_NOTIFICATION_TYPE_PRIMARY', 5);
define('TOOL_GNOTIFY_NOTIFICATION_TYPE_SECONDARY', 6);
