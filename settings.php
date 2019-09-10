<?php  
// This file is part of mod_offlinequiz for Moodle - http://moodle.org/
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
 * Administration settings definitions for the offlinequiz module.
 *
 * @package       tool
 * @subpackage    gnotify
 * @author        Thomas Wedekind
 * @copyright     2019 Universitaet Wien
 * @since         Moodle 3.7+
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $ADMIN;
if ($hassiteconfig) {
    $temp = new admin_externalpage('gnotify_templates', new lang_string('templates','tool_gnotify'), "$CFG->wwwroot/$CFG->admin/tool/gnotify/templates.php");
    $ADMIN->add('server',$temp);
}
 ?>