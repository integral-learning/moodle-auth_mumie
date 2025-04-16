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

namespace auth_mumie;

use auth_mumie\privacy\provider;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\userlist;
use core_user;
use context_course;
use context_user;
use context_system;

/**
 * PHPUnit tests for the privacy provider in auth_mumie.
 *
 * @package    auth_mumie
 * @copyright  2017-2025 integral-learning GmbH (https://www.integral-learning.de/)
 * @author     Tobias Goltz <tobias.goltz@integral-learning.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \auth_mumie\privacy\provider
 */
final class privacy_provider_test extends \core_privacy\tests\provider_testcase {

        /**
         * Test: get_contexts_for_userid returns no data for user without logins.
         *
         * @covers ::get_contexts_for_userid
         */
    public function test_get_contexts_for_userid_no_data(): void {
        global $USER;
        $this->resetAfterTest(true);

        $this->setAdminUser();
        $contextlist = provider::get_contexts_for_userid($USER->id);
        $this->assertCount(0, $contextlist);
    }

    /**
     * Test: get_contexts_for_userid returns expected contexts for various users.
     *
     * @covers ::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid(): void {
        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->create_login($user1);
        $this->create_login($user2, $course1);
        $this->create_login($user2, $course2);

        // There should be one user context.
        $contextlist1 = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(1, $contextlist1);

        // There should be two course and one user context.
        $contextlist2 = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(3, $contextlist2);

        // There should be no contexts for a user that hasn't logged in.
        $contextlist3 = provider::get_contexts_for_userid($user3->id);
        $this->assertEmpty($contextlist3);
    }

    /**
     * Test: get_users_in_context returns correct user lists.
     *
     * @covers ::get_users_in_context
     */
    public function test_get_users_in_context(): void {
        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->create_login($user1, $course1);
        $this->create_login($user1, $course2);
        $this->create_login($user2, $course1);
        $this->create_login($user3, $course1);

        $coursectx1 = \context_course::instance($course1->id);
        $coursectx2 = \context_course::instance($course2->id);
        $coursectx3 = \context_course::instance($course3->id);

        $userlist1 = new \core_privacy\local\request\userlist($coursectx1, 'core_course');
        provider::get_users_in_context($userlist1);
        // We expect all 3 users to have personal data within course1.
        $this->assertCount(3, $userlist1->get_userids());

        $userlist2 = new \core_privacy\local\request\userlist($coursectx2, 'core_course');
        provider::get_users_in_context($userlist2);
        // We expect one user to have personal data within course2.
        $this->assertCount(1, $userlist2->get_userids());

        $userlist3 = new \core_privacy\local\request\userlist($coursectx3, 'core_course');
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3->get_userids());
    }

    /**
     * Test: export_user_data returns nothing if no data exists.
     *
     * @covers ::export_user_data
     */
    public function test_export_user_data_no_data(): void {
        $this->resetAfterTest(true);

        $user1 = $this->getDataGenerator()->create_user();

        $approvedcontextlist1 = new \core_privacy\tests\request\approved_contextlist(
        \core_user::get_user($user1->id),
        'core_course',
        []
        );
        provider::export_user_data($approvedcontextlist1);
        $writer = writer::with_context(\context_system::instance());
        $this->assertFalse($writer->has_any_data_in_any_context());

        $approvedcontextlist2 = new \core_privacy\tests\request\approved_contextlist(
        \core_user::get_user($user1->id),
        'core_user',
        []
        );
        provider::export_user_data($approvedcontextlist2);
        $writer = writer::with_context(\context_system::instance());
        $this->assertFalse($writer->has_any_data_in_any_context());
    }

    /**
     * Test: delete_data_for_user deletes correct records.
     *
     * @covers ::delete_data_for_user
     */
    public function test_delete_data_for_user(): void {
        global $DB;
        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->create_login($user1, $course1);
        $this->create_login($user1, $course2);
        $this->create_login($user2, $course1);

        $contextlist = new approved_contextlist(
        $user1,
        'core_course',
        [
            \context_course::instance($course1->id)->id,
            \context_course::instance($course2->id)->id,
        ]
        );
        provider::delete_data_for_user($contextlist);

        $this->assertEquals([$user2->id], $DB->get_fieldset_select('auth_mumie_id_hashes', 'the_user', ''));
        $this->assertCount(1, $DB->get_records('auth_mumie_sso_tokens', []));

        $this->create_login($user3, $course1);
        $this->create_login($user3, $course2);
        $this->create_login($user3);

        $contextlist = new approved_contextlist(
        $user3,
        'core_user',
        [context_user::instance($user3->id)->id]
        );
        provider::delete_data_for_user($contextlist);

        $this->assertEquals([$user2->id], $DB->get_fieldset_select('auth_mumie_id_hashes', 'the_user', ''));
        $this->assertCount(1, $DB->get_records('auth_mumie_sso_tokens', []));

    }

    /**
     * Test: delete_data_for_users deletes correct users' data.
     *
     * @covers ::delete_data_for_users
     */
    public function test_delete_data_for_users(): void {
        global $DB;
        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->create_login($user1, $course1);
        $this->create_login($user2, $course1);
        $this->create_login($user3, $course1);

        $coursectx1 = context_course::instance($course1->id);

        $approveduserlist1 = new \core_privacy\local\request\approved_userlist(
        $coursectx1,
        'core_course',
        [$user1->id, $user2->id]
        );

        provider::delete_data_for_users($approveduserlist1);
        $this->assertEquals([$user3->id], $DB->get_fieldset_select('auth_mumie_id_hashes', 'the_user', ''));
        $this->assertCount(1, $DB->get_records('auth_mumie_sso_tokens', []));

        $this->create_login($user1, $course1);
        $this->create_login($user1, $course2);
        $this->create_login($user1);
        $this->create_login($user2);

        $userctx1 = context_user::instance($user1->id);
        $approveduserlist2 = new \core_privacy\local\request\approved_userlist(
        $userctx1,
        'core_user',
        [$user1->id, $user2->id]
        );

        provider::delete_data_for_users($approveduserlist2);
        $this->assertEquals([$user3->id], $DB->get_fieldset_select('auth_mumie_id_hashes', 'the_user', ''));
        $this->assertCount(1, $DB->get_records('auth_mumie_sso_tokens', []));
    }

    /**
     * Test: delete_data_for_all_users_in_context deletes all user data for given context.
     *
     * @covers ::delete_data_for_all_users_in_context
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;
        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->create_login($user1, $course1);
        $this->create_login($user2, $course1);
        $this->create_login($user3, $course1);
        $this->create_login($user1, $course2);

        $context1 = context_course::instance($course1->id);

        provider::delete_data_for_all_users_in_context($context1);
        $this->assertEquals([$user1->id], $DB->get_fieldset_select('auth_mumie_id_hashes', 'the_user', ''));
        $this->assertCount(1, $DB->get_records('auth_mumie_sso_tokens', []));

        $this->create_login($user3);
        $this->create_login($user3, $course1);
        $userctx = context_user::instance($user3->id);
        provider::delete_data_for_all_users_in_context($userctx);
        $this->assertEquals([$user1->id], $DB->get_fieldset_select('auth_mumie_id_hashes', 'the_user', ''));
        $this->assertCount(1, $DB->get_records('auth_mumie_sso_tokens', []));
    }

    /**
     * Create login data for a user. If a course is specified, a gradepool hash is added.
     *
     * @param \stdClass $user   The user
     * @param \stdClass|null $course Optional course object
     * @return void
     */
    protected function create_login($user, $course = null): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/auth/mumie/lib.php');

        $hash = auth_mumie_get_hashed_id($user->id);
        if ($course) {
            $hash .= '@gradepool' . $course->id . '@';
        }
        $idhash = new \stdClass();
        $idhash->hash = $hash;
        $idhash->the_user = $user->id;

        $DB->insert_record('auth_mumie_id_hashes', (array) $idhash);

        $ssotoken = new \stdClass();
        $ssotoken->token = auth_mumie_get_token(20);
        $ssotoken->timecreated = time();
        $ssotoken->the_user = $hash;

        $DB->insert_record('auth_mumie_sso_tokens', (array) $ssotoken);
    }

}
