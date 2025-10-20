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

namespace tool_gnotify\privacy;

use context;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\core_userlist_provider;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use tool_gnotify\ack;

/**
 * Privacy provider implementation for the tool_gnotify plugin.
 * This class handles metadata, data export, and data deletion for user data.
 *
 * @package     tool_gnotify
 * @author      Gregor Eichelberger
 * @copyright   2025 Technische Universität Wien {@link http://www.tuwien.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider,
        core_userlist_provider {

    /**
     * Description of the metadata stored for users in tool_gnotify.
     *
     * @param collection $collection a collection to add to.
     * @return collection the collection, with relevant metadata descriptions for tool_gnotify added.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
                'tool_gnotify_acks',
                [
                    'userid' => 'privacy:metadata:tool_gnotify_acks:userid',
                    'notificationid' => 'privacy:metadata:tool_gnotify_acks:notificationid',
                    'timecreated' => 'privacy:metadata:tool_gnotify_acks:timecreated',
                    'timemodified' => 'privacy:metadata:tool_gnotify_acks:timemodified',
                ],
                'privacy:metadata:tool_gnotify_acks'
        );
        return $collection;
    }

    /**
     * Get all contexts contain user information for the given user.
     *
     * @param int $userid the id of the user.
     * @return contextlist the list of contexts containing user information.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT ctx.id
                  FROM {tool_gnotify_acks} a
                  JOIN {context} ctx ON ctx.instanceid = a.userid AND ctx.contextlevel = :contextlevel
                 WHERE a.userid = :userid";
        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Export all user data for the user in the identified contexts.
     *
     * @param approved_contextlist $contextlist the list of approved contexts for the user.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        $user = $contextlist->get_user();

        $acks = ack::get_records(['userid' => $user->id]);
        foreach ($acks as $ack) {
            $data = (object)[
                    'userid' => $ack->get('userid'),
                    'notificationid' => $ack->get('notificationid'),
                    'timecreated' => transform::datetime($ack->get('timecreated')),
                    'timemodified' => transform::datetime($ack->get('timemodified')),
            ];
            writer::with_context(\context_user::instance($user->id))->export_data(
                    [get_string('privacy:metadata:tool_gnotify_acks', 'tool_gnotify')],
                    $data
            );
        }
    }

    /**
     * Delete user data in the list of given contexts.
     *
     * @param approved_contextlist $contextlist the list of contexts.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        if (empty($contextlist->count())) {
            return;
        }
        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_USER) {
                continue;
            }
            if ($context->instanceid == $userid) {
                ack::delete_all_by_user($userid);
            }
        }
    }

    /**
     * Delete all user data for this context.
     *
     * @param  context $context The context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        if ($context->contextlevel != CONTEXT_USER) {
            return;
        }
        ack::delete_all_by_user($context->instanceid);
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $sql = "SELECT userid FROM {tool_gnotify_acks} WHERE userid = ?";
        $params = [$context->instanceid];
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            ack::delete_all_by_user($context->instanceid);
        }
    }
}
