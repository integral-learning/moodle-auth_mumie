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

require_once("../../config.php");
require_once($CFG->dirroot . '/auth/mumie/lib.php');
require_login();

global $DB, $USER;
$id = optional_param('id', 0, PARAM_INT); // Course Module ID.
$embedded = optional_param("embedded", false, PARAM_BOOL);

$mumietask = $DB->get_record("mumie", array('id' => $id));
$ssotoken = new \stdClass();
$ssotoken->token = auth_mumie_get_token(20);

if (isset($mumietask->use_hashed_id) && $mumietask->use_hashed_id == 1) {
    $hashidtable = "auth_mumie_id_hashes";
    $hash = auth_mumie_get_hashed_id($USER->id);
    if ($mumietask->privategradepool) {
        $hash .= '@gradepool' . $mumietask->course . '@';
    }
    $ssotoken->the_user = $hash;
    $row = new \stdClass();
    $row->hash = $hash;
    $row->the_user = $USER->id;
    if ($oldrecord = $DB->get_record($hashidtable, array("the_user" => $USER->id, "hash" => $row->hash))) {
        $row->id = $oldrecord->id;
        $DB->update_record($hashidtable, $row);
    } else {
        $DB->insert_record($hashidtable, (array) $row);
    }
} else {
    $ssotoken->the_user = $USER->id;
}
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
$problempath = auth_mumie_get_problem_path($mumietask);

echo
    "
    <form id='mumie_sso_form' name='mumie_sso_form' method='post' action='{$loginurl}'>
        <input type='hidden' name='userId' id='userId' type ='text' value='{$ssotoken->the_user}'/>
        <input type='hidden' name='token' id='token' type ='text' value='{$ssotoken->token}'/>
        <input type='hidden' name='org' id='org' type ='text' value='{$org}'/>
        <input type='hidden' name='resource' id='resource' type ='text' value='{$problemurl}'/>
        <input type='hidden' name='path' id='resource' type ='text' value='{$problempath}'/>
        <input type='hidden' name='lang' id='resource' type ='text' value='{$mumietask->language}'/>
    </form>
    <script>
    document.forms['mumie_sso_form'].submit();
    </script>
    ";
