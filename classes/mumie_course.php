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
 * This class represents a MUMIE Course in moodle
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mumie;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/auth/mumie/classes/mumie_problem.php');
require_once($CFG->dirroot . '/auth/mumie/classes/mumie_tag.php');

/**
 * This class represents a MUMIE Course in moodle
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumie_course implements \JsonSerializable {
    /**
     * The course's name
     * @var string
     */
    private $name;
    /**
     * All tasks available on the server
     * @var mumie_problem[]
     */
    private $tasks;
    /**
     * Identifier of the course
     * @var string
     */
    private $coursefile;
    /**
     * All languages that are available in this course
     * @var string[]
     */
    private $languages = array();
    /**
     * All tags set for tasks in this course
     * @var mumie_tag[]
     */
    private $tags = array();

    /**
     * Get the value of coursefile
     * @return string
     */
    public function get_coursefile() {
        return $this->coursefile;
    }

    /**
     * Set the value of coursefile
     * @param string $coursefile
     * @return  self
     */
    public function set_coursefile($coursefile) {
        $this->coursefile = $coursefile;
        return $this;
    }

    /**
     * Get the value of tasks
     * @return mumie_problem[]
     */
    public function get_tasks() {
        return $this->tasks;
    }

    /**
     * Set the value of tasks
     * @param mumie_problem[] $tasks
     * @return  self
     */
    public function set_tasks($tasks) {
        $this->tasks = $tasks;
        return $this;
    }

    /**
     * Get the value of name
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the value of name
     * @param string $name
     * @return  self
     */
    public function set_name($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Constructor
     * @param stdClass $coursewithtasks
     */
    public function __construct($coursewithtasks) {
        $this->name = $coursewithtasks->name;
        $this->coursefile = $coursewithtasks->pathToCourseFile;
        $this->tasks = [];
        if ($coursewithtasks->tasks) {
            foreach ($coursewithtasks->tasks as $task) {
                $taskobj = new mumie_problem($task);
                array_push($this->tasks, $taskobj);
            }
        }
        $this->collect_languages();
        $this->collect_tags();
    }

    /**
     * Collect and set all languages used in this course
     */
    public function collect_languages() {
        $langs = [];
        foreach ($this->tasks as $task) {
            array_push($langs, ...$task->get_languages());
        }
        $this->languages = array_values(array_unique($langs));
    }

    /**
     * Collect and set all tags that are used in this course
     */
    public function collect_tags() {
        $tags = array();
        foreach ($this->tasks as $task) {
            foreach ($task->get_tags() as $tag) {
                if (!isset($tags[$tag->get_name()])) {
                    $tags[$tag->get_name()] = array();
                }
                $tags[$tag->get_name()] = $tag->merge($tags[$tag->get_name()]);
            }
        }

        $this->tags = array_values($tags);
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
     * Get the value of languages
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
     * Find a mumie problem in this course by its link
     * @param string $link
     * @return mumie_problem
     */
    public function get_task_by_link($link) {
        if (strpos($link, "?") !== false) {
            $link = substr($link, 0, strpos($link, "?"));
        }
        foreach ($this->tasks as $task) {
            if ($task->get_link() == $link) {
                return $task;
            }
        }
    }

    /**
     * Add a MUMIE problem to the server-course-problem structure.
     * @param stdClass $task an instance of MUMIE Task
     */
    public function add_custom_problem_to_structure($task) {
        array_push($this->tasks, mumie_problem::from_task_db_object($task));
        $this->collect_languages();
    }
}
