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
 * This class provides service functionalities related to sso_tokens.
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_mumie\token;

defined('MOODLE_INTERNAL') || die();

use auth_mumie\user\mumie_user;

require_once($CFG->dirroot . '/auth/mumie/classes/sso/token/sso_token.php');

/**
 * This class provides service functionalities related to sso_tokens.
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class token_service {
    /**
     * Generate a new SSO Token for a given user
     * @param mumie_user $user
     * @return sso_token
     * @throws \dml_exception
     */
    public static function generate_sso_token(mumie_user $user): sso_token {
        if ($token = sso_token::find_by_user($user->get_mumie_id())) {
            $token->set_token(self::generate_token());
            $token->set_timecreated(time());
            $token->update();
        } else {
            $token = new sso_token(self::generate_token(), $user->get_mumie_id(), time());
            $token->create();
        }
        return $token;
    }

    /**
     * Check whether a given token is valid
     * @param mumie_user|null $user
     * @param string          $token
     * @return bool
     * @throws \dml_exception
     */
    public static function is_token_valid(?mumie_user $user, string $token): bool {
        if ($user == null) {
            return false;
        }
        $ssotoken = sso_token::find_by_user($user->get_mumie_id());
        return $ssotoken != null
            && $ssotoken->get_token() == $token
            && !self::has_token_timed_out($ssotoken);
    }

    /**
     * Check whether an existing token has timed out
     * @param sso_token $token
     * @return bool
     */
    private static function has_token_timed_out(sso_token $token): bool {
        $current = time();
        return $current - $token->get_timecreated() > 60;
    }

    /**
     * Generate a new token value
     * @return string
     */
    private static function generate_token(): string {
        return auth_mumie_get_token(20);
    }
}
