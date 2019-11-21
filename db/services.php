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
 * Webservice definition
 *
 * @package     tool_gnotify
 * @author      Angela Baier, Gregor Eichelberger, Thomas Wedekind
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$services = array(
        'tool_gnotify_services' => array(
                'functions' => array('tool_gnotify_acknoledge_notification'),
                'requiredcapability' => '',
                'restrictedusers' => 0,
                'enabled' => 1,
        )
);

$functions = array(
        'tool_gnotify_acknoledge_notification' => array(
                'classname' => 'tool_gnotify_external',
                'methodname' => 'acknoledge', // implement this function into the above class
                'classpath' => 'tool/gnotify/externallib.php',
                'description' => 'Allows users to acknoledge that they have seen a notification',
                'type' => 'write', // the value is 'write' if your function does any database change, otherwise it is 'read'.
                'ajax' => true, // true/false if you allow this web service function to be callable via ajax
                'capabilities' => '',  // TODO capabilities
        )
);