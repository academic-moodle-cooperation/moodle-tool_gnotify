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
 * Settings
 *
 * @package     tool_gnotify
 * @author      Angela Baier, Gregor Eichelberger, Thomas Wedekind
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $ADMIN, $CFG;
if ($hassiteconfig) {

    $ADMIN->add(
            'tools',
            new admin_category('gnotifyfolder', new lang_string('gnotify', 'tool_gnotify'))
    );

    $managegnotify = new admin_externalpage('gnotify_templates',
            new lang_string('managegnotify', 'tool_gnotify'), "$CFG->wwwroot/$CFG->admin/tool/gnotify/templates.php");
    $ADMIN->add('gnotifyfolder', $managegnotify);

    $settings = new admin_settingpage('tool_gnotify', new lang_string('settings', 'tool_gnotify'));
    $settings->add(new admin_setting_configduration('tool_gnotify/retentionperiod',
            new lang_string('retentionperiod', 'tool_gnotify'),
            new lang_string('retentionperioddesc', 'tool_gnotify'),
            30 * DAYSECS));

    $settings->add(new admin_setting_configcheckbox(
            'tool_gnotify/notificationpadding',
            get_string('padding', 'tool_gnotify'),
            get_string('paddinginfo', 'tool_gnotify'),
    1)
    );

    $ADMIN->add('gnotifyfolder', $settings);
}

