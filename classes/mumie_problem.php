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
 * This class represents a MUMIE problem in moodle
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mumie;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/auth/mumie/classes/mumie_tag.php');

/**
 * This class represents a MUMIE problem in moodle.
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumie_problem implements \JsonSerializable {
    /**
     * Link to the resource on the MUMIE server
     * @var string
     */
    private $link;
    /**
     * Headlines for all available languages
     * @var stdClass[]
     */
    private $headline;
    /**
     * All languages this task is available in
     * @var string[]
     */
    private $languages = array();
    /**
     * All content tags set for this problem
     * @var mumie_tag[]
     */
    private $tags = array();

    /**
     * Get headlines for all available languages
     * @return stcClass[]
     */
    public function get_headline() {
        return $this->headline;
    }

    /**
     * Set the value of headline
     * @param stdClass[] $headline
     * @return  self
     */
    public function set_headline($headline) {
        $this->headline = $headline;

        return $this;
    }

    /**
     * Constructor
     * @param stdClass $task
     */
    public function __construct($task) {
        $this->link = $task->link;
        $this->headline = $task->headline;
        if (isset($task->tags)) {
            foreach ($task->tags as $tag) {
                array_push($this->tags, new mumie_tag($tag->name, $tag->values));
            }
        }
        $this->collect_languages();
    }

    /**
     * Create a mumie_problem from a MUMIE Task instance
     * @param stdClass $data the instance of MUMIE Task
     * @return mumie_problem
     */
    public static function from_task_db_object($data) {
        $task = new \stdClass;
        $task->link = \mod_mumie\locallib::remove_params_from_url($data->taskurl);
        $headline = [((object)["language" => $data->language, "name" => $data->name])];
        $task->headline = $headline;
        return new mumie_problem($task);
    }

    /**
     * Collect and set all languages this problem is available in
     */
    public function collect_languages() {
        if ($this->headline) {
            foreach ($this->headline as $langitem) {
                array_push($this->languages, $langitem->language);
            }
        }
    }
    /**
     * Get the value of link
     * @return string
     */
    public function get_link() {
        return $this->link;
    }

    /**
     * Set the value of link
     * @param string $link
     * @return  self
     */
    public function set_link($link) {
        $this->link = $link;

        return $this;
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
     * Get the value of tags
     * @return mumie_tag[]
     */
    public function get_tags() {
        return $this->tags;
    }

    /**
     * Get the headline for a given language
     * @param string $language
     * @return string
     */
    public function get_headline_by_language($language) {
        foreach ($this->headline as $localheadline) {
            if ($localheadline->language == $language) {
                return $localheadline->name;
            }
        }
    }
}
