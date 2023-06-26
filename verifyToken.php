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
 * This script is used by external MUMIE servers to verify SSO tokens
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use auth_mumie\user\mumie_user_service;
use auth_mumie\token\token_service;

require_once("../../config.php");
require_once($CFG->dirroot . "/auth/mumie/classes/sso/user/mumie_user_service.php");
require_once($CFG->dirroot . "/auth/mumie/classes/sso/token/token_service.php");

header('Content-Type:application/json');

$token = required_param('token', PARAM_ALPHANUM);
$mumieid = required_param('userId', PARAM_RAW);
$user = mumie_user_service::get_from_mumie_user($mumieid);

$user->load();
$response = new \stdClass();
if (token_service::is_token_valid($user, $token)) {
    $response->status = "valid";
    if (get_config('auth_mumie', 'userdata_firstname')) {
        $response->firstname = $user->get_firstname();
    }
    if (get_config('auth_mumie', 'userdata_lastname')) {
        $response->lastname = $user->get_lastname();
    }
    if (get_config('auth_mumie', 'userdata_mail')) {
        $response->email = $user->get_email();
    }
} else {
    $response->status = "invalid";
}

echo json_encode($response);
