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
 * External auth_mumie API
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . '/auth/mumie/classes/mumie_server.php');

/**
 * External auth_mumie API
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_mumie_external extends external_api {

    /**
     * Describes the parameters for submit_mumieserver_form webservice.
     * @return external_function_parameters
     */
    public static function submit_mumieserver_form_parameters() {
        return new external_function_parameters(
            array(
                'contextid' => new external_value(PARAM_INT, 'The context id for action'),
                'jsonformdata' => new external_value(PARAM_RAW, 'The data from the mumie server form, encoded as a json array'),
            )
        );
    }

    /**
     * Submit the mumieserver form.
     *
     * @param int $contextid The context id for the course.
     * @param string $jsonformdata The data from the form, encoded as a json array.
     * @return int new mumieserver id.
     */
    public static function submit_mumieserver_form($contextid, $jsonformdata) {
        global $CFG, $USER, $DB;

        require_once($CFG->dirroot . '/auth/mumie/lib.php');
        require_once($CFG->dirroot . '/auth/mumie/classes/mumie_server.php');
        require_once($CFG->dirroot . '/auth/mumie/mumieserver_form.php');

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(
            self::submit_mumieserver_form_parameters(),
            ['contextid' => $contextid, 'jsonformdata' => $jsonformdata]
        );

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);

        // We always must call validate_context in a webservice.
        self::validate_context($context);
        require_capability('auth/mumie:addserver', $context);

        $serialiseddata = json_decode($params['jsonformdata']);

        $data = array();
        parse_str($serialiseddata, $data);

        $editoroptions = [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'trust' => false,
            'context' => $context,
            'noclean' => true,
            'subdirs' => false,
        ];
        $mumieserver = new stdClass();
        $mumieserver = file_prepare_standard_editor(
            $mumieserver,
            'description',
            $editoroptions,
            $context,
            'mumie',
            'description',
            null
        );

        // The last param is the ajax submitted data.
        $mform = new mumieserver_form(null, array('editoroptions' => $editoroptions), 'post', '', null, true, $data);

        $validateddata = $mform->get_data();
        if ($validateddata) {
            $server = auth_mumie\mumie_server::from_object((object) $validateddata);
            $server->upsert();
        } else {
            // Generate a warning.
            throw new moodle_exception('erroreditgroup', 'mumieserver');
        }

        return $server->get_id();
    }

    /**
     * Describes the parameters for submit_mumieserver_form webservice.
     * @return external_function_parameters
     */
    public static function submit_mumieserver_form_returns() {
        return new external_value(PARAM_INT, 'server id');
    }

    /**
     * Describes the parameters for get_server_structure webservice.
     * @return external_function_parameters
     */
    public static function get_server_structure_parameters() {
        return new external_function_parameters(
            array(
                "contextid" => new external_value(PARAM_INT, 'The context id for the requesting course'),
            )
        );
    }

    /**
     * Webservice to get a list of all available MUMIE courses, servers and tasks
     * @param int $contextid The context id for the course.
     */
    public static function get_server_structure($contextid) {
        global $CFG;

        $params = self::validate_parameters(
            self::get_server_structure_parameters(),
            ['contextid' => $contextid]
        );

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);
        self::validate_context($context);
        require_capability('auth/mumie:viewavailablecourses', $context);

        return json_encode(auth_mumie\mumie_server::get_all_servers_with_structure());
    }

    /**
     * Describes the parameters for get_server_structure webservice.
     * @return external_function_parameters
     */
    public static function get_server_structure_returns() {
        return new external_value(PARAM_RAW, "servers");
    }
}
