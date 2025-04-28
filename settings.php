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
 * This file defines the global auth_mumie settings page.
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/auth/mumie/classes/mumie_server.php');

global $DB, $PAGE;

$mumieservers = auth_mumie\mumie_server::get_all_servers();

// Build html table containing all saved mumie servers.
$table = new html_table();
$table->attributes['class'] = 'generaltable auth_index mumie_server_list_container';
$table->head = [get_string("mumie_table_header_name", "auth_mumie"), get_string("mumie_table_header_url", "auth_mumie"),
    get_string("mumie_edit_button", "auth_mumie"), get_string("mumie_delete_button", "auth_mumie")];

foreach ($mumieservers as $server) {
    $id = "<span class='mumie_list_entry_id' hidden>" . $server->get_id() . "</span>";
    $name = "<span class='mumie_list_entry_name'>" . $server->get_name() . "</span>" . $id;
    $url = "<span class='mumie_list_entry_url'>" . $server->get_urlprefix() . "</span>";
    $edit = "<a class = 'mumie_list_edit_button' title='" . get_string("mumie_edit_button", "auth_mumie") . "'>"
        . '<span class="icon fa fa-cog fa-fw " aria-hidden="true" aria-label=""></span>'
        . "</a>";
    $deleteurl = "{$CFG->wwwroot}/auth/mumie/deletemumieserver.php?id={$server->get_id()}&amp;sesskey={$USER->sesskey}";
    $delete = "<a class = 'mumie_list_delete_button' href='{$deleteurl}' title='"
    . get_string("mumie_delete_button", "auth_mumie")
        . "'>"
        . '<span class="icon fa fa-trash fa-fw " aria-hidden="true"></span>'
        . "</a>";
    $table->data[] = [$name, $url, $edit, $delete];
}

$addbutton = "<button class='btn mumie_add_server_button btn-primary' id='mumie_add_server_button'>"
. '<span class="icon fa fa-plus fa-fw " aria-hidden="true" aria-label=""></span>'
. get_string("mumie_add_server_button", "auth_mumie")
    . "</button>";

if ($ADMIN->fulltree) {
    // Header for authentication settings.
    $settings->add(new admin_setting_heading(
        'authorization',
        get_string('mumie_authentication_header', 'auth_mumie'),
        get_string('mumie_authentication_header_desc', 'auth_mumie')
    ));

    $settings->add(new admin_setting_configtext(
        'auth_mumie/mumie_api_key',
        get_string('mumie_api_key', 'auth_mumie'),
        get_string('mumie_api_key_desc', 'auth_mumie'),
        '',
        PARAM_ALPHANUM
    ));

    $settings->add(new admin_setting_configtext(
        'auth_mumie/mumie_org',
        get_string('mumie_org', 'auth_mumie'),
        get_string('mumie_org_desc', 'auth_mumie'),
        '',
        PARAM_ALPHANUMEXT
    ));

    // Header for shared user data.
    $settings->add(new admin_setting_heading(
        'userdata',
        get_string('mumie_shared_user_data', 'auth_mumie'),
        get_string('mumie_shared_user_data_desc', 'auth_mumie')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'auth_mumie/userdata_firstname',
        get_string('mumie_firstname', 'auth_mumie'),
        '',
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'auth_mumie/userdata_lastname',
        get_string('mumie_lastname', 'auth_mumie'),
        '',
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'auth_mumie/userdata_mail',
        get_string('mumie_mail', 'auth_mumie'),
        '',
        0
    ));

    // Header for MumieTask settings.
    $settings->add(new admin_setting_heading(
        'mumietask',
        get_string('mumie_task_admin_header', 'auth_mumie'),
        get_string('mumie_task_admin_header_desc', 'auth_mumie')
    ));

    $gradepooloptions = [
        -1 => get_string('admin_gradepool_free_choice_option', 'auth_mumie'),
        1 => get_string('admin_gradepool_private_option', 'auth_mumie'),
        0 => get_string('admin_gradepool_shared_option', 'auth_mumie'),
    ];

    $settings->add(
        new admin_setting_configselect(
            'auth_mumie/defaultgradepool',
            get_string('admin_gradepool_selection', 'auth_mumie'),
            get_string('admin_gradepool_description', 'auth_mumie'),
            -1,
            $gradepooloptions
        )
    );

    $settings->add(
        new admin_setting_heading(
            'mumie_servers',
            get_string("mumie_server_list_heading", "auth_mumie"),
            html_writer::table($table) . $addbutton
        )
    );

    $settings->add(
        new admin_setting_heading(
            'mumie_dev_options',
            get_string("mumie_dev_options_heading", "auth_mumie"),
            get_string("mumie_dev_options_description", "auth_mumie")
        )
    );

    $settings->add(new admin_setting_configtext(
        'auth_mumie/mumie_problem_selector_url',
        get_string('mumie_problem_selector_url', 'auth_mumie'),
        get_string('mumie_problem_selector_url_description', 'auth_mumie'),
        'https://pool.mumie.net',
        PARAM_URL
    ));

    $context = context_system::instance();
    $PAGE->requires->js_call_amd('auth_mumie/settings', 'init', [json_encode($context->id)]);
}
