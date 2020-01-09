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
 * This moodle form is used to insert or update MumieServer in the database
 *
 * @package auth_mumie
 * @copyright  2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->dirroot . '/auth/mumie/classes/mumie_server.php');

/**
 * This moodle form is used to insert or update MumieServer in the database
 *
 * @package auth_mumie
 * @copyright  2018
 * @author     Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumieserver_form extends moodleform {

    /**
     * Define fields and default values for the mumie server form
     * @return void
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('mumie_server_name', 'auth_mumie'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addHelpButton("name", 'mumie_server_name', 'auth_mumie');

        $mform->addElement('text', 'url_prefix', get_string('mumie_url_prefix', 'auth_mumie'));
        $mform->setType('url_prefix', PARAM_NOTAGS);
        $mform->addHelpButton("url_prefix", 'mumie_url_prefix', 'auth_mumie');

        $mform->addElement('hidden', "id", 0);
        $mform->setType("id", PARAM_INT);
        $mform->setDefault("id", 0);

        $mform->addElement('hidden', 'notifyParent', false);
        $mform->setType("notifyParent", PARAM_BOOL);
    }

    /**
     * Valdiate the form data
     * @param array $data form data
     * @param array $files files uploaded
     * @return array associative array of errors
     */
    public function validation($data, $files) {
        global $DB;
        $errors = array();
        $server = auth_mumie\mumie_server::from_object((object) $data);
        if (!$server->is_valid_mumie_server()) {
            $errors["url_prefix"] = get_string('mumie_form_server_not_existing', 'auth_mumie');
        }

        // Array containing all servers with the given url_prefix.
        $serverbyprefix = auth_mumie\mumie_server::get_by_urlprefix($data["url_prefix"]);
        $serverbyname = auth_mumie\mumie_server::get_by_name($data["name"]);

        if (strlen($data["name"]) == 0) {
            $errors["name"] = get_string('mumie_form_required', 'auth_mumie');
        }

        if ($serverbyname->get_id() != null && !$data["id"] > 0) {
            $errors["name"] = get_string('mumie_form_already_existing_name', "auth_mumie");
        }
        if ($serverbyname->get_id() != null && $data["id"] > 0 && $serverbyname->get_id() != $data["id"]) {
            $errors["name"] = get_string('mumie_form_already_existing_name', "auth_mumie");
        }

        if (strlen($data["url_prefix"]) == 0) {
            $errors["url_prefix"] = get_string('mumie_form_required', 'auth_mumie');
        }

        /* url_prefix is a unique attribute. If a new server is added (id = default value),
        there mustn't be a server with this property in the database
         */
        if ($serverbyprefix->get_id() != null && !$data["id"] > 0) {
            $errors["url_prefix"] = get_string('mumie_form_already_existing_config', "auth_mumie");
        }

        /* url_prefix is a unique attribute. If an existing server is edited (id>0), make sure,
        that there is no other server(a server with a different id) with the same property in the database
         */
        if ($serverbyprefix->get_id() != null && $data["id"] > 0 && $serverbyprefix->get_id() != $data["id"]) {
            $errors["url_prefix"] = get_string('mumie_form_already_existing_config', "auth_mumie");
        }

        return $errors;
    }
}
