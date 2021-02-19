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
 * Strings for auth_mumie
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['pluginname'] = 'MUMIE Single Sign On';

$string['mumie_shared_user_data'] = 'Shared User Data';
$string['mumie_shared_user_data_desc'] = 'Choose which user data should be shared with MUMIE servers';

$string['mumie_firstname'] = 'Firstname';
$string['mumie_lastname'] = 'Lastname';
$string['mumie_mail'] = 'Email';

$string['mumie_api_key'] = 'MUMIE API Key';
$string['mumie_api_key_desc'] = 'Specify the MUMIE API key for grade synchronization ';

$string['mumie_org'] = 'MUMIE Org';
$string['mumie_org_desc'] = 'Specify the MUMIE organization key, i.e "rwth" ';

// Used in settings.php.
$string['mumie_table_header_name'] = 'Server name';
$string['mumie_table_header_url'] = 'URL prefix';
$string['mumie_server_list_heading'] = 'Configured MUMIE servers';
$string['mumie_edit_button'] = 'Edit';
$string['mumie_delete_button'] = 'Delete';
$string['mumie_add_server_button'] = 'Add MUMIE Server';
$string['mumie_authentication_header'] = 'Authentication & Authorization';
$string['mumie_authentication_header_desc'] = 'These settings are used to authenticate users and authorize requests';
$string['mumie_task_admin_header'] = 'MUMIE Task settings';
$string['mumie_task_admin_header_desc'] = 'These settings apply to the MUMIE Task plugin';
$string['admin_gradepool_selection'] = 'Share grades between courses';
$string['admin_gradepool_description'] = 'Choose whether to share grades for MUMIE problems between courses. You can leave this decision to the teachers.
<br>
<br>If sharing is enabled, points that were earned for MUMIE problems in one course will be automatically synchronized with other courses\' gradebooks that have grade synchronization enabled as well.
<br>If not, courses will neither be able to import nor to export grades.
<br><br><b>Changing this value does not affect existing MUMIE Tasks or courses containing MUMIE Tasks!</b>
';
$string['admin_gradepool_free_choice_option'] = 'Leave decision to teachers';
$string['admin_gradepool_private_option'] = 'Do not share grades';
$string['admin_gradepool_shared_option'] = 'Share grades';
$string['mumie_dev_options_heading'] = 'Developer options';
$string['mumie_dev_options_description'] = 'These settings are for developers only. No changes required!';
$string['mumie_problem_selector_url'] = 'Problem selector URL';
$string['mumie_problem_selector_url_description'] = 'Lemon instance that should be used to select problems';

// Used in mumieserver form.
$string['mumie_form_required'] = 'required';
$string['mumie_form_server_not_existing'] = 'There is no MUMIE server for this URL';
$string['mumie_form_already_existing_config'] = 'There is already a configuration for this URL prefix';
$string['mumie_form_already_existing_name'] = 'There is already a configuration for this name';
$string['mumie_form_title'] = 'Configure MUMIE Server';
$string['mumie_form_server_config'] = 'MUMIE server configuration';
$string['mumie_server_name'] = 'Server name';
$string['mumie_server_name_help'] = 'Please insert a unique name for this configuration.';
$string['mumie_form_server_btn_submit'] = 'Submit';
$string['mumie_form_server_btn_cancel'] = 'Cancel';
$string['mumie_url_prefix'] = 'MUMIE URL Prefix';
$string['mumie_url_prefix_help'] = 'Specify the MUMIE URL prefix  <br><br> e.g. <b>https://www.ombplus.de/ombplus</b> <br><br> There can only be a single configuration for any URL prefix.';

// Capabilities.
$string['mumie:addserver'] = 'Add a new MUMIE server to MOODLE';
$string['mumie:deleteserver'] = 'Remove a MUMIE server from MOODLE';
$string['mumie:viewavailablecourses'] = 'View all MUMIE servers, courses and tasks that are available on this MOODLE platform';

// Privacy.
$string['privacy:metadata:auth_mumie_tokens:token'] = 'Token used to verify a user\'s Single Sign On attempt to MUMIE/Lemon servers.';
$string['privacy:metadata:auth_mumie_tokens:hash'] = 'The hashed moodle user ID used to identify the user on MUMIE/Lemon servers.';
$string['privacy:metadata:auth_mumie_tokens:timecreated'] = 'The timestamp of the user\'s last Single Sign On attempt';
$string['privacy:metadata:auth_mumie_hashes:userid'] = 'Moodle user ID';
$string['privacy:metadata:auth_mumie_servers:firstname'] = 'The user\'s first name';
$string['privacy:metadata:auth_mumie_servers:lastname'] = 'The user\'s last name';
$string['privacy:metadata:auth_mumie_servers:email'] = 'The user\'s e-mail address';
$string['privacy:metadata:auth_mumie_tokens:tableexplanation'] = 'Details of SSO tokens used to log into MUMIE/Lemon servers';
$string['privacy:metadata:auth_mumie_hashes:tableexplanation'] = 'Details of hash lookup table for moodle user IDs';
$string['privacy:metadata:auth_mumie_servers:tableexplanation'] = 'Details of optional sharing of personal data with MUMIE/Lemon servers';