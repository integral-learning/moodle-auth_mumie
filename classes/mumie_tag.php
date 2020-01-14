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
 * mumie_tags are used to classify MUMIE Tasks
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_mumie;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/auth/mumie/classes/mumie_problem.php');

/**
 * mumie_tags are used to classify MUMIE Tasks
 *
 * @package auth_mumie
 * @copyright  2017-2020 integral-learning GmbH (https://www.integral-learning.de/)
 * @author Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mumie_tag implements \JsonSerializable {
    /**
     * Name of the tag
     * @var string
     */
    private $name;
    /**
     * All values for the tag
     * @var string[]
     */
    private $values = array();

    /**
     * Constructor
     * @param string $name
     * @param string[] $values
     */
    public function __construct($name, $values) {
        $this->name = $name;
        $this->values = $values;
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
     * Merge this tag with another into a new one
     * @param mumie_tag $tag the tag to merge with
     * @return mumie_tag the new tag
     */
    public function merge($tag) {
        $mergedtag = new mumie_tag($this->name, $this->values);
        if ($tag instanceof mumie_tag && $tag->name == $mergedtag->name) {
            array_push($mergedtag->values, ...$tag->values);
            $mergedtag->values = array_values(array_unique($mergedtag->values));
        }

        return $mergedtag;
    }

    /**
     * Get the name of this tag
     * @return string
     */
    public function get_name() {
        return $this->name;
    }
}
