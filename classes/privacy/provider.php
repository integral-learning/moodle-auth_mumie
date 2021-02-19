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
 * This file defines the version of auth_mumie
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
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

class provider implements 
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {
    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised item collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
 
        // Here you will add more items into the collection.
 
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

        foreach($hashes as $hash) {
            $matches = array();
            \preg_match('@gradepool([0-9]*)@', $hash->hash, $matches);
            if (count($matches) > 0 && !in_array($matches[1], $courseids)) {
                array_push($courseids, $matches[1]);
            }
        }

        if(count($courseids) > 0) {
            $sql = "SELECT c.id
                    FROM {context} c
                    JOIN {course} course
                    ON c.contextlevel = :contextlevel
                    AND c.instanceid IN (" . implode(", ", $courseids) .")";
            $contextlist->add_from_sql(
                $sql,
                array(
                    'contextlevel' => CONTEXT_COURSE,
                )
            );
        }

        // Add User context
        $sql = "SELECT ctx.id
                  FROM {auth_mumie_id_hashes} hashes
                  JOIN {context} ctx ON ctx.instanceid = hashes.the_user AND ctx.contextlevel = :contextlevel
                 WHERE hashes.the_user = :userid";
        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    private static function arrayToSqlString($array) {
        $s = '';
        //foreach()
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        // debugging("GETTING USERS in CONTEXT");

        //TODO: Handle User-Context;
        if (!is_a($context, \context_course::class)) {
            return;
        }

        $sql = "SELECT the_user as userid
            FROM {auth_mumie_id_hashes}
            WHERE hash LIKE :gradepool
        ";
        $userlist->add_from_sql('userid', $sql, array('gradepool' => $context->instance));
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

    private static function export_id_hashes(array $hashes, $context) {
        $data = [];
        $courseid = $context->__get("instanceid");
        debugging("courseid" . $courseid);
        foreach($hashes as $hash) {
            if(strpos($hash, "@gradepool{$courseid}@") !== false) {
                $data["uniqueIdForCourse"] = $hash;
            }
        }
        writer::with_context($context)->export_data([get_string('pluginname', 'auth_mumie'), 'TODO: user ID for MUMIE and Lemon servers'], (object) $data);
    }

    private static function export_sso_tokens(array $hashes, $context) {
        global $DB;

        $sql = "SELECT * FROM {auth_mumie_sso_tokens}
                WHERE the_user in (" . "'" . implode("', '", (array) $hashes) . "')";
        $tokens = $DB->get_records_sql($sql, array("hashes" => implode("', '", (array) $hashes)));
        writer::with_context($context)->export_data([get_string('pluginname', 'auth_mumie'), 'TODO: SSO Tokens'], (object) $tokens);
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
            if($context->contextlevel == CONTEXT_COURSE) {
                $courseid = $context->__get("instanceid");
                $records = $DB->get_records('auth_mumie_id_hashes', array('the_user' => $userid));
                foreach ($records as $record) {
                    if (strpos($record->hash, "@gradepool{$courseid}@") !== false) {
                        $DB->delete_records('auth_mumie_id_hashes', array('the_user' => $userid, 'hash' => $record->hash));
                        $DB->delete_records('auth_mumie_sso_tokens', array('the_user' => $record->hash));
                    }
                }
            } else if ($context->contextlevel == CONTEXT_USER) {
                $records = $DB->get_records('auth_mumie_id_hashes', array('the_user' => $userid));
                foreach ($records as $record) {
                    $DB->delete_records('auth_mumie_id_hashes', array('the_user' => $userid));
                    $DB->delete_records('auth_mumie_sso_tokens', array('the_user' => $record->hash));
                }
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
            $records = $DB->get_records('auth_mumie_id_hashes', array('the_user', $userid));
                foreach ($records as $record) {
                    $DB->delete_records('auth_mumie_id_hashes', array('the_user', $userid));
                    $DB->delete_records('auth_mumie_sso_tokens', array('the_user', $record->hash));
                }
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $courseid = $context->__get("instanceid");
            $sql = "SELECT * FROM {auth_mumie_id_hashes} WHERE 'hash' LIKE = ':gradepool'";
            $DB->get_records_sql($sql, array("gradepool" => "@gradepool{$courseid}@"));
            foreach ($records as $record) {
                $DB->delete_records('auth_mumie_id_hashes', array('the_user', $userid));
                $DB->delete_records('auth_mumie_sso_tokens', array('the_user', $record->hash));
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

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_USER) {
                continue;
            } else if ($context->contextlevel == CONTEXT_COURSE) {

            }
        }
    }

    private static function delete_user_in_course_context(\context $context) {
        $courseid = $context->__get("instanceid");
        $userid = $contextlist->get_user()->id;
        $records = $DB->get_records('auth_mumie_id_hashes', array('the_user' => $userid));
        foreach ($records as $record) {
            if (strpos($record->hash, "@gradepool{$courseid}@") !== false) {
                $DB->delete_records('auth_mumie_id_hashes', array('the_user' => $userid, 'hash' => $record->hash));
                $DB->delete_records('auth_mumie_sso_tokens', array('the_user' => $record->hash));
            }
        }
    }
}