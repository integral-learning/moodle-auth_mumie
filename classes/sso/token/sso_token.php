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
 * This class represents a db entity in the 'auth_mumie_sso_tokens' table.
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mumie\token;

/**
 * This class represents a db entity in the 'auth_mumie_sso_tokens' table.
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sso_token {
    /**
     * Name of the database table
     */
    const SSO_TOKEN_TABLE = "auth_mumie_sso_tokens";

    /**
     * @var int
     */
    private int $id;
    /**
     * @var string
     */
    private string $token;
    /**
     * @var string
     */
    private string $user;
    /**
     * @var int
     */
    private int $timecreated;

    /**
     * Create a new instance
     * @param string $token
     * @param string $user
     * @param int    $timecreated
     */
    public function __construct(string $token, string $user, int $timecreated) {
        $this->token = $token;
        $this->user = $user;
        $this->timecreated = $timecreated;
    }

    /**
     * Create a new db entry
     * @return void
     * @throws \dml_exception
     */
    public function create(): void {
        global $DB;
        $DB->insert_record(
            self::SSO_TOKEN_TABLE,
            [
                "the_user" => $this->user,
                "token" => $this->token,
                "timecreated" => $this->timecreated,
            ]);
    }

    /**
     * Update existing db entry
     * @return void
     * @throws \dml_exception
     */
    public function update(): void {
        global $DB;
        $DB->update_record(
            self::SSO_TOKEN_TABLE,
            [
                "the_user" => $this->user,
                "token" => $this->token,
                "timecreated" => $this->timecreated,
                "id" => $this->id,
            ]);
    }

    /**
     * Find db entry matching a given user
     * @param string $user
     * @return sso_token|null
     * @throws \dml_exception
     */
    public static function find_by_user(string $user): ?sso_token {
        global $DB;
        $record = $DB->get_record(self::SSO_TOKEN_TABLE, ["the_user" => $user]);
        return self::from_record($record);
    }

    /**
     * Create instance from db record
     * @param \stdClass $record
     * @return sso_token|null
     */
    private static function from_record($record): ?sso_token {
        if (!$record) {
            return null;
        }
        $token = new sso_token($record->token, $record->the_user, $record->timecreated);
        $token->set_id($record->id);
        return $token;
    }

    /**
     * Get the id
     * @return int
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Set the id
     * @param int $id
     */
    public function set_id(int $id): void {
        $this->id = $id;
    }

    /**
     * Get the token
     * @return string
     */
    public function get_token(): string {
        return $this->token;
    }

    /**
     * Set the token
     * @param string $token
     */
    public function set_token(string $token): void {
        $this->token = $token;
    }

    /**
     * Get the user.
     * @return string
     */
    public function get_user(): string {
        return $this->user;
    }

    /**
     * Set the user
     * @param string $user
     */
    public function set_user(string $user): void {
        $this->user = $user;
    }

    /**
     * Get time created
     * @return int
     */
    public function get_timecreated(): int {
        return $this->timecreated;
    }

    /**
     * Set time created
     * @param int $timecreated
     */
    public function set_timecreated(int $timecreated): void {
        $this->timecreated = $timecreated;
    }
}
