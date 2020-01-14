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
 * @copyright  2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");

global $DB;

header('Content-Type:application/json');

if (!data_submitted()) {
    print_error("Method Not Allowed or Bad Request");
    exit(0);
}

$token = required_param('token', PARAM_ALPHANUM);
$userid = required_param('userId', PARAM_RAW);

$table = "auth_mumie_sso_tokens";

$mumietoken = $DB->get_record($table, array('the_user' => $userid, 'token' => $token));

if (strlen($userid) >= 128) {
    $moodleuserid = $DB->get_record("auth_mumie_id_hashes", array('hash' => $userid))->the_user;
} else {
    $moodleuserid = $userid;
}
$response = new \stdClass();
$user = $DB->get_record('user', array('id' => $moodleuserid));
if ($mumietoken != null && $user != null) {
    $current = time();
    if (($current - $mumietoken->timecreated) >= 60) {
        $response->status = "invalid";
    } else {
        $response->status = "valid";
        $response->userid = $user->id;

        if (get_config('auth_mumie', 'userdata_firstname')) {
            $response->firstname = $user->firstname;
        }
        if (get_config('auth_mumie', 'userdata_lastname')) {
            $response->lastname = $user->lastname;
        }
        if (get_config('auth_mumie', 'userdata_mail')) {
            $response->email = $user->email;
        }
    }
} else {
    $response->status = "invalid";
}

echo json_encode($response);
