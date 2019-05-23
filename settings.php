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
 * This file defines the global auth_mumie settingspage.
 *
 * @package auth_mumie
 * @copyright  2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext(
        'auth_mumie/mumie_api_key',
        get_string('mumie_api_key', 'auth_mumie'),
        get_string('mumie_api_key_desc', 'auth_mumie'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'auth_mumie/mumie_org',
        get_string('mumie_org', 'auth_mumie'),
        get_string('mumie_org_desc', 'auth_mumie'),
        '',
        PARAM_URL
    ));

    // Header for shared user data.
    $settings->add(new admin_setting_heading('userdata',
        get_string('mumie_shared_user_data', 'auth_mumie'),
        get_string('mumie_shared_user_data_desc', 'auth_mumie')));

    $settings->add(new admin_setting_configcheckbox('auth_mumie/userdata_firstname',
        get_string('mumie_firstname', 'auth_mumie'), '', 0));

    $settings->add(new admin_setting_configcheckbox('auth_mumie/userdata_lastname',
        get_string('mumie_lastname', 'auth_mumie'), '', 0));
}
