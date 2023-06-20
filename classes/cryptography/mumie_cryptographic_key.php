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
class mumie_cryptographic_key {
    const MUMIE_CRYPTOGRAPHIC_KEY_TABLE = "auth_mumie_cryptographic_key";
    private string $id;
    private string $name;
    private string $key;

    /**
     * @param string $name
     * @param string $key
     */
    public function __construct(string $name, string $key) {
        $this->name = $name;
        $this->key = $key;
    }

    public function create() {
        global $DB;
        $DB->insert_record(self::MUMIE_CRYPTOGRAPHIC_KEY_TABLE, ["name" => $this->name, "key" => $this->key]);
    }

    public function update() {
        global $DB;
        $DB->update_record(self::MUMIE_CRYPTOGRAPHIC_KEY_TABLE, ["name" => $this->name, "key" => $this->key, "id" => $this->id]);
    }

    public static function get_by_name(string $name) : mumie_cryptographic_key | null {
        global $DB;
        $record = $DB->get_record(self::MUMIE_CRYPTOGRAPHIC_KEY_TABLE, ["name" => $name]);
        return self::from_record($record);
    }

    private static function from_record($record) : mumie_cryptographic_key | null {
        if (!$record) {
            return null;
        }
        $cryptokey = new mumie_cryptographic_key($record->name, $record->key);
        $cryptokey->set_id($record->id);
        return $cryptokey;
    }

    /**
     * @return string
     */
    public function get_id() : string {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function set_id(string $id) : void {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function get_name() : string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function set_name(string $name) : void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function get_key() : string {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function set_key(string $key) : void {
        $this->key = $key;
    }

}
