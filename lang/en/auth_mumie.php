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
 * @copyright  2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['pluginname'] = 'MUMIE Single Sign On';

$string['mumie_shared_user_data'] = 'Shared User Data';
$string['mumie_shared_user_data_desc'] = 'Choose which user data other than the id should be shared with MUMIE servers';

$string['mumie_firstname'] = 'Firstname';
$string['mumie_lastname'] = 'Lastname';

$string['mumie_api_key'] = 'MUMIE API Key';
$string['mumie_api_key_desc'] = 'Specify the MUMIE API key for grade synchronization ';

$string['mumie_org'] = 'MUMIE Org';
$string['mumie_org_desc'] = 'Specify the MUMIE organization key, i.e "rwth" ';