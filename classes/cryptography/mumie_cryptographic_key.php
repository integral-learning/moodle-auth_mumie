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
 * This file defines a class representing a cryptographic key stored in the database
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This class is representing a cryptographic key stored in the database
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumie_cryptographic_key {
    /**
     * Name of table used to store keys
     */
    const MUMIE_CRYPTOGRAPHIC_KEY_TABLE = "auth_mumie_cryptographic_key";
    /**
     * @var string
     */
    private string $id;
    /**
     * @var string
     */
    private string $name;
    /**
     * @var string
     */
    private string $key_value;

    /**
     * Create a new instance
     * @param string $name
     * @param string $key_value
     */
    public function __construct(string $name, string $key_value) {
        $this->name = $name;
        $this->key_value = $key_value;
    }

    /**
     * Insert a new key_value into the database
     * @return void
     * @throws dml_exception
     */
    public function create() {
        global $DB;
        $DB->insert_record(self::MUMIE_CRYPTOGRAPHIC_KEY_TABLE, ["name" => $this->name, "key_value" => $this->key_value]);
    }

    /**
     * Update an existing database entry
     * @return void
     * @throws dml_exception
     */
    public function update() {
        global $DB;
        $DB->update_record(self::MUMIE_CRYPTOGRAPHIC_KEY_TABLE, ["name" => $this->name, "key_value" => $this->key_value, "id" => $this->id]);
    }

    /**
     * Find a database entry by name
     * @param string $name
     * @return mumie_cryptographic_key|null
     * @throws dml_exception
     */
    public static function get_by_name(string $name) : ?mumie_cryptographic_key {
        global $DB;
        $record = $DB->get_record(self::MUMIE_CRYPTOGRAPHIC_KEY_TABLE, ["name" => $name]);
        return self::from_record($record);
    }

    /**
     * Create a class instance from a record
     * @param mixed $record
     * @return mumie_cryptographic_key|null
     */
    private static function from_record($record) : ?mumie_cryptographic_key {
        if (!$record) {
            return null;
        }
        $cryptokey = new mumie_cryptographic_key($record->name, $record->key_value);
        $cryptokey->set_id($record->id);
        return $cryptokey;
    }

    /**
     * Get the id
     * @return string
     */
    public function get_id() : string {
        return $this->id;
    }

    /**
     * Set a new id
     * @param string $id
     */
    public function set_id(string $id) : void {
        $this->id = $id;
    }

    /**
     * Get the name
     * @return string
     */
    public function get_name() : string {
        return $this->name;
    }

    /**
     * Set a new name
     * @param string $name
     */
    public function set_name(string $name) : void {
        $this->name = $name;
    }

    /**
     * Get the key_value
     * @return string
     */
    public function get_key_value() : string {
        return $this->key_value;
    }

    /**
     * Set a new key_value
     * @param string $key_value
     */
    public function set_key_value(string $key_value) : void {
        $this->key_value = $key_value;
    }
}
