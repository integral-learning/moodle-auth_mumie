<?php
namespace auth_mumie;

defined('MOODLE_INTERNAL') || die;
global $CFG;


require_once ($CFG->dirroot . '/auth/mumie/classes/mumie_course.php');
class mumie_server implements \JsonSerializable {

    const MUMIE_SERVER_TABLE_NAME = "auth_mumie_servers";
    private $id;
    private $url_prefix;
    private $name;

    private $courses;
    private $languages = array();

    public static function from_object($record) {
        $server = new mumie_server();
        $server->set_url_prefix($record->url_prefix);
        $server->set_name($record->name);
        if($record->id != 0) {
            $server->set_id($record->id);
        }
        return $server;
    }

    public function create() {
        global $DB;

        $DB->insert_record(MUMIE_SERVER_TABLE_NAME, ["url_prefix" => $this->url_prefix, "name" => $this->name]);
    }

    public function update() {
        global $DB;

        $DB->update_record(MUMIE_SERVER_TABLE_NAME, ["url_prefix" => $this->url_prefix, "name" => $this->name, "id"=>$this->id]);
    }

    public function delete() {
        global $DB;

        $DB->delete_records(MUMIE_SERVER_TABLE_NAME, array("id" => $this->id));
    }
    
    public function upsert() {
        if (isset($this->id) && $this->id > 0) {
            $this->update();
        } else {
            $this->create();
        }

    }

    public function load() {
        global $DB;
        $record = (object) $DB->get_record(MUMIE_SERVER_TABLE_NAME, ["url_prefix" => $this->url_prefix, "name" => $this->name, "id"=>$this->id]);
        if(!isset($record->id)) {
            return;
        }
        $this->set_id($record->id);
        $this->set_url_prefix($record->url_prefix);
        $this->set_name($record->name);
    }

    public static function get_all_servers() {
        global $DB;

        return array_map('self::from_object', $DB->get_records(MUMIE_SERVER_TABLE_NAME));
    }

    public static function get_all_servers_with_structure() {
        $servers = array();
        foreach(self::get_all_servers() as $server) {
            $server->load_structure();
            array_push($servers, $server);
        }
        return $servers;
    }

    public static function delete_by_id($id) {
        $server = new mumie_server();
        $server->set_id($id);
        $server->delete();
    }

    public static function get_by_url_prefix($url_prefix) {
        global $DB;
        $record = $DB->get_record(MUMIE_SERVER_TABLE_NAME, ["url_prefix" => $url_prefix]);
        return self::from_object($record);
    }

    public static function get_by_name($name) {
        global $DB;
        $record = $DB->get_record(MUMIE_SERVER_TABLE_NAME, ["name" => $name]);
        return self::from_object($record);
    }
   
    public function get_logout_url() {
        return $this->url_prefix . "public/xapi/auth/sso/logout/" . get_config('auth_mumie', 'mumie_org');
    }

    public function get_courses_and_tasks(){
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->url_prefix . "public/courses-and-tasks",
            CURLOPT_USERAGENT => 'Codular Sample cURL Request',
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }

    public function is_valid_mumie_server() {
        return $this->get_courses_and_tasks()->courses != null;
    }

    /**
     * Get the value of courses
     */
    public function get_courses()
    {
        return $this->courses;
    }

    /**
     * Set the value of courses
     *
     * @return  self
     */
    public function set_courses($courses)
    {
        $this->courses = $courses;

        return $this;
    }


    protected function load_structure()
    {
        $coursesandtasks = $this->get_courses_and_tasks();
        $this->courses = [];
        if ($coursesandtasks) {
            foreach ($coursesandtasks->courses as $course) {
                array_push($this->courses, new mumie_course($course));
            }
        }
        $this->collect_languages();
    }

    private function collect_languages()
    {
        $langs = [];
        foreach ($this->courses as $course) {
            array_push($langs, ...$course->get_languages());
        }
        $this->languages = array_values(array_unique($langs));
    }


    public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
    }

    /**
     * Get the value of languages
     */
    public function get_languages()
    {
        return $this->languages;
    }

    /**
     * Set the value of languages
     *
     * @return  self
     */
    public function set_languages($languages)
    {
        $this->languages = $languages;

        return $this;
    }


    public function get_course_by_Name($name)
    {
        foreach ($this->courses as $course) {
            if ($course->get_name() == $name) {
                return $course;
            }
        }
    }

    public function get_url_prefix() {
        return $this->url_prefix;
    }

    public function set_url_prefix($url_prefix) {
        $url_prefix = (substr($url_prefix, -1) == '/' ? $url_prefix : $url_prefix . '/');
        $this->url_prefix = $url_prefix;
        return $this;
    }

    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        $this->name = $name;
        return $this;
    }

    public function get_id() {
        return $this->id;
    }

    public function set_id($id) {
        $this->id = $id;
        return $this;
    }
}
