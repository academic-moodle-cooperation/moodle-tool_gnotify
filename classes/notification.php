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
 * Notification persistent
 *
 * @package     tool_gnotify
 * @author      Gregor Eichelberger
 * @copyright   2021 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_gnotify;

/**
 * Class representing a notification
 *
 * @package     tool_gnotify
 * @author      Gregor Eichelberger
 * @copyright   2021 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification extends \core\persistent {
    /**
     * Table name for this persistent.
     */
    const TABLE = 'tool_gnotify_notifications';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'templateid' => [
                'type' => PARAM_INT,
                'description' => 'Template',
            ],
            'content' => [
                'type' => PARAM_RAW,
            ],
            'fromdate' => [
                'type' => PARAM_INT,
                'description' => 'Start data',
            ],
            'todate' => [
                'type' => PARAM_INT,
                'description' => 'End data',
            ],
            'visibleon' => [
                'type' => PARAM_RAW,
                'description' => 'Visible on page',
            ],
            'visiblefor' => [
                'type' => PARAM_RAW,
                'description' => 'Visible for role',
            ],
            'visibleforprofile' => [
                    'type' => PARAM_TEXT,
                    'description' => 'Visible for profile',
            ],
            'configdata' => [
                'type' => PARAM_RAW,
            ],
            'datamodel' => [
                'type' => PARAM_RAW,
            ],
        ];
    }

    /**
     * Return corresponding template.
     *
     * @return template
     * @throws \coding_exception
     */
    public function get_template(): template {
        return new template($this->get('templateid'));
    }

    /**
     * Extract data model.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function get_data_model() {
        return json_decode($this->get('datamodel'));
    }

    /**
     * Check if notification is visible for page
     *
     * @param string $pagelayout
     * @return bool
     * @throws \coding_exception
     */
    public function is_visible_on_page($pagelayout): bool {
        $visibleon = $this->get("visibleon");
        if (empty($visibleon) || (strpos($this->get('visibleon'), $pagelayout) !== false)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if notification is visible for role
     *
     * @param \context $context
     * @return bool
     * @throws \coding_exception
     */
    public function is_visible_for_role(\context $context): bool {
        $visiblefor = $this->get("visiblefor");
        if (empty($visiblefor)) {
            return true;
        } else {
            $roles = get_user_roles($context);
            foreach ($roles as $role) {
                if (strpos($visiblefor, $role->roleid) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if notification is visible for profile
     *
     * @param \stdClass $user
     * @return bool
     * @throws \coding_exception
     */
    public function is_visible_for_profile(\stdClass $user): bool {
        $visibleforprofile = $this->get("visibleforprofile");

        if (empty($visibleforprofile)) {
            return true;
        } else {
            $profilefields = explode(',', $visibleforprofile);
            foreach ($profilefields as $profilefield) {
                $profilefield = explode(':', $profilefield);
                if (count($profilefield) == 2) {
                    $profilefield[0] = trim($profilefield[0]);
                    $profilefield[1] = trim($profilefield[1]);
                    if (property_exists($user, $profilefield[0]) && preg_match("/$profilefield[1]/", $user->{$profilefield[0]})) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Patch notification based on template
     *
     * @param template $template
     * @return bool|void
     */
    public function patch(template $template) {
        $this->set('content', $template->get('content'));
        $datamodel = json_decode($template->get('datamodel'));
        $olddatamodel = json_decode(self::get('datamodel'));
        foreach ($datamodel as $key => $value) {
            if (property_exists($olddatamodel, $key) && $olddatamodel->$key) {
                $datamodel->$key = $olddatamodel->$key;
            }
        }
        $this->set('datamodel', json_encode($datamodel));
    }

    /**
     * Create notification bare bone from template.
     *
     * @param template $template
     * @return notification
     * @throws \coding_exception
     */
    public static function get_from_template(template $template) {
        $record = new \stdClass();
        $record->templateid = $template->get('id');
        $record->content = $template->get('content');
        $record->datamodel = $template->get('datamodel');
        return new notification(0, $record);
    }
}
