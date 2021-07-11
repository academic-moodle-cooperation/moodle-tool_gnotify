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
 * upgrade.php for tool_gnotify
 * @package    tool_gnotify
 * @copyright  2019 University of Vienna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * xmldb_tool_gnotify_upgrade is the function that upgrades
 * the gnotify tool database when is needed
 *
 * This function is automaticly called when version number in
 * version.php changes.
 *
 * @param float $oldversion Old version number.
 *
 * @return boolean
 */
function xmldb_tool_gnotify_upgrade(float $oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2019120400) {

        // Define field isvisibleonlogin to be added to tool_gnotify_tpl_ins.
        $table = new xmldb_table('tool_gnotify_tpl_ins');
        $field = new xmldb_field('isvisibleonlogin', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'sticky');

        // Conditionally launch add field isvisibleonlogin.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gnotify savepoint reached.
        upgrade_plugin_savepoint(true, 2019120400, 'tool', 'gnotify');
    }
    if ($oldversion < 2019120601) {

        // Define field isvisibleonlogin to be added to tool_gnotify_tpl_ins.
        $table = new xmldb_table('tool_gnotify_tpl_ins');
        $field = new xmldb_field('dismissable', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'sticky');

        // Conditionally launch add field isvisibleonlogin.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gnotify savepoint reached.
        upgrade_plugin_savepoint(true, 2019120601, 'tool', 'gnotify');
    }
    if ($oldversion < 2019120602) {

        // Define field ntype to be added to tool_gnotify_tpl_ins.
        $table = new xmldb_table('tool_gnotify_tpl_ins');
        $field = new xmldb_field('ntype', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'dismissable');

        // Conditionally launch add field ntype.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gnotify savepoint reached.
        upgrade_plugin_savepoint(true, 2019120602, 'tool', 'gnotify');
    }
    if ($oldversion < 2019122000) {

        // Define field padding to be added to tool_gnotify_tpl_ins.
        $table = new xmldb_table('tool_gnotify_tpl_ins');
        $field = new xmldb_field('padding', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'ntype');

        // Conditionally launch add field padding.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Gnotify savepoint reached.
        upgrade_plugin_savepoint(true, 2019122000, 'tool', 'gnotify');
    }
    if ($oldversion < 2021042008) {

        // Define table tool_gnotify_templates to be created.
        $table = new xmldb_table('tool_gnotify_templates');

        // Adding fields to table tool_gnotify_templates.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('contentformat', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('datamodel', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table tool_gnotify_templates.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for tool_gnotify_templates.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table tool_gnotify_notifications to be created.
        $table = new xmldb_table('tool_gnotify_notifications');

        // Adding fields to table tool_gnotify_notifications.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('fromdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('todate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visibleonlogin', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('configdata', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('datamodel', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table tool_gnotify_notifications.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $table->add_key('templateid_fk', XMLDB_KEY_FOREIGN, ['templateid'], 'tool_gnotify_template', ['id']);

        // Conditionally launch create table for tool_gnotify_notifications.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Gnotify savepoint reached.
        upgrade_plugin_savepoint(true, 2021042008, 'tool', 'gnotify');
    }

    return true;
}
