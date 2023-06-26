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
 * This class provides service functionalities for mumie_users
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mumie\user;

defined('MOODLE_INTERNAL') || die();

use auth_mumie\hash\hashing_service;
use auth_mumie\hash\mumie_id_hash;

require_once($CFG->dirroot . '/auth/mumie/classes/sso/hash/hashing_service.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/user/mumie_user.php');

/**
 * This class provides service functionalities for mumie_users
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumie_user_service {
    public static function get_user($moodleid, $mumietask) : mumie_user {
        if (self::use_id_masking($mumietask)) {
            $mumieid = hashing_service::generate_hash($moodleid, $mumietask)->get_hash();
        } else {
            $mumieid = $moodleid;
        }
        return new mumie_user($moodleid, $mumieid);
    }

    public static function get_from_mumie_user(string $mumieid) : mumie_user {
        if (self::is_mumie_id_masked($mumieid)) {
            $moodleid = mumie_id_hash::find_by_hash($mumieid)->get_user();
        } else {
            $moodleid = (int) $mumieid;
        }
        return new mumie_user($moodleid, $mumieid);
    }

    private static function use_id_masking(mixed $mumietask) : bool {
        return isset($mumietask->use_hashed_id) && $mumietask->use_hashed_id == 1;
    }

    private static function is_mumie_id_masked(string $mumieid) : bool {
        return strlen($mumieid) >= 128;
    }
}
