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
require_once($CFG->dirroot . '/auth/mumie/classes/sso/sso_service.php');

require_login();

global $DB, $USER;
$id = optional_param('id', 0, PARAM_INT); // Course Module ID.

$mumietask = $DB->get_record("mumie", ['id' => $id]);

sso_service::sso($USER->id, $mumietask);
