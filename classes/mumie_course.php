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
require_once ($CFG->dirroot .'/auth/mumie/classes/mumie_tag.php');

class mumie_course implements \JsonSerializable
{
    private $name;
    private $tasks;
    private $coursefile;
    private $languages = array();
    private $tags = array();

    /**
     * Get the value of coursefile
     */
    public function get_coursefile()
    {
        return $this->coursefile;
    }

    /**
     * Set the value of coursefile
     *
     * @return  self
     */
    public function set_coursefile($coursefile)
    {
        $this->coursefile = $coursefile;

        return $this;
    }

    /**
     * Get the value of tasks
     */
    public function get_tasks()
    {
        return $this->tasks;
    }

    /**
     * Set the value of tasks
     *
     * @return  self
     */
    public function set_tasks($tasks)
    {
        $this->tasks = $tasks;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function set_name($name)
    {
        $this->name = $name;

        return $this;
    }

    public function __construct($coursewithtasks)
    {
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

    public function collect_languages()
    {
        $langs = [];
        foreach ($this->tasks as $task) {
            array_push($langs, ...$task->get_languages());
        }
        $this->languages = array_values(array_unique($langs));
    }

    public function collect_tags()
    {
        $tags = array();
        foreach ($this->tasks as $task) {
            foreach ($task->get_tags() as $tag) {
                if(!isset($tags[$tag->get_name()])) {
                    $tags[$tag->get_name()] = array();
                }
                $tags[$tag->get_name()] = $tag->merge($tags[$tag->get_name()]);
            }
        }

        $this->tags = array_values($tags);
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

    public function get_task_by_link($link)
    {
        foreach ($this->tasks as $task) {
            if ($task->get_link() == $link) {
                return $task;
            }
        }
    }
}
