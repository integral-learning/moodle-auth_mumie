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
 * This class represents a DB entry in the userId -> hash lookup table auth_mumie_id_hashes.
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mumie\hash;

/**
 * This class represents a DB entry in the userId -> hash lookup table auth_mumie_id_hashes.
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumie_id_hash {
    /**
     * Name of the database table
     */
    const HASH_ID_TABLE = "auth_mumie_id_hashes";
    /**
     * @var int
     */
    private int $id;
    /**
     * @var int
     */
    private int $user;
    /**
     * @var string
     */
    private string $hash;

    /**
     * Create a new instance
     * @param int    $user
     * @param string $hash
     */
    public function __construct(int $user, string $hash) {
        $this->user = $user;
        $this->hash = $hash;
    }

    /**
     * Create a database entry, if none exists.
     * @return void
     */
    public function save() : void {
        if (!self::find($this->user, $this->hash)) {
            $this->create();
        }
    }

    /**
     * Create a new db entry
     * @return void
     * @throws \dml_exception
     */
    public function create() : void {
        global $DB;
        $DB->insert_record(self::HASH_ID_TABLE, ["the_user" => $this->user, "hash" => $this->hash]);
    }

    /**
     * Update existing db entry
     * @return void
     * @throws \dml_exception
     */
    public function update() : void {
        global $DB;
        $DB->update_record(self::HASH_ID_TABLE, ["id" => $this->id, "the_user" => $this->user, "hash" => $this->hash]);
    }

    /**
     * Find id has by moodle user id.
     * @param string $user
     * @return mumie_id_hash|null
     * @throws \dml_exception
     */
    public static function find_by_user(string $user) : ?mumie_id_hash {
        global $DB;
        $record = $DB->get_record(self::HASH_ID_TABLE, ["the_user" => $user]);
        return self::from_record($record);
    }

    /**
     * Find db entry matching a given user and hash.
     * @param int $user
     * @param string $hash
     * @return mumie_id_hash|null
     * @throws \dml_exception
     */
    private static function find(int $user, string $hash) : ?mumie_id_hash {
        global $DB;
        $record = $DB->get_record(self::HASH_ID_TABLE, ["the_user" => $user, "hash" => $hash]);
        return self::from_record($record);
    }

    /**
     * Find db entry matching a given hash.
     * @param string $hash
     * @return mumie_id_hash|null
     * @throws \dml_exception
     */
    public static function find_by_hash(string $hash) : ?mumie_id_hash {
        global $DB;
        $cache = \cache::make('auth_mumie', 'mumieidhash');
        $result = $cache->get($hash);
        if ($result === false) {
            $record = $DB->get_record(self::HASH_ID_TABLE, ["hash" => $hash]);
            $result = self::from_record($record);
            $cache->set($hash, $result);
        }
        return $result;
    }

    /**
     * Create class instance from db result.
     * @param  \stdClass $record
     * @return mumie_id_hash|null
     */
    private static function from_record($record) : ?mumie_id_hash {
        if (!$record) {
            return null;
        }
        $idhash = new mumie_id_hash($record->the_user, $record->hash);
        $idhash->set_id($record->id);
        return $idhash;
    }

    /**
     * Get the id.
     * @return int
     */
    public function get_id() : int {
        return $this->id;
    }

    /**
     * Set the id
     * @param int $id
     */
    public function set_id(int $id) : void {
        $this->id = $id;
    }

    /**
     * Get the moodle user id
     * @return int
     */
    public function get_user() : int {
        return $this->user;
    }

    /**
     * Set the moodle user id
     * @param int $user
     */
    public function set_user(int $user) : void {
        $this->user = $user;
    }

    /**
     * Get the hash
     * @return string
     */
    public function get_hash() : string {
        return $this->hash;
    }

    /**
     * Set the hash
     * @param string $hash
     */
    public function set_hash(string $hash) : void {
        $this->hash = $hash;
    }
}
