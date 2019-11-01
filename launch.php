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
 * @copyright  2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_mumie;
require_once ("../../config.php");
require_once ($CFG->dirroot . '/auth/mumie/lib.php');
require_login();

global $DB, $USER;
$id = optional_param('id', 0, PARAM_INT); // Course Module ID.
$embedded = optional_param("embedded", false, PARAM_BOOL);

$mumietask = $DB->get_record("mumie", array('id' => $id));
$ssotoken = new \stdClass();
$ssotoken->token = auth_mumie_get_token(20);
$ssotoken->the_user = $mumietask->use_encrypted_id == 1 ? auth_mumie_get_hashed_id($USER->id) : $USER->id;
$ssotoken->timecreated = time();

$loginurl = auth_mumie_get_login_url($mumietask);

$tokentable = "auth_mumie_sso_tokens";

$org = get_config("auth_mumie", "mumie_org");

if ($oldtoken = $DB->get_record($tokentable, array("the_user" => $ssotoken->the_user))) {
    $ssotoken->id = $oldtoken->id;
    $DB->update_record($tokentable, $ssotoken);
} else {
    $DB->insert_record($tokentable, (array) $ssotoken);
}

$problemurl = auth_mumie_get_problem_url($mumietask);

echo
    "
    <form id='mumie_sso_form' name='mumie_sso_form' method='post' action='{$loginurl}'>
        <input type='hidden' name='userId' id='userId' type ='text' value='{$USER->id}'/>
        <input type='hidden' name='token' id='token' type ='text' value='{$ssotoken->token}'/>
        <input type='hidden' name='org' id='org' type ='text' value='{$org}'/>
        <input type='hidden' name='resource' id='resource' type ='text' value='{$problemurl}'/>
    </form>
    <script>
    document.forms['mumie_sso_form'].submit();
    </script>
    ";