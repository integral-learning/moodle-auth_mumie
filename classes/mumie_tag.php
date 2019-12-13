<?php
/**
 * MumieTask plugin
 *
 * @copyright   2019 integral-learning GmbH (https://www.integral-learning.de/)
 * @author      Tobias Goltz (tobias.goltz@integral-learning.de)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_mumie;

defined('MOODLE_INTERNAL') || die;
require_once ($CFG->dirroot . '/auth/mumie/classes/mumie_problem.php');

class mumie_tag implements \JsonSerializable{
    private $name;
    private $values = array();

    public function __construct($name, $values) {
        $this->name = $name;
        $this->values = $values;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
    }

    
    public function merge($tag) {
        $mergedTag = new mumie_tag($this->name, $this->values);
        if($tag instanceof mumie_tag && $tag->name == $mergedTag->name) {
            array_push($mergedTag->values, ...$tag->values);
            $mergedTag->values = array_values(array_unique($mergedTag->values));
        }

        return $mergedTag;
    }

    public function get_name(){
        return $this->name;
    }

}