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
 * This class is a service providing functionalities regarding hashing/masking of moodle ids.
 * Hashes are used as user id on MUMIE servers.
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mumie\hash;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/auth/mumie/classes/sso/hash/mumie_id_hash.php');
require_once($CFG->dirroot . '/auth/mumie/lib.php');

/**
 * This class is a service providing functionalities regarding hashing/masking of moodle ids.
 * Hashes are used as user id on MUMIE servers.
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hashing_service {
    public static function generate_hash($user, $mumietask) : mumie_id_hash {
        $mumieidhash = new mumie_id_hash($user, self::get_hash_with_suffix($user, $mumietask));
        $mumieidhash->save();
        return $mumieidhash;
    }

    private static function get_hash_with_suffix($user, $mumietask) : string {
        $hash = auth_mumie_get_hashed_id($user);
        if ($mumietask->privategradepool) {
            $hash .= '@gradepool' . $mumietask->course . '@';
        }
        return $hash;
    }

}
