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

$string['admin_gradepool_description'] = 'Legen Sie fest, ob Noten für MUMIE-Aufgaben zwischen Kursen geteilt werden sollen. Sie können diese Entscheidung auch den Dozierenden überlassen.
<br>
<br>Falls das Teilen aktiviert ist, werden Punkte, die für das Bearbeiten von MUMIE-Aufgaben in einem MOODLE-Kurse vergeben wurden, auch in anderen Kursen übernommen.
<br>Falls diese Option deaktiviert ist, werden keine Punkte aus anderen MOODLE-Kursen übernommen.
<br><br><b>Änderungen an dieser Einstellungen haben keinen Einfluss auf bereits existierende MUMIE Tasks!</b>
';
$string['admin_gradepool_free_choice_option'] = 'Dozierende entscheiden selbst';
$string['admin_gradepool_private_option'] = 'Punkte nicht teilen';
$string['admin_gradepool_selection'] = 'Teilen von Punkte zwischen Kursen';
$string['admin_gradepool_shared_option'] = 'Punkte teilen';

$string['cachedef_mumieidhash'] = 'Cache für das mumie_id_hash für ein Hash';

$string['mumie:addserver'] = 'MUMIE-Server zu MOODLE hinzufügen';
$string['mumie:deleteserver'] = 'MUMIE-Server löschen';
$string['mumie:ssotoproblemselector'] = 'Problem selector mit SSO öffnen';
$string['mumie:viewavailablecourses'] = 'Alle MUMIE-Server, -Courses und -Tasks sehen, die auf diesem MOODLE verfügbar sind';
$string['mumie_add_server_button'] = 'MUMIE-Server hinzufügen';
$string['mumie_api_key'] = 'MUMIE API Key';
$string['mumie_api_key_desc'] = 'Geben Sie den MUMIE API-Key zur Notensynchronisation an';
$string['mumie_authentication_header'] = 'Authentifizierung & Autorisierung';
$string['mumie_authentication_header_desc'] = 'Diese Einstellungen werden zum Authentifizieren von Nutzern und Autorisieren von Requests benutzt';
$string['mumie_course_account'] = 'Individueller MUMIE-Account für Moodle-Kurs';
$string['mumie_delete_button'] = 'Löschen';
$string['mumie_dev_options_description'] = 'Diese Einstellungen sind nur für die Entwicklung gedacht. Bitte nehmen Sie keine Änderungen daran vor!';
$string['mumie_dev_options_heading'] = 'Entwickleroptionen';
$string['mumie_edit_button'] = 'Bearbeiten';
$string['mumie_firstname'] = 'Vorname';
$string['mumie_form_already_existing_config'] = 'Es gibt bereits eine Serverkonfiguration für diesen URL-Prefix';
$string['mumie_form_already_existing_name'] = 'Es gibt bereits eine Serverkonfiguration für diesen Namen';
$string['mumie_form_required'] = 'Pflichtfeld';
$string['mumie_form_server_btn_cancel'] = 'Abbrechen';
$string['mumie_form_server_btn_submit'] = 'Speichern';
$string['mumie_form_server_config'] = 'MUMIE-Serverkonfiguration';
$string['mumie_form_server_not_existing'] = 'Für diese URL existiert kein MUMIE-Server';
$string['mumie_form_title'] = 'MUMIE-Serverkonfiguration';
$string['mumie_lastname'] = 'Nachname';
$string['mumie_mail'] = 'Email-Addresse';
$string['mumie_org'] = 'MUMIE Org';
$string['mumie_org_desc'] = 'Geben Sie Ihr Organisationskürzel für MUMIE an, z.B. "rwth"';
$string['mumie_problem_selector_url'] = 'Problem-Selector-URL';
$string['mumie_problem_selector_url_description'] = 'Die URL zur Lemon-Instanz, mit der Aufgaben ausgewählt werdedn sollen';
$string['mumie_server_list_heading'] = 'Konfigurierte MUMIE-Server';
$string['mumie_server_name'] = 'Servername';
$string['mumie_server_name_help'] = 'Bitte wählen Sie einen eindeutigen Namen für diese Konfiguration.';
$string['mumie_shared_user_data'] = 'Nutzerdaten';
$string['mumie_shared_user_data_desc'] = 'Bestimmen Sie, welche Nutzerdaten zur Verfügung gestellt werden sollen';
$string['mumie_sso_tokens'] = 'Login-Daten für Single-Sign-On';
$string['mumie_table_header_name'] = 'Servername';
$string['mumie_table_header_url'] = 'URL-Prefix';
$string['mumie_task_admin_header'] = 'MUMIE-Task-Einstellungen';
$string['mumie_task_admin_header_desc'] = 'Diese Einstellungen gelten für das MUMIE-Task-Plugin';
$string['mumie_url_prefix'] = 'URL-Prefix';
$string['mumie_url_prefix_help'] = 'Bitte geben Sie die URL des MUMIE-Servers ein.  <br><br> z.B. <b>https://www.ombplus.de/ombplus</b> <br><br> Für jede URL kann nur ein Server konfiguriert werden.';

$string['pluginname'] = 'MUMIE Single-Sign-On';
$string['privacy:metadata:auth_mumie_hashes:tableexplanation'] = 'Details zur Lookup Table für gehashte Moodle-User-IDs';
$string['privacy:metadata:auth_mumie_hashes:userid'] = 'Moodle-Nutzer-ID';
$string['privacy:metadata:auth_mumie_servers:email'] = 'E-Mail-Adrese des Nutzers';
$string['privacy:metadata:auth_mumie_servers:firstname'] = 'Vorname des Nutzers';
$string['privacy:metadata:auth_mumie_servers:lastname'] = 'Nachname des Nutzers';
$string['privacy:metadata:auth_mumie_servers:tableexplanation'] = 'Details zum optionalen Teilen von persönlichen Daten mit MUMIE/Lemon-Servern';
$string['privacy:metadata:auth_mumie_tokens:hash'] = 'Gehashte Moodle-User-Id mit der Nutzer auf MUMIE/Lemon-Servern identifiziert wird.';
$string['privacy:metadata:auth_mumie_tokens:tableexplanation'] = 'Details zu den SSO-Tokens, die zum Login auf MUMIE/Lemon-Servern verwendet werdem.';
$string['privacy:metadata:auth_mumie_tokens:timecreated'] = 'Zeitpunkt des letzten Single-Sign-On-Versuchs';
$string['privacy:metadata:auth_mumie_tokens:token'] = 'Tokens zur Verifizierung von Single-Sign-On-Versuchen zu MUMIE/Lemon-Server.';
