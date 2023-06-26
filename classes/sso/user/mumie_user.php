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
 * This class represents a joined moodle and MUMIE user.
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mumie\user;

/**
 * This class represents a joined moodle and MUMIE user.
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumie_user {
    private int $moodleid;
    private string $mumieid;
    private string $firstname;
    private string $lastname;
    private string $email;

    /**
     * @param int    $moodleid
     * @param string $mumieid
     */
    public function __construct(int $moodleid, string $mumieid) {
        $this->moodleid = $moodleid;
        $this->mumieid = $mumieid;
    }

    public function load() {
        global $DB;
        $user = $DB->get_record('user', array('id' => $this->moodleid));
        if (!$user) {
            return;
        }
        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->email = $user->email;
    }

    /**
     * @return int
     */
    public function get_moodle_id() : int {
        return $this->moodleid;
    }

    /**
     * @param int $moodleid
     */
    public function set_moodle_id(int $moodleid) : void {
        $this->moodleid = $moodleid;
    }

    /**
     * @return string
     */
    public function get_mumie_id() : string {
        return $this->mumieid;
    }

    /**
     * @param string $mumieid
     */
    public function set_mumie_id(string $mumieid) : void {
        $this->mumieid = $mumieid;
    }

    /**
     * @return string
     */
    public function get_firstname() : string {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function get_lastname() : string {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function get_email() : string {
        return $this->email;
    }
}
