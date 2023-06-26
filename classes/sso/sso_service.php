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
 * This class is is the main service used to handle SSO logic when opening MUMIE Tasks
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mumie;

defined('MOODLE_INTERNAL') || die();

use auth_mumie\user\mumie_user_service;
use auth_mumie\token\token_service;
use auth_mumie\token\sso_token;

require_once($CFG->dirroot . '/auth/mumie/classes/sso/user/mumie_user_service.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/token/token_service.php');
require_once($CFG->dirroot . '/mod/mumie/lib.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/launch_form_builder.php');

/**
 * This class is is the main service used to handle SSO logic when opening MUMIE Tasks
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sso_service {
    const WORKSHEET_PREFIX = "worksheet_";
    public static function sso($moodleid, $mumietask) : void {
        $mumieuser = mumie_user_service::get_user($moodleid, $mumietask);
        $ssotoken = token_service::generate_sso_token($mumieuser);
        $deadline = mumie_get_effective_duedate($moodleid, $mumietask);
        echo self::get_launch_form($ssotoken, $mumietask, $deadline);
    }

    private static function get_launch_form(sso_token $token, $mumietask, $deadline) : string {
        $launchformbuilder = new launch_form_builder($token, $mumietask);

        $problempath = auth_mumie_get_problem_path($mumietask);
        if (self::include_signed_deadline($problempath, $deadline)) {
            $launchformbuilder->with_deadline($deadline);
        }
        return $launchformbuilder->build();
    }

    private static function include_signed_deadline(string $problempath, $deadline) : bool {
        return str_starts_with($problempath, self::WORKSHEET_PREFIX)
            && $deadline > 0;
    }
}
