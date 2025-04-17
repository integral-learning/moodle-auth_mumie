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
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/auth/mumie/classes/mumie_server.php');

/**
 * This moodle form is used to insert or update MumieServer in the database
 *
 * @package auth_mumie
 * @copyright  2017-2020
 * @author     Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumieserver_form extends moodleform {

    /**
     * Define fields and default values for the mumie server form
     * @return void
     */
    public function definition() {
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
    * If there are errors return array of errors ("fieldname"=>"error message"), otherwise true if ok.
    * Server side rules do not work for uploaded files, implement serverside rules here if needed.
    * @param array $data — form data
    * @param array $files — array of uploaded files "element_name"=>tmp_file_path
    * @return array — associative array of errors
    */
    public function validation($data, $files) {
        $errors = [];
        $server = auth_mumie\mumie_server::from_object((object) $data);

        // Check if server is valid.
        if (!$server->is_valid_mumie_server()) {
            $errors['url_prefix'] = get_string('mumie_form_server_not_existing', 'auth_mumie');
        }

        $serverbyprefix = auth_mumie\mumie_server::get_by_urlprefix($data["url_prefix"]);
        $serverbyname = auth_mumie\mumie_server::get_by_name($data["name"]);
        $id = $data['id'] ?? 0;

        // Name required.
        if (empty($data['name'])) {
            $errors['name'] = get_string('mumie_form_required', 'auth_mumie');
        }

        // Name must be unique.
        if ($serverbyname->get_id() !== null && $serverbyname->get_id() != $id) {
            $errors['name'] = get_string('mumie_form_already_existing_name', 'auth_mumie');
        }

        // URL prefix required.
        if (empty($data['url_prefix'])) {
            $errors['url_prefix'] = get_string('mumie_form_required', 'auth_mumie');
        }

        // URL prefix must be unique.
        if ($serverbyprefix->get_id() !== null && $serverbyprefix->get_id() != $id) {
            $errors['url_prefix'] = get_string('mumie_form_already_existing_config', 'auth_mumie');
        }

        return $errors;
    }
}
