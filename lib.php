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
 * A library of functions and constants for the MUMIE auth plugin
 *
 * @package auth_mumie
 * @copyright  2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get complete url for single sign in to MUMIE server
 *
 * @param stdClass $mumietask
 * @return string login url
 */
function auth_mumie_get_login_url($mumietask) {
    return $mumietask->server . 'public/xapi/auth/sso/login';
}

/**
 * Get complete url for single sign out from MUMIE server
 *
 * @param stdClass $mumietask
 * @return string logout url
 */
function auth_mumie_get_logout_url($mumietask) {
    return $mumietask->server . 'public/xapi/auth/sso/logout';
}

/**
 * Get complete url to the problem on MUMIE server
 *
 * @param stdClass $mumietask
 * @return string login url
 */
function auth_mumie_get_problem_url($mumietask) {
    return $mumietask->server . $mumietask->taskurl;
}

/**
 * Generate a randomized token for single sign in to MUMIE servers
 *
 * @param int $length word length of the token
 * @return string token
 */
function auth_mumie_get_token($length) {
    $token = "";
    $codealphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codealphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codealphabet .= "0123456789";
    $max = strlen($codealphabet) - 1;

    for ($i = 0; $i < $length; $i++) {
        $token .= $codealphabet[rand(0, $max)];
    }

    return $token;
}

/**
 * Get mumieserver_form as a fragment
 *
 * @param stdClass $args context and formdata
 * @return string html code necessary to display mumieserver form as fragment
 */
function auth_mumie_output_fragment_new_mumieserver_form($args) {
    global $CFG;
    require_once ($CFG->dirroot . '/auth/mumie/mumieserver_form.php');

    $args = (object) $args;

    $context = $args->context;
    $o = '';

    $formdata = [];
    if (!empty($args->jsonformdata)) {
        $serialiseddata = json_decode($args->jsonformdata);
        parse_str($serialiseddata, $formdata);
    }
    $mumieserver = new stdClass();

    require_capability('auth/mumie:addserver', $context);

    $editoroptions = [
        'maxfiles' => EDITOR_UNLIMITED_FILES,
        'maxbytes' => $course->maxbytes,
        'trust' => false,
        'context' => $context,
        'noclean' => true,
        'subdirs' => false,
    ];

    $mumieserver = file_prepare_standard_editor(
        $mumieserver,
        'description',
        $editoroptions,
        $context,
        'mumieserver',
        'description',
        null
    );

    $mform = new mumieserver_form(null, array('editoroptions' => $editoroptions), 'post', '', null, true, $formdata);

    $mform->set_data($mumieserver);

    if (!empty($args->jsonformdata) && strcmp($args->jsonformdata, "{}") !== 0) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    ob_start();
    $mform->display();
    $o .= ob_get_contents();
    ob_end_clean();

    return $o;
}

/**
 * Get a hashed string from the moodle user id.
 *
 * Some institutions use personal data (like matriculation numbers) as user id in moodle.
 * We need to pseudonymize the id to improve data protection.
 * We use the first 10 characters of the xapi-key as salt to further increase security.
 *
 * @param string $id userId that should be hashed
 * @return string Hashed string with 128 characters
 */
function auth_mumie_get_hashed_id($id) {
    return hash("sha512", $id . substr(get_config('auth_mumie', 'mumie_api_key'), 0, 10));
}