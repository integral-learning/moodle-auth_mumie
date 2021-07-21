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
 * This class provides an interface to export and delete user data.
 *
 * @package auth_mumie
 * @copyright  2017-2021 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_mumie\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;

/**
 * This class provides an interface to export and delete user data.
 *
 * @package auth_mumie
 * @copyright  2017-2021 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {
    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialized item collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_database_table(
            'auth_mumie_sso_tokens',
            [
                'token' => 'privacy:metadata:auth_mumie_tokens:token',
                'the_user' => 'privacy:metadata:auth_mumie_tokens:hash',
                'timecreated' => 'privacy:metadata:auth_mumie_tokens:timecreated',
            ],
            'privacy:metadata:auth_mumie_tokens:tableexplanation'
        );

        $collection->add_database_table(
            'auth_mumie_id_hashes',
            [
                'the_user' => 'privacy:metadata:auth_mumie_hashes:userid',
                'hash' => 'privacy:metadata:auth_mumie_tokens:hash',
            ],
            'privacy:metadata:auth_mumie_hashes:tableexplanation'
        );

        $collection->add_external_location_link(
            'MUMIE/Lemon',
            [
                'firstname' => 'privacy:metadata:auth_mumie_servers:firstname',
                'lastname' => 'privacy:metadata:auth_mumie_servers:lastname',
                'email' => 'privacy:metadata:auth_mumie_servers:email'
            ],
            'privacy:metadata:auth_mumie_servers:tableexplanation'
        );
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        global $DB;
        $contextlist = new contextlist();
        $contextlist->set_component('auth_mumie');

        $hashes = $DB->get_records('auth_mumie_id_hashes', array('the_user' => $userid));
        $courseids = array();

        foreach ($hashes as $hash) {
            $matches = array();
            \preg_match('@gradepool([0-9]*)@', $hash->hash, $matches);
            if (count($matches) > 0 && !in_array($matches[1], $courseids)) {
                array_push($courseids, $matches[1]);
            }
        }

        if (count($courseids) > 0) {
            list($insql, $inparams) = $DB->get_in_or_equal($courseids);

            $sql = "SELECT c.id
                    FROM {context} c
                    JOIN {course} course
                    ON c.contextlevel = ?
                    AND c.instanceid $insql";
            $contextlist->add_from_sql(
                $sql,
                array_merge([CONTEXT_COURSE], $inparams)
            );
        }

        // Add User context.
        $sql = "SELECT ctx.id
                  FROM {auth_mumie_id_hashes} hashes
                  JOIN {context} ctx ON ctx.instanceid = hashes.the_user AND ctx.contextlevel = :contextlevel
                 WHERE hashes.the_user = :userid";
        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_course::class)) {
            return;
        }
        $courseid = $context->__get("instanceid");

        $sql = "SELECT the_user as userid
            FROM {auth_mumie_id_hashes}
            WHERE hash LIKE :gradepool
        ";
        $userlist->add_from_sql('userid', $sql, array('gradepool' => "%@gradepool{$courseid}@"));
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param  approved_contextlist $contextlist The list of approved contexts for a user.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        foreach ($contextlist->get_contexts() as $context) {
            // Check that the context is a course context.
            if ($context->contextlevel != CONTEXT_COURSE && $context->contextlevel != CONTEXT_USER) {
                continue;
            }
            $records = $DB->get_records('auth_mumie_id_hashes', array('the_user' => $contextlist->get_user()->id));
            $hashes = array_map(
                function($record) {
                    return $record->hash;
                },
                $records
            );
            if ($context->contextlevel == CONTEXT_COURSE ) {
                self::export_id_hashes($hashes, $context);
            }

            if ($context->contextlevel == CONTEXT_USER) {
                self::export_sso_tokens($hashes, $context);
            }

        }
    }

    /**
     * Export lookup table for hashed user IDs.
     *
     * @param  array $hashes
     * @param  \context $context
     * @return void
     */
    private static function export_id_hashes(array $hashes, \context $context) {
        $data = [];
        $courseid = $context->__get("instanceid");
        foreach ($hashes as $hash) {
            if (strpos($hash, "@gradepool{$courseid}@") !== false) {
                $data["mumieId"] = $hash;
            }
        }
        writer::with_context($context)->export_data(
            [
                get_string('pluginname', 'auth_mumie'),
                get_string('mumie_course_account', 'auth_mumie')
            ],
            (object) $data
        );
    }

    /**
     * Export sso token table.
     *
     * @param  array $hashes
     * @param  \context $context
     * @return void
     */
    private static function export_sso_tokens(array $hashes, \context $context) {
        global $DB;

        list($insql, $inparams) = $DB->get_in_or_equal($hashes);

        $sql = "SELECT * FROM {auth_mumie_sso_tokens}
                WHERE the_user $insql";
        $tokens = $DB->get_records_sql($sql, $inparams);
        writer::with_context($context)->export_data(
            [
                get_string('pluginname', 'auth_mumie'),
                get_string('mumie_sso_tokens', 'auth_mumie')
            ],
            (object) $tokens
        );
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_COURSE) {
                self::delete_in_course_context($context, [$userid]);
            } else if ($context->contextlevel == CONTEXT_USER) {
                self::delete_in_user_context($context, [$userid]);
            }
        }
    }

    /**
     * Delete all use data which matches the specified context.
     *
     * @param \context $context The module context.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel == CONTEXT_USER) {
            self::delete_in_user_context($context, [$context->__get('instanceid')]);
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $courseid = $context->__get("instanceid");
            $sql = "SELECT * FROM {auth_mumie_id_hashes} WHERE hash LIKE :gradepool";
            $records = $DB->get_records_sql($sql, array('gradepool' => "%@gradepool{$courseid}@"));
            foreach ($records as $record) {
                $DB->delete_records('auth_mumie_id_hashes', array('id' => $record->id));
                $DB->delete_records('auth_mumie_sso_tokens', array('the_user' => $record->hash));
            }
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param  approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            self::delete_in_user_context($context, $userlist->get_userids());
        } else if ($context instanceof \context_course) {
            self::delete_in_course_context($context, $userlist->get_userids());
        }
    }

    /**
     * Delete all personal data from a given course context.
     *
     * @param  \context $context
     * @param  array $userids
     * @return void
     */
    private static function delete_in_course_context(\context $context, array $userids) {
        global $DB;
        $courseid = $context->__get("instanceid");
        list($insql, $inparams) = $DB->get_in_or_equal($userids);
        $sql = "SELECT * FROM {auth_mumie_id_hashes} WHERE the_user $insql";
        $records = $DB->get_records_sql($sql, $inparams);
        foreach ($records as $record) {
            if (strpos($record->hash, "@gradepool{$courseid}@") !== false ) {
                $DB->delete_records('auth_mumie_id_hashes', array('the_user' => $record->the_user, 'hash' => $record->hash));
                $DB->delete_records('auth_mumie_sso_tokens', array('the_user' => $record->hash));
            }
        }
    }

    /**
     * Delete personal data for a given user context.
     *
     * @param  \context $context
     * @param  array $userids
     * @return void
     */
    private static function delete_in_user_context(\context $context, array $userids) {
        global $DB;
        if (!is_a($context, \context_user::class)) {
            return;
        }
        list($insql, $inparams) = $DB->get_in_or_equal($userids);
        $sql = "SELECT * FROM {auth_mumie_id_hashes} WHERE the_user $insql";
        $records = $DB->get_records_sql($sql, $inparams);
        foreach ($records as $record) {
            $DB->delete_records('auth_mumie_id_hashes', array('the_user' => $record->the_user));
            $DB->delete_records('auth_mumie_sso_tokens', array('the_user' => $record->hash));
        }
    }
}
