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

$string['pluginname'] = 'MUMIE Single-Sign-On';

$string['mumie_shared_user_data'] = 'Nutzerdaten';
$string['mumie_shared_user_data_desc'] = 'Bestimmen Sie, welche Nutzerdaten neben der ID MUMIE-Servern zur Verfügung gestellt werden sollen';

$string['mumie_firstname'] = 'Vorname';
$string['mumie_lastname'] = 'Nachname';
$string['mumie_mail'] = 'Email-Addresse';

$string['mumie_api_key'] = 'MUMIE API Key';
$string['mumie_api_key_desc'] = 'Geben Sie den MUMIE API-Key zur Notensynchronisation an';

$string['mumie_org'] = 'MUMIE Org';
$string['mumie_org_desc'] = 'Geben Sie Ihr Organisationskürzel für MUMIE an, z.B. "rwth"';

// Used in settings.php.
$string['mumie_table_header_name'] = 'Servername';
$string['mumie_table_header_url'] = 'URL-Prefix';
$string['mumie_server_list_heading'] = 'Konfigurierte MUMIE-Server';
$string['mumie_edit_button'] = 'Bearbeiten';
$string['mumie_delete_button'] = 'Löschen';
$string['mumie_add_server_button'] = 'MUMIE-Server hinzufügen';

// Used in mumieserver form.
$string['mumie_form_required'] = 'Pflichtfeld';
$string['mumie_form_server_not_existing'] = 'Für diese URL existiert kein MUMIE-Server';
$string['mumie_form_already_existing_config'] = 'Es gibt bereits eine Serverkonfiguration für diesen URL-Prefix';
$string['mumie_form_already_existing_name'] = 'Es gibt bereits eine Serverkonfiguration für diesen Namen';
$string['mumie_form_title'] = 'MUMIE-Serverkonfiguration';
$string['mumie_form_server_config'] = 'MUMIE-Serverkonfiguration';
$string['mumie_server_name'] = 'Servername';
$string['mumie_server_name_help'] = 'Bitte wählen Sie einen eindeutigen Namen für diese Konfiguration.';
$string['mumie_form_server_btn_submit'] = 'Speichern';
$string['mumie_form_server_btn_cancel'] = 'Abbrechen';
$string['mumie_url_prefix'] = 'URL-Prefix';
$string['mumie_url_prefix_help'] = 'Bitte geben Sie die URL des MUMIE-Servers ein.  <br><br> z.B. <b>https://www.ombplus.de/ombplus</b> <br><br> Für jede URL kann nur ein Server konfiguriert werden.';

// Capabilities.
$string['mumie:addserver'] = 'MUMIE-Server zu MOODLE hinzufügen';
$string['mumie:deleteserver'] = 'MUMIE-Server löschen';
$string['mumie:viewavailablecourses'] = 'Alle MUMIE-Server, -Courses und -Tasks sehen, die auf diesem MOODLE verfügbar sind';