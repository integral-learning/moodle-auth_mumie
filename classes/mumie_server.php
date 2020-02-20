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
 * This class represents a MUMIE Server in moodle
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_mumie;

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once($CFG->dirroot . '/auth/mumie/classes/mumie_course.php');

/**
 * This class represents a MUMIE Server in moodle
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumie_server implements \JsonSerializable {
    /**
     * The db table name for MUMIE server configurations
     */
    const MUMIE_SERVER_TABLE_NAME = "auth_mumie_servers";

    /**
     * This is used as parameter when requesting available courses and tasks.
     */
    const MUMIE_JSON_FORMAT_VERSION = 2;

    /**
     * Primary key for db entry
     * @var int
     */
    private $id;
    /**
     * Server URL
     * @var string
     */
    private $urlprefix;
    /**
     * A human-readable name for a server configuration
     * @var string
     */
    private $name;

    /**
     * All courses that are available on the server
     * @var mumie_course[]
     */
    private $courses;
    /**
     * All languages that are available on the server
     * @var string[]
     */
    private $languages = array();

    /**
     * Create an instance of mumie_server from a database record
     * @param stdClass $record
     * @return mumie_server
     */
    public static function from_object($record) {
        $server = new mumie_server();
        $server->set_urlprefix($record->url_prefix);
        $server->set_name($record->name);
        if ($record->id != 0) {
            $server->set_id($record->id);
        }
        return $server;
    }

    /**
     * Create a database entry for this MUMIE server
     */
    private function create() {
        global $DB;
        $DB->insert_record(self::MUMIE_SERVER_TABLE_NAME, ["url_prefix" => $this->urlprefix, "name" => $this->name]);
    }

    /**
     * Update the database entry for this MUMIE server
     */
    public function update() {
        global $DB;
        $DB->update_record(
            self::MUMIE_SERVER_TABLE_NAME,
            ["url_prefix" => $this->urlprefix, "name" => $this->name, "id" => $this->id]
        );
    }

    /**
     * Delete this MUMIE server from the database
     */
    public function delete() {
        global $DB;
        $DB->delete_records(self::MUMIE_SERVER_TABLE_NAME, array("id" => $this->id));
    }

    /**
     * Create or update a database entry for this MUMIE server
     */
    public function upsert() {
        if (isset($this->id) && $this->id > 0) {
            $this->update();
        } else {
            $this->create();
        }
    }

    /**
     * Get all MUMIE server configurations
     *
     * @return mumie_server[]
     */
    public static function get_all_servers() {
        global $DB;
        return array_map('self::from_object', $DB->get_records(self::MUMIE_SERVER_TABLE_NAME));
    }

    /**
     * Get all MUMIE servers including their course structure
     * @return mumie_server[]
     */
    public static function get_all_servers_with_structure() {
        $servers = array();
        foreach (self::get_all_servers() as $server) {
            $server->load_structure();
            array_push($servers, $server);
        }
        return $servers;
    }

    /**
     * Get logout urls for all configured MUMIE servers
     *
     * @return string[] Logout urls for all MUMIE servers
     */
    public static function get_all_logout_urls() {
        return array_map(function ($server) {
            return $server->get_logout_url();
        }, self::get_all_servers());
    }

    /**
     * Delete a MUMIE server
     * @param int $id
     */
    public static function delete_by_id($id) {
        $server = new mumie_server();
        $server->set_id($id);
        $server->delete();
    }

    /**
     * Get a MUMIE server configuration by its URL
     * @param string $urlprefix
     * @return mumie_server
     */
    public static function get_by_urlprefix($urlprefix) {
        global $DB;
        $server = new mumie_server();
        $server->set_urlprefix($urlprefix);
        $record = $DB->get_record(self::MUMIE_SERVER_TABLE_NAME, ["url_prefix" => $server->get_urlprefix()]);
        return self::from_object($record);
    }

    /**
     * Get a MUMIE server configuration by its name
     * @param string $name
     * @return mumie_server
     */
    public static function get_by_name($name) {
        global $DB;
        $record = $DB->get_record(self::MUMIE_SERVER_TABLE_NAME, ["name" => $name]);
        return self::from_object($record);
    }

    /**
     * Get the logout URL for this MUMIE server
     * @return string
     */
    public function get_logout_url() {
        return $this->urlprefix . "public/xapi/auth/sso/logout/" . get_config('auth_mumie', 'mumie_org');
    }

    /**
     * Get URL for XAPI grades
     */
    public function get_grade_sync_url() {
        return $this->urlprefix. '/public/xapi?v=' . self::MUMIE_JSON_FORMAT_VERSION;
    }

    /**
     * Get the latest course structure form the MUMIE server
     * @return stdClass server response
     */
    public function get_courses_and_tasks() {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->urlprefix . "public/courses-and-tasks?v=" . self::MUMIE_JSON_FORMAT_VERSION,
            CURLOPT_USERAGENT => 'Codular Sample cURL Request',
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    /**
     * Check if this URL actually belongs to a MUMIE server
     * @return bool
     */
    public function is_valid_mumie_server() {
        return $this->get_courses_and_tasks()->courses != null;
    }

    /**
     * Check if there already is a configuration for the given url prefix in the database.
     * @return bool
     */
    public function config_exists_for_url() {
        $config = self::get_by_urlprefix($this->urlprefix);
        return $config->get_id() != null;
    }

    /**
     * Get the value of courses
     * @return mumie_course[]
     */
    public function get_courses() {
        return $this->courses;
    }

    /**
     * Set the value of courses
     *
     * @param mumie_courses[] $courses
     * @return  self
     */
    public function set_courses($courses) {
        $this->courses = $courses;

        return $this;
    }

    /**
     * Loead and set the latest tasks and courses from the MUMIE server
     */
    public function load_structure() {
        $coursesandtasks = $this->get_courses_and_tasks();
        $this->courses = [];
        if ($coursesandtasks) {
            foreach ($coursesandtasks->courses as $course) {
                array_push($this->courses, new mumie_course($course));
            }
        }
        $this->collect_languages();
    }

    /**
     * Collect and set a list of all languages that are available on this MUMIE server
     */
    private function collect_languages() {
        $langs = [];
        foreach ($this->courses as $course) {
            array_push($langs, ...$course->get_languages());
        }
        $this->languages = array_values(array_unique($langs));
    }

    /**
     * Necessary to encode this object as json.
     * @return mixed
     */
    public function jsonSerialize() {
        $vars = get_object_vars($this);

        return $vars;
    }

    /**
     * Find a course on this server by its coursefile
     * @param string $coursefile
     * @return mumie_couse
     */
    public function get_course_by_coursefile($coursefile) {
        foreach ($this->courses as $course) {
            if ($course->get_coursefile() == $coursefile) {
                return $course;
            }
        }
    }

    /**
     * Add a MUMIE problem to the server-course-problem structure.
     * @param stdClass an instance of MUMIE Task
     */
    public function add_custom_problem_to_structure($task) {
        $this->get_course_by_coursefile($task->mumie_coursefile)->add_custom_problem_to_structure($task);
        $this->collect_languages();
    }

    /**
     * Get all languages that are available on the MUMIE server
     * @return string[]
     */
    public function get_languages() {
        return $this->languages;
    }

    /**
     * Set the value of languages
     * @param string[] $languages
     * @return  self
     */
    public function set_languages($languages) {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get the URL of the MUMIE server
     * @return string
     */
    public function get_urlprefix() {
        return $this->urlprefix;
    }

    /**
     * Set URL prefix of the MUMIE server
     * @param string $urlprefix
     * @return self
     */
    public function set_urlprefix($urlprefix) {
        $urlprefix = (substr($urlprefix, -1) == '/' ? $urlprefix : $urlprefix . '/');
        $this->urlprefix = $urlprefix;
        return $this;
    }

    /**
     * Get the human-readable name for this MUMIE server configuration
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the human-readable name for this MUMIE server configuration
     * @param string $name
     * @return self
     */
    public function set_name($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the primary key for this MUMIE server configuration
     * @return int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Set the primary key for this MUMIE server configuration
     * @param int $id
     * @return self
     */
    public function set_id($id) {
        $this->id = $id;
        return $this;
    }
}
