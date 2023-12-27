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
 * Opens the Problem Selector with SSO
 *
 * @package auth_mumie
 * @copyright  2017-2023 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Yannic Lapawczyk (yannic.lapawczyk@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_mumie;

use auth_mumie\token\token_service;
use auth_mumie\user\mumie_user_service;

require_once("../../config.php");
require_once($CFG->dirroot . '/auth/mumie/classes/sso/sso_service.php');

/**
 * Generates a hidden input field to send the selection when submitting the form
 *
 * @param string|null $selection The potential selection
 *
 * @return string The HTML representation of the hidden input field for the selection value
 */
function selection_input(?string $selection) : string {
    if ($selection === null) {
        return '';
    }
    return "<input type='hidden' name='selection' id='selection' type ='text' value='{$selection}'/>";
}

/**
 * Generates a form to open the problem selector and submit the form automatically
 *
 * @param \stdClass $user The user object
 * @param string $server_url The server URL
 * @param string $grading_type The grading type
 * @param string $problem_lang The problem language
 * @param string $origin The origin of the request
 * @param string|null $selection The potential selection
 *
 * @return string The HTML representation of the problem selector form
 * @throws \dml_exception
 */
function open_problem_selector(\stdClass $user, $server_url, $grading_type, $problem_lang, $origin, $selection = null) : string {
    $problem_selector_url = get_config('auth_mumie', 'mumie_problem_selector_url');
    $mumie_user = mumie_user_service::get_user($user->id);
    $sso_token = token_service::generate_sso_token($mumie_user);
    $org = get_config("auth_mumie", "mumie_org");
    $selection_input = selection_input($selection);

    return"
            <form id='mumie_problem_selector_form' name='mumie_problem_selector_form' method='post' action='{$problem_selector_url}/api/sso/problem-selector'>
                <input type='hidden' name='userId' id='userId' type ='text' value='{$sso_token->get_user()}'/>
                <input type='hidden' name='token' id='token' type ='text' value='{$sso_token->get_token()}'/>
                <input type='hidden' name='org' id='org' type ='text' value='{$org}'/>
                <input type='hidden' name='uiLang' id='uiLang' type ='text' value='{$user->lang}'/>
                <input type='hidden' name='serverUrl' id='serverUrl' type ='text' value='{$server_url}'/>
                <input type='hidden' name='gradingType' id='gradingType' type ='text' value='{$grading_type}'/>
                <input type='hidden' name='problemLang' id='problemLang' type ='text' value='{$problem_lang}'/>
                <input type='hidden' name='origin' id='origin' type ='text' value='{$origin}'/>
                {$selection_input}
            </form>
            <script>
            document.forms['mumie_problem_selector_form'].submit();
            </script>
        ";
}

require_login();

global $USER;

$server_url = required_param('server_url', PARAM_URL);
$grading_type = required_param('grading_type', PARAM_ALPHA);
$problem_lang = required_param('problem_lang', PARAM_LANG);
$origin = required_param('origin', PARAM_URL);
$context_id = required_param('context_id', PARAM_INT);
$selection = optional_param('selection', null, PARAM_STRINGID);

$context = \context::instance_by_id($context_id);
require_capability('auth/mumie:ssotoproblemselector', $context);

echo open_problem_selector($USER, $server_url, $grading_type, $problem_lang, $origin, $selection);
