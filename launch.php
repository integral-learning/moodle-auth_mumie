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
 * Enables single sign on and single sign out with MUMIE servers
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_mumie;

use auth_mumie\hash\hashing_service;
use auth_mumie\token\token_service;
use auth_mumie\user\mumie_user_service;

const WORKSHEET_PREFIX = "worksheet_";
require_once("../../config.php");
require_once($CFG->dirroot . '/auth/mumie/lib.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/launch_form_builder.php');
require_once($CFG->dirroot . '/auth/mumie/classes/cryptography/mumie_cryptography_service.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/user/mumie_user_service.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/token/token_service.php');
require_once($CFG->dirroot . '/auth/mumie/classes/sso/token/sso_token.php');
require_once($CFG->dirroot . '/mod/mumie/lib.php');
require_login();

global $DB, $USER;
$id = optional_param('id', 0, PARAM_INT); // Course Module ID.

$mumietask = $DB->get_record("mumie", array('id' => $id));

$mumieuser = mumie_user_service::get_user($USER->id, $mumietask);
$ssotoken = token_service::generate_sso_token($mumieuser);

$problempath = auth_mumie_get_problem_path($mumietask);

$launchformbuilder = new launch_form_builder($ssotoken, $mumietask);
$deadline = mumie_get_effective_duedate($USER->id, $mumietask);
if (include_deadline_signature($problempath, $deadline)) {
    $launchformbuilder->with_deadline($deadline);
}
echo $launchformbuilder->build();

function include_deadline_signature(string $problempath, $deadline) : bool {
    return str_starts_with($problempath, WORKSHEET_PREFIX)
        && $deadline > 0;
}
