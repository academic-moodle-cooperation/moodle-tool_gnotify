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
                'functions' => array('tool_gnotify_acknowledge_notification'),
                'requiredcapability' => '',
                'restrictedusers' => 0,
                'enabled' => 1,
        )
);

$functions = array(
        'tool_gnotify_acknowledge_notification' => array(
                'classname' => 'tool_gnotify\external\acknowledge',
                'methodname' => 'execute',
                'description' => 'Allows users to acknowledge that they have seen a notification',
                'type' => 'write',
                'ajax' => true,
                'capabilities' => '',  // TODO capabilities
        )
);